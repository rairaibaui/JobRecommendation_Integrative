import tempfile
import os
from datetime import datetime, timedelta
import pytest

from document_verifier_service import mandaluyong_verifier as mv


def make_temp_image_with_text(text: str):
    # create a small PNG with the provided text rendered via PIL to simulate OCR output
    from PIL import Image, ImageDraw
    img = Image.new('RGB', (800, 1000), color='white')
    draw = ImageDraw.Draw(img)
    y = 20
    for line in text.splitlines():
        draw.text((20, y), line, fill='black')
        y += 18
    tmp = tempfile.NamedTemporaryFile(suffix='.png', delete=False)
    img.save(tmp.name)
    return tmp.name


@pytest.fixture(autouse=True)
def patch_ocr_and_detectors(monkeypatch):
    # Default stable behavior for tests: mock OCR/detection functions to deterministic values
    def fake_ocr(path):
        # Read the image file (created by helper) and return a stored marker (the test writes the content)
        # We embed the intended OCR text into the filename for simplicity in tests
        try:
            with open(path + '.txt', 'r', encoding='utf-8') as fh:
                return fh.read()
        except Exception:
            return ''

    def fake_sig(path):
        return 0.8  # high confidence signature by default

    monkeypatch.setattr(mv, 'ocr_extract_text', fake_ocr)
    monkeypatch.setattr(mv, 'detect_signature', fake_sig)
    monkeypatch.setattr(mv, 'detect_seal', lambda p: 0.2)
    yield


def write_ocr_text_for_image(image_path, text):
    # write a companion .txt file used by fake_ocr
    with open(image_path + '.txt', 'w', encoding='utf-8') as fh:
        fh.write(text)


def test_valid_dti_certificate():
    # create mock image + OCR text
    ocr_text = "Republic of the Philippines\nDepartment of Trade and Industry\nMandaluyong City\nCertificate of Business Name Registration\nBusiness Name: MARGARITA SARI-SARI STORE\nOwner: MARGARITA PALLES MONDERO\nIssued: 2024-11-01\nValid Until: 2026-11-01\nSignature: MA. CRISTINA A. ROQUE"
    img = make_temp_image_with_text(ocr_text)
    write_ocr_text_for_image(img, ocr_text)

    res = mv.verify_document(img)
    os.unlink(img)
    try:
        os.unlink(img + '.txt')
    except Exception:
        pass
    assert res['status'] == 'PENDING_MANUAL_REVIEW'
    assert res['extracted_data']['document_type'].startswith('DTI')


def test_valid_barangay_locational_clearance():
    ocr_text = "Republic of the Philippines\nOffice of the Punong Barangay\nMandaluyong City\nBusiness Locational Clearance\nBusiness Name: MARGARITA SARI-SARI STORE\nOwner: MARGARITA PALLES MONDERO\nIssued: 2025-05-01\nValid Until: 2026-05-01\nSignature: CARLITO T. CERNAD"
    img = make_temp_image_with_text(ocr_text)
    write_ocr_text_for_image(img, ocr_text)

    res = mv.verify_document(img)
    os.unlink(img)
    try:
        os.unlink(img + '.txt')
    except Exception:
        pass
    assert res['status'] == 'PENDING_MANUAL_REVIEW'
    assert 'LOCATIONAL' in res['extracted_data']['document_type'] or 'BARANGAY' in res['extracted_data']['document_type']


def test_blocked_invalid_type():
    ocr_text = "Invoice\nStore: Bob's Shop\nTotal: 123.45\nDate: 2025-10-01\nThank you for shopping"
    img = make_temp_image_with_text(ocr_text)
    write_ocr_text_for_image(img, ocr_text)

    res = mv.verify_document(img)
    os.unlink(img)
    try:
        os.unlink(img + '.txt')
    except Exception:
        pass
    assert res['status'] == 'BLOCKED'


def test_blocked_expired_date():
    # create permit with expiry last year
    past = (datetime.utcnow() - timedelta(days=400)).strftime('%Y-%m-%d')
    ocr_text = f"Republic of the Philippines\nDepartment of Trade and Industry\nMandaluyong City\nCertificate of Business Name Registration\nBusiness Name: OLD STORE\nOwner: OLD OWNER\nIssued: 2020-01-01\nValid Until: {past}\nSignature: SIGNER NAME"
    img = make_temp_image_with_text(ocr_text)
    write_ocr_text_for_image(img, ocr_text)

    res = mv.verify_document(img)
    os.unlink(img)
    try:
        os.unlink(img + '.txt')
    except Exception:
        pass
    assert res['status'] == 'BLOCKED'
*** End Patch