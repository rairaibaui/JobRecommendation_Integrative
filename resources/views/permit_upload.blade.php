<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Upload Business Permit</title>
</head>
<body style="font-family:Arial, sans-serif; padding:24px;">
    <h2>Upload Business Permit (Barangay or Mayor's Permit)</h2>
    @if(session('error'))<div style="color:#b00">{{ session('error') }}</div>@endif
    <form method="POST" action="{{ route('permit.upload') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;"><label>Email (optional for lookup):</label><br><input type="email" name="email" value="{{ old('email') }}" style="width:320px;padding:8px;border:1px solid #ccc;border-radius:4px;"></div>
  <div style="margin-bottom:12px;"><label>Document (PDF, max 5MB)</label><br><input type="file" name="document" accept=".pdf" required></div>
        <div><button type="submit" style="padding:8px 16px;border-radius:6px;background:#2b6cb0;color:white;border:none;">Upload & Verify</button></div>
    </form>

    <hr style="margin:24px 0">
    <p>Example API response (success):</p>
    <pre>{
  "success": true,
  "data": {
    "document": { "id": 1, "email": "employer@example.com", "document_type": "BARANGAY_CLEARANCE", "fields": { "business_name": "ACME", "owner_name": "Juan dela Cruz" }, "has_signature": true, "status": "AUTO_APPROVED" },
    "analysis": { "document_type": "BARANGAY_CLEARANCE", "fields": { ... }, "has_signature": true, "status": "AUTO_APPROVED", "raw_text": "..." }
  },
  "message": "Document auto-approved."
}
    </pre>
</body>
</html>
