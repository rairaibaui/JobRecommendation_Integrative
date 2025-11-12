import os
import sqlite3
import tempfile
from flask import Flask, request, jsonify, url_for, redirect
from itsdangerous import URLSafeTimedSerializer
from werkzeug.utils import secure_filename
from verifier import process_uploaded_document

DB_PATH = os.path.join(os.path.dirname(__file__), 'verifier.db')
SECRET_KEY = os.environ.get('DV_SECRET', 'dev-secret-key-please-change')
TOKEN_SALT = 'document-verifier-salt'

app = Flask(__name__)
app.config['SECRET_KEY'] = SECRET_KEY
serializer = URLSafeTimedSerializer(app.config['SECRET_KEY'])

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
    result = process_uploaded_document(save_path, email_verified=email_verified)

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


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
