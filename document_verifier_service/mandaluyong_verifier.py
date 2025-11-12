import re
from datetime import datetime, timedelta

# Avoid importing heavy native libraries (cv2/numpy/pytesseract) at module import time so tests
# can import this module without those system deps. We provide lightweight wrappers that will
# attempt to delegate to the on-disk verifier when available, but fall back to safe defaults.


def _default_ocr_extract_text(path):
    # best-effort: try to import the project's verifier OCR; if not available, return empty string
    try:
        from .verifier import ocr_extract_text as _ocr
        return _ocr(path)
    except Exception:
        return ''


def _default_detect_signature(path):
    try:
        from .verifier import detect_signature as _ds
        return _ds(path)
    except Exception:
        return 0.0


def _default_ela_score(path):
    try:
        from .verifier import ela_forgery_score as _es
        return _es(path)
    except Exception:
        return 0.0


# Exported functions which tests can monkeypatch easily
ocr_extract_text = _default_ocr_extract_text
detect_signature = _default_detect_signature
ela_forgery_score = _default_ela_score


def _normalize_text_for_search(text: str) -> str:
    return (text or '').lower()


def extract_dates_from_text(text: str):
    """Return dict with possible issue_date and valid_from/valid_until (strings or None)."""
    candidates = {'issue_date': None, 'valid_from': None, 'valid_until': None}
    # common date patterns
    date_regexes = [
        r"(\d{4}-\d{2}-\d{2})",
        r"(\d{1,2}/\d{1,2}/\d{2,4})",
        r"([A-Za-z]{3,9}\s+\d{1,2},?\s*\d{4})",
    ]
    text_low = text or ''
    # issue date
    m = None
    for pat in [r"issued[:\s]*([A-Za-z0-9,\-/ ]{6,60})", r"date issued[:\s]*([A-Za-z0-9,\-/ ]{6,60})"]:
        mo = re.search(pat, text_low, re.IGNORECASE)
        if mo:
            m = mo.group(1)
            break
    if m:
        candidates['issue_date'] = m.strip()
    # valid until / validity
    mo = re.search(r"(valid until|validity|valid to|validity until)[:\s]*([A-Za-z0-9,\-/ ]{6,60})", text_low, re.IGNORECASE)
    if mo:
        candidates['valid_until'] = mo.group(2).strip()
    # valid from
    mo = re.search(r"(valid from|effective from)[:\s]*([A-Za-z0-9,\-/ ]{6,60})", text_low, re.IGNORECASE)
    if mo:
        candidates['valid_from'] = mo.group(2).strip()

    # fallback: first date-like tokens in text
    if not candidates['issue_date'] or not candidates['valid_until']:
        for rx in date_regexes:
            mo = re.search(rx, text_low)
            if mo:
                if not candidates['issue_date']:
                    candidates['issue_date'] = mo.group(1)
                elif not candidates['valid_until']:
                    candidates['valid_until'] = mo.group(1)
    return candidates


def extract_firm_and_owner(text: str):
    """Heuristic extraction of firm name and owner/proprietor name."""
    text_low = text or ''
    firm = None
    owner = None
    # common labels
    m = re.search(r"(business name|firm name|registered name)[:\s]*([A-Za-z0-9\-\.&()\,\s]{3,200})", text_low, re.IGNORECASE)
    if m:
        firm = m.group(2).strip()
    else:
        # Try lines that are ALL CAPS and relatively long (likely header names)
        for line in (text or '').splitlines():
            if line.strip() and len(line.strip()) > 6 and line.strip() == line.strip().upper() and len(line.strip().split()) >= 2:
                firm = line.strip()
                break

    m2 = re.search(r"(owner|proprietor|proprietor name|owner name|business owner)[:\s]*([A-Za-z0-9\-\.&()\,\s]{3,200})", text_low, re.IGNORECASE)
    if m2:
        owner = m2.group(2).strip()
    else:
        # fallback for person-like caps
        for line in (text or '').splitlines():
            parts = [p for p in line.strip().split() if p]
            if len(parts) >= 2 and all(p.istitle() for p in parts[:2]):
                owner = line.strip()
                break

    return {'firm_name': firm, 'owner_name': owner}


