Document Verifier Service (Prototype)

This lightweight Flask service demonstrates an AI-assisted document verification pipeline for employer registration.

Features
- Email verification (token-based, demo via logging)
- Upload document endpoint
- OCR via Tesseract (pytesseract)
- Document classification (Barangay Clearance / Mayor's / Business Permit) via text heuristics
- Signature detection (simple heuristic using OpenCV strokes in signature area)
- Forgery detection via Error Level Analysis (ELA) heuristic
- Decision engine that returns AUTO_APPROVED / REVIEW_BY_ADMIN / REJECTED_FAKE_DOCUMENT

Quick start (Windows PowerShell)
1. Create virtualenv and activate
```powershell
python -m venv .venv
.\.venv\Scripts\Activate
pip install -r document_verifier_service/requirements.txt
```
2. Ensure Tesseract is installed and on PATH. On Windows install from: https://github.com/tesseract-ocr/tesseract
3. Run the service
```powershell
$env:FLASK_APP = 'document_verifier_service.app'
$env:FLASK_ENV = 'development'
python -m flask run
```
4. Endpoints
- POST /register { "email": "you@example.com" }
  -> returns a verification URL in logs (demo)
- GET /verify/<token>
  -> mark email verified
- POST /upload_document (form-data)
  - email: verified email
  - token: token from /register (optional if email already verified)
  - file: document file
  -> JSON response with fields: email_verified, document_type, signature_detected (bool), forgery_score (0..1), status

Notes & limitations
- This is a prototype. Signature detection and forgery detection use heuristics and are not production-grade.
- Replace or augment detection functions with ML models for production use (signature classifier, forgery model).
- Tesseract language and accuracy depend on local installation and language packs.

Tests
```powershell
python -m pytest document_verifier_service/tests -q
```
