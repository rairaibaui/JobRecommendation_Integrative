import io
import os
import tempfile
from PIL import Image, ImageChops
import pytesseract
import numpy as np
import cv2


def ocr_extract_text(image_path):
    """Extract text from an image/pdf page using pytesseract.
    Returns extracted text (string)."""
    try:
        img = Image.open(image_path).convert('RGB')
        text = pytesseract.image_to_string(img)
        return text or ""
    except Exception:
        return ""


def detect_document_type(extracted_text: str):
    t = extracted_text.lower()
    if 'barangay' in t or 'clearance' in t:
        return 'BARANGAY_CLEARANCE'
    if 'mayor' in t or 'business permit' in t or 'mayor\'s permit' in t:
        return 'MAYOR_BUSINESS_PERMIT'
    return None


def detect_signature(image_path, debug=False):
    """Heuristic signature detector.
    Strategy: focus on lower third of the page, threshold to dark strokes, find connected components
    and compute stroke-area ratio. Return confidence in [0,1]."""
    try:
        img = cv2.imdecode(np.fromfile(image_path, dtype=np.uint8), cv2.IMREAD_COLOR)
        if img is None:
            return 0.0
        h, w = img.shape[:2]
        # Crop lower 30% of image where signatures typically appear
        y0 = int(h * 0.65)
        crop = img[y0:h, 0:w]
        gray = cv2.cvtColor(crop, cv2.COLOR_BGR2GRAY)
        # Adaptive threshold to catch ink strokes
        thr = cv2.adaptiveThreshold(gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
                                    cv2.THRESH_BINARY_INV, 31, 10)
        # Remove small noise
        kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (3, 3))
        clean = cv2.morphologyEx(thr, cv2.MORPH_OPEN, kernel, iterations=1)
        # Find contours
        contours, _ = cv2.findContours(clean, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
        stroke_area = 0
        for c in contours:
            area = cv2.contourArea(c)
            # consider only meaningful stroke areas
            if area > 50:
                stroke_area += area
        total_area = crop.shape[0] * crop.shape[1]
        ratio = stroke_area / float(total_area) if total_area > 0 else 0.0
        # Map ratio to confidence heuristically
        # small strokes -> low, larger signature blobs -> high
        confidence = min(1.0, ratio * 5.0)
        if debug:
            return confidence, {
                'ratio': ratio,
                'stroke_area': stroke_area,
                'total_area': total_area,
                'contours': len(contours),
            }
        return confidence
    except Exception:
        return 0.0


def ela_forgery_score(image_path):
    """Simple Error Level Analysis (ELA) heuristic.
    Save image at quality=90 and compute normalized difference.
    Returns a score between 0 and 1 (higher = more likely edited).
    Note: ELA is heuristic and not reliable for all formats."""
    try:
        orig = Image.open(image_path).convert('RGB')
        tmp = tempfile.NamedTemporaryFile(suffix='.jpg', delete=False)
        tmp.close()
        # Save recompressed
        orig.save(tmp.name, 'JPEG', quality=90)
        recompressed = Image.open(tmp.name).convert('RGB')
        os.unlink(tmp.name)

        # Compute absolute difference
        diff = ImageChops.difference(orig, recompressed)
        # Amplify and compute mean
        stat = np.mean(np.array(diff).astype(np.float32) / 255.0)
        # Normalize into 0..1; empirical scaling
        score = float(min(1.0, stat * 4.0))
        return score
    except Exception:
        return 0.0


def decision_engine(email_verified, doc_type, signature_confidence, forgery_score):
    # thresholds
    FORGERY_THRESHOLD = 0.5
    SIGNATURE_CONF_THRESHOLD = 0.7

    if not email_verified:
        return 'WAIT_EMAIL_VERIFICATION'
    if doc_type not in ['BARANGAY_CLEARANCE', 'MAYOR_BUSINESS_PERMIT']:
        return 'REJECTED_INVALID_DOCUMENT'
    if forgery_score >= FORGERY_THRESHOLD:
        return 'REJECTED_FAKE_DOCUMENT'
    if signature_confidence < SIGNATURE_CONF_THRESHOLD:
        return 'REVIEW_BY_ADMIN'
    return 'AUTO_APPROVED'


def process_uploaded_document(file_path, email_verified=True, debug=False):
    text = ocr_extract_text(file_path)
    doc_type = detect_document_type(text)
    signature_conf = detect_signature(file_path, debug=False)
    forgery = ela_forgery_score(file_path)
    status = decision_engine(email_verified=email_verified, doc_type=doc_type,
                             signature_confidence=signature_conf, forgery_score=forgery)
    result = {
        'email_verified': bool(email_verified),
        'document_type': doc_type,
        'signature_detected': signature_conf >= 0.7,
        'signature_confidence': round(float(signature_conf), 3),
        'forgery_score': round(float(forgery), 3),
        'status': status,
        'extracted_text_snippet': (text or '')[:800]
    }
    if debug:
        result['_debug'] = {
            'raw_text_length': len(text or ''),
        }
    return result
