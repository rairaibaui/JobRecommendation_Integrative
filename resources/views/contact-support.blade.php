<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
      :root {
        --brand-1:#334A5E; --brand-2:#648EB5; --ink:#1b2530; --muted:#6b7a8b; --card:#ffffff;
      }
      * { box-sizing: border-box; }
      body { margin:0; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--ink); background: linear-gradient(180deg, #406482 0%, #5C7A94 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
      .container { width:100%; max-width:600px; background: var(--card); border-radius: 16px; box-shadow: 0 18px 40px rgba(0,0,0,.12); padding: 32px; }
      .header { text-align:center; margin-bottom:24px; }
      .header i { width:56px; height:56px; background:linear-gradient(135deg, var(--brand-2), var(--brand-1)); color:#fff; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:24px; margin-bottom:12px; }
      h1 { margin:0 0 8px; font-size: 28px; font-weight: 700; color: var(--ink); }
      .subtitle { color: var(--muted); font-size: 14px; margin:0; }
      .form-group { margin-bottom:16px; }
      label { display:block; font-size: 13px; margin-bottom:6px; color: var(--ink); font-weight: 600; }
      input, select, textarea { width: 100%; border-radius: 10px; border: 1px solid #d6dde5; padding: 10px 12px; font-size: 14px; font-family: 'Inter', sans-serif; }
      input, select { height: 42px; }
      textarea { min-height: 120px; resize: vertical; }
      input:focus, select:focus, textarea:focus { outline: none; border-color: var(--brand-2); box-shadow: 0 0 0 4px rgba(100,142,181,.15); }
      .btn { width: 100%; height: 44px; border: none; border-radius: 10px; background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); color: #fff; font-weight:700; letter-spacing:.3px; cursor:pointer; box-shadow: 0 10px 22px rgba(51,74,94,.28); transition: transform .12s ease; font-size:15px; }
      .btn:hover { transform: translateY(-1px); }
      .btn:disabled { opacity:0.6; cursor:not-allowed; }
      .back-link { display:inline-flex; align-items:center; gap:8px; color:var(--brand-1); text-decoration:none; font-size:14px; margin-top:20px; }
      .back-link:hover { text-decoration:underline; }
      .alert { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; }
      .alert-success { background:#e8f6ee; color:#17643f; border:1px solid #c7ecd6; }
      .alert-error { background:#ffe8e8; color:#9b2121; border:1px solid #ffd2d2; }
      .info-box { background:#f8f9fa; border-radius:10px; padding:16px; margin-top:20px; font-size:13px; color:#555; }
      .info-box strong { color:var(--brand-1); display:block; margin-bottom:8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-headset"></i>
            <h1>Contact Support</h1>
            <p class="subtitle">We're here to help! Send us a message and we'll get back to you soon.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin:8px 0 0 0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : '') }}" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <select name="subject" id="subject" required>
                    <option value="">Select a topic</option>
                    <option value="Account Issue" {{ old('subject') == 'Account Issue' ? 'selected' : '' }}>Account Issue</option>
                    <option value="Technical Problem" {{ old('subject') == 'Technical Problem' ? 'selected' : '' }}>Technical Problem</option>
                    <option value="Job Posting" {{ old('subject') == 'Job Posting' ? 'selected' : '' }}>Job Posting</option>
                    <option value="Application Issue" {{ old('subject') == 'Application Issue' ? 'selected' : '' }}>Application Issue</option>
                    <option value="Feature Request" {{ old('subject') == 'Feature Request' ? 'selected' : '' }}>Feature Request</option>
                    <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" placeholder="Please describe your issue or question in detail..." required>{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </form>

        <div class="info-box">
            <strong><i class="fas fa-clock"></i> Response Time</strong>
            We typically respond within 24-48 hours during business days.
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>
</body>
</html>
