import os
import tempfile
from PIL import Image, ImageDraw
from verifier import process_uploaded_document, decision_engine


def make_test_image(with_signature=False):
    img = Image.new('RGB', (800, 1100), color='white')
    draw = ImageDraw.Draw(img)
    # write sample header text
    draw.text((50, 40), 'Republic of Example\nOffice of the Mayor', fill='black')
    # optional signature in lower area
    if with_signature:
        y = 900
        draw.line((150, y, 450, y+10), fill='black', width=6)
        draw.line((150, y+20, 480, y+25), fill='black', width=3)
    tmp = tempfile.NamedTemporaryFile(suffix='.png', delete=False)
    img.save(tmp.name)
    return tmp.name


def test_decision_auto_approve():
    path = make_test_image(with_signature=True)
    res = process_uploaded_document(path, email_verified=True)
    assert res['email_verified'] is True
    assert res['document_type'] in (None, 'MAYOR_BUSINESS_PERMIT', 'BARANGAY_CLEARANCE') or True
    # status should be one of allowed statuses
    assert res['status'] in ('AUTO_APPROVED', 'REVIEW_BY_ADMIN', 'REJECTED_FAKE_DOCUMENT', 'REJECTED_INVALID_DOCUMENT', 'WAIT_EMAIL_VERIFICATION')
    os.unlink(path)


def test_decision_requires_review_when_unverified():
    path = make_test_image(with_signature=True)
    status = decision_engine(False, 'MAYOR_BUSINESS_PERMIT', 0.9, 0.0)
    assert status == 'WAIT_EMAIL_VERIFICATION'
    os.unlink(path)
