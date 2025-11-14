import os
import sqlite3
import tempfile
import logging
from flask import Flask, request, jsonify, url_for, redirect
from itsdangerous import URLSafeTimedSerializer
from werkzeug.utils import secure_filename

# Lazy imports - only load when needed to avoid startup failures
_verifier_module = None
_ai_detector_module = None
_legacy_verifier_module = None

DB_PATH = os.path.join(os.path.dirname(__file__), 'verifier.db')
SECRET_KEY = os.environ.get('DV_SECRET', 'dev-secret-key-please-change')
TOKEN_SALT = 'document-verifier-salt'

app = Flask(__name__)
app.config['SECRET_KEY'] = SECRET_KEY
serializer = URLSafeTimedSerializer(app.config['SECRET_KEY'])

# Set up logging
if not app.debug:
    logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def get_verifier_module():
    """Lazy import of verifier module"""
    global _verifier_module
    if _verifier_module is None:
        try:
            from verifier import process_uploaded_document
            _verifier_module = process_uploaded_document
        except ImportError as e:
            logger.error(f"Failed to import verifier module: {e}")
            raise
    return _verifier_module

def get_ai_detector():
    """Lazy import of AI detector module"""
    global _ai_detector_module
    if _ai_detector_module is None:
        try:
            from mandaluyong_ai_detector import validate_document as ai_validate_document
            _ai_detector_module = ai_validate_document
        except ImportError as e:
            logger.warning(f"Failed to import AI detector module: {e}")
            _ai_detector_module = False  # Mark as unavailable
    return _ai_detector_module if _ai_detector_module is not False else None

def get_legacy_verifier():
    """Lazy import of legacy verifier module"""
    global _legacy_verifier_module
    if _legacy_verifier_module is None:
        try:
            from mandaluyong_verifier import verify_document as legacy_verify_document
            _legacy_verifier_module = legacy_verify_document
        except ImportError as e:
            logger.warning(f"Failed to import legacy verifier module: {e}")
            _legacy_verifier_module = False  # Mark as unavailable
    return _legacy_verifier_module if _legacy_verifier_module is not False else None

ALLOWED_EXT = {'pdf', 'jpg', 'jpeg', 'png', 'tif', 'tiff'}
UPLOAD_DIR = os.path.join(os.path.dirname(__file__), 'uploads')
os.makedirs(UPLOAD_DIR, exist_ok=True)


def init_db():
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute('''CREATE TABLE IF NOT EXISTS employers (id INTEGER PRIMARY KEY, email TEXT UNIQUE, verified INTEGER DEFAULT 0)''')
    c.execute('''CREATE TABLE IF NOT EXISTS documents (id INTEGER PRIMARY KEY, employer_id INTEGER, path TEXT, status TEXT)''')
    conn.commit()
    conn.close()


init_db()


@app.route('/register', methods=['POST'])
def register():
    data = request.get_json() or {}
    email = data.get('email')
    if not email:
        return jsonify({'error': 'email required'}), 400
    # create employer row if not exists
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    try:
        c.execute('INSERT OR IGNORE INTO employers (email) VALUES (?)', (email,))
        conn.commit()
    finally:
        conn.close()

    token = serializer.dumps(email, salt=TOKEN_SALT)
    verify_url = url_for('verify_email', token=token, _external=True)
    # In prod send email. For prototype return URL in response for convenience
    app.logger.info('Verification URL for %s: %s', email, verify_url)
    return jsonify({'message': 'registration created, check logs for verification link', 'verify_url': verify_url})


@app.route('/verify/<token>')
def verify_email(token):
    try:
        email = serializer.loads(token, salt=TOKEN_SALT, max_age=60 * 60 * 24)
    except Exception:
        return jsonify({'success': False, 'message': 'invalid or expired token'}), 400
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute('UPDATE employers SET verified=1 WHERE email=?', (email,))
    conn.commit()
    conn.close()
    return jsonify({'success': True, 'message': 'email verified'})