def detect_seal(image_path):
    """Detect circular/oval seals/stamps. Returns confidence 0..1."""
    try:
        img = cv2.imdecode(np.fromfile(image_path, dtype=np.uint8), cv2.IMREAD_GRAYSCALE)
        if img is None:
            return 0.0
        h, w = img.shape[:2]
        # focus central/right/left areas where stamps often appear
        regions = [img[int(h*0.2):int(h*0.6), int(w*0.05):int(w*0.45)], img[int(h*0.2):int(h*0.6), int(w*0.55):int(w*0.95)]]
        best = 0.0
        for r in regions:
            blurred = cv2.GaussianBlur(r, (5,5), 0)
            edges = cv2.Canny(blurred, 50, 150)
            contours, _ = cv2.findContours(edges, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
            for c in contours:
                area = cv2.contourArea(c)
                if area < 200: continue
                (x,y), radius = cv2.minEnclosingCircle(c)
                circle_area = np.pi * (radius**2)
                if circle_area <= 0: continue
                ratio = area / circle_area
                if 0.5 <= ratio <= 1.0:
                    best = max(best, min(1.0, (area / float(h*w)) * 10.0))
        return float(min(1.0, best))
    except Exception:
        return 0.0


def verify_document(file_path, debug=False):
    """Main verifier implementing the user's Mandaluyong spec.

    Returns a dict of the form:
      - status: BLOCKED or PENDING_MANUAL_REVIEW
      - reason: short explanation when BLOCKED
      - extracted_data: dict of extracted fields
      - ai_confidence: Low/Medium/High
    """
    text = ocr_extract_text(file_path) or ''
    norm = _normalize_text_for_search(text)

    # 1) classification
    doc_type = None
    if 'department of trade and industry' in norm or 'certificate of business name registration' in norm or 'business name registration' in norm:
        doc_type = 'DTI_BUSINESS_NAME_REGISTRATION'
    elif ('locational clearance' in norm or 'locational' in norm) and 'barangay' in norm:
        doc_type = 'BARANGAY_LOCATIONAL_CLEARANCE'
    elif 'mayor' in norm or "mayor's permit" in norm or 'business permit' in norm:
        doc_type = 'MAYOR_BUSINESS_PERMIT'
    elif 'barangay clearance' in norm:
        doc_type = 'BARANGAY_CLEARANCE'

    # Mandatory header checks
    headers_ok = True
    header_reasons = []
    if 'republic of the philippines' not in norm:
        headers_ok = False
        header_reasons.append('missing_republic_header')
    if 'mandaluyong' not in norm and 'mandaluyong city' not in norm:
        headers_ok = False
        header_reasons.append('missing_mandaluyong')
    if doc_type == 'DTI_BUSINESS_NAME_REGISTRATION' and 'department of trade and industry' not in norm:
        headers_ok = False
        header_reasons.append('missing_dti')
    if doc_type in ('BARANGAY_CLEARANCE', 'BARANGAY_LOCATIONAL_CLEARANCE') and 'punong barangay' not in norm and 'office of the punong barangay' not in norm and 'barangay' not in norm:
        # be lenient: presence of barangay word is ok; otherwise mark missing
        headers_ok = False
        header_reasons.append('missing_barangay_office')

    if doc_type is None:
        return {'status': 'BLOCKED', 'reason': 'unrecognized_document_type', 'extracted_data': {}, 'ai_confidence': 'Low'}

    if not headers_ok:
        return {'status': 'BLOCKED', 'reason': 'missing_mandatory_header', 'details': header_reasons, 'extracted_data': {}, 'ai_confidence': 'Low'}

    # 2) extract key fields
    dates = extract_dates_from_text(text)
    names = extract_firm_and_owner(text)
    sig_conf = detect_signature(file_path)
    seal_conf = detect_seal(file_path)

    # parse validity date (try to interpret to check expiry)
    valid_until_raw = dates.get('valid_until')
    parsed_valid = None
    if valid_until_raw:
        try:
            from dateutil import parser as _dp
            parsed_valid = _dp.parse(valid_until_raw, fuzzy=True)
        except Exception:
            parsed_valid = None
    # if valid_until exists and is parseable, check expiry
    if parsed_valid:
        now = datetime.utcnow()
        if parsed_valid < (now - timedelta(days=1)):
            return {'status': 'BLOCKED', 'reason': 'expired_valid_date', 'extracted_data': {'valid_until': valid_until_raw}, 'ai_confidence': 'Low'}

    # 3) ensure presence of mandatory A/B/C features
    missing = []
    if not names.get('firm_name'):
        missing.append('firm_name')
    if not names.get('owner_name'):
        missing.append('owner_name')
    if not dates.get('issue_date'):
        missing.append('issue_date')
    # valid date is mandatory
    if not (dates.get('valid_until') or dates.get('valid_from')):
        missing.append('valid_date')
    # signature or seal required
    if sig_conf < 0.3 and seal_conf < 0.3:
        missing.append('signature_or_seal')

    if missing:
        return {'status': 'BLOCKED', 'reason': 'missing_mandatory_fields', 'missing_fields': missing, 'extracted_data': {'firm': names.get('firm_name'), 'owner': names.get('owner_name'), 'dates': dates, 'signature_confidence': sig_conf, 'seal_confidence': seal_conf}, 'ai_confidence': 'Low'}

    # compute ai_confidence level
    score_components = []
    # header presence counted earlier; give 20
    score_components.append(20)
    # fields present
    field_count = 0
    for k in ('firm_name', 'owner_name'):
        if names.get(k): field_count += 1
    # dates
    if dates.get('issue_date'): field_count += 1
    if dates.get('valid_until') or dates.get('valid_from'): field_count += 1
    score_components.append(min(40, field_count * 10))
    # signature/seal
    sigseal = max(sig_conf, seal_conf)
    score_components.append(int(sigseal * 30))
    combined = sum(score_components)
    if combined >= 70:
        ai_conf = 'High'
    elif combined >= 40:
        ai_conf = 'Medium'
    else:
        ai_conf = 'Low'

    extracted = {
        'document_type': doc_type,
        'firm_name': names.get('firm_name'),
        'owner_name': names.get('owner_name'),
        'issue_date': dates.get('issue_date'),
        'valid_from': dates.get('valid_from'),
        'valid_until': dates.get('valid_until'),
        'signature_confidence': round(float(sig_conf), 3),
        'seal_confidence': round(float(seal_conf), 3),
        'raw_text_snippet': (text or '')[:1500]
    }

    return {'status': 'PENDING_MANUAL_REVIEW', 'extracted_data': extracted, 'ai_confidence': ai_conf}