@app.route('/upload_document', methods=['POST'])
def upload_document():
    # form-data: email, token (optional), file
    email = request.form.get('email')
    token = request.form.get('token')
    if not email:
        return jsonify({'error': 'email required'}), 400

    # Check email verification by token or DB
    email_verified = False
    if token:
        try:
            t_email = serializer.loads(token, salt=TOKEN_SALT, max_age=60 * 60 * 24)
            email_verified = (t_email == email)
        except Exception:
            email_verified = False
    else:
        conn = sqlite3.connect(DB_PATH)
        c = conn.cursor()
        c.execute('SELECT verified FROM employers WHERE email=?', (email,))
        row = c.fetchone()
        conn.close()
        if row and row[0] == 1:
            email_verified = True

    if 'file' not in request.files:
        return jsonify({'success': False, 'message': 'file required'}), 400

    f = request.files['file']
    filename = secure_filename(f.filename)
    ext = filename.rsplit('.', 1)[-1].lower() if '.' in filename else ''
    if ext not in ALLOWED_EXT:
        return jsonify({'success': False, 'message': 'unsupported file type'}), 400

    save_path = os.path.join(UPLOAD_DIR, filename)
    f.save(save_path)

    # Process
    try:
        process_uploaded_document = get_verifier_module()
        result = process_uploaded_document(save_path, email_verified=email_verified)
    except Exception as e:
        app.logger.error(f"Error processing document: {e}")
        return jsonify({'success': False, 'message': f'Document processing failed: {str(e)}'}), 500

    # Persist document record
    conn = sqlite3.connect(DB_PATH)
    c = conn.cursor()
    c.execute('SELECT id FROM employers WHERE email=?', (email,))
    row = c.fetchone()
    if not row:
        c.execute('INSERT INTO employers (email, verified) VALUES (?, ?)', (email, 1 if email_verified else 0))
        conn.commit()
        employer_id = c.lastrowid
    else:
        employer_id = row[0]
    c.execute('INSERT INTO documents (employer_id, path, status) VALUES (?,?,?)', (employer_id, save_path, result['status']))
    conn.commit()
    conn.close()

    return jsonify(result)


@app.route('/validate_document', methods=['POST'])
def validate_document():
    """
    New endpoint for AI document validation.
    Accepts file path as JSON or file upload.
    Tries new AI detector first, falls back to legacy OCR verifier.
    """
    try:
        # Accept either JSON with file_path or multipart form with file
        if request.is_json:
            data = request.get_json()
            file_path = data.get('file_path')
            
            if not file_path:
                return jsonify({'error': 'file_path required in JSON body'}), 400
            
            if not os.path.exists(file_path):
                return jsonify({'error': f'File not found: {file_path}'}), 404
        else:
            # Multipart form data with file upload
            if 'file' not in request.files:
                return jsonify({'error': 'file required in form data'}), 400
            
            f = request.files['file']
            if f.filename == '':
                return jsonify({'error': 'No file selected'}), 400
            
            filename = secure_filename(f.filename)
            ext = filename.rsplit('.', 1)[-1].lower() if '.' in filename else ''
            if ext not in ALLOWED_EXT:
                return jsonify({'error': 'unsupported file type'}), 400
            
            save_path = os.path.join(UPLOAD_DIR, filename)
            f.save(save_path)
            file_path = save_path
        
        # Try new AI detector first
        ai_detector = get_ai_detector()
        if ai_detector:
            try:
                app.logger.info(f'Attempting new AI detector for file: {file_path}')
                result = ai_detector(file_path)
                if result and 'ai_detection' in result:
                    app.logger.info('Successfully validated with new AI detector')
                    return jsonify(result)
            except Exception as e:
                app.logger.warning(f'New AI detector failed: {e}, falling back to legacy verifier')
        else:
            app.logger.warning('New AI detector not available, skipping')
        
        # Fallback to legacy OCR verifier
        legacy_verifier = get_legacy_verifier()
        if legacy_verifier:
            try:
                app.logger.info(f'Attempting legacy OCR verifier for file: {file_path}')
                result = legacy_verifier(file_path)
                if result and 'status' in result:
                    app.logger.info('Successfully validated with legacy OCR verifier')
                    return jsonify(result)
            except Exception as e:
                app.logger.error(f'Legacy verifier also failed: {e}')
        else:
            app.logger.error('Legacy verifier not available')
        
        return jsonify({
            'error': 'Both AI validation methods failed',
            'status': 'ERROR'
        }), 500
        
    except Exception as e:
        app.logger.error(f'Document validation endpoint error: {e}')
        return jsonify({
            'error': str(e),
            'status': 'ERROR'
        }), 500


@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    # Check module availability
    modules_status = {
        'ai_detector': get_ai_detector() is not None,
        'legacy_verifier': get_legacy_verifier() is not None,
    }
    try:
        get_verifier_module()
        modules_status['verifier'] = True
    except:
        modules_status['verifier'] = False
    
    return jsonify({
        'status': 'healthy',
        'service': 'document_verifier',
        'port': int(os.environ.get('FLASK_PORT', 5010)),
        'modules': modules_status
    })


if __name__ == '__main__':
    port = int(os.environ.get('FLASK_PORT', 5010))  # Default to 5010 to avoid port conflicts
    app.run(host='0.0.0.0', port=port)
