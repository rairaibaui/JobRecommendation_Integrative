<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
      :root {
        --brand-1:#334A5E; --brand-2:#648EB5; --ink:#1b2530; --muted:#6b7a8b; --card:#ffffff;
      }
      * { box-sizing: border-box; }
      body { margin:0; font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--ink); }
        .auth-grid { 
            min-height: 100vh; 
            display: flex; 
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #406482 0%, #5C7A94 100%); 
                padding: 40px 16px 40px 24px; /* small left gutter for hero */
            width: 100%;
        }
        .auth-inner {
            width: min(1200px, 100% - 48px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: 0.7fr 1.3fr;
            gap: 40px;
            align-items: center;
        }
        .hero { 
            background: linear-gradient(135deg, var(--brand-2), var(--brand-1));
            border-radius: 16px; 
            color:#fff; 
            padding: 32px 28px; 
            box-shadow: 0 15px 40px rgba(51,74,94,.3);
            display:flex; 
            flex-direction:column; 
            justify-content:center; 
            min-height: 400px;
            animation: bumpInLeft 0.75s cubic-bezier(.2,.8,.2,1) both;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        /* Bump-in animations: slight overshoot toward center, then settle */
        @keyframes bumpInLeft {
            0% { opacity: 0; transform: translateX(-60px); }
            60% { opacity: 1; transform: translateX(12px); }
            80% { transform: translateX(-6px); }
            100% { transform: translateX(0); }
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.3;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.5;
            }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hero h1 { 
            margin:0 0 6px; 
            font-size: 22px; 
            font-weight: 700; 
            position: relative; 
            z-index: 1;
            line-height: 1.2;
        }
        .hero p { 
            margin:0 0 14px; 
            opacity:.95; 
            font-size:13px; 
            line-height:1.4; 
            position: relative; 
            z-index: 1;
        }
        .hero .bullets { 
            display:grid; 
            gap:8px; 
            margin-top:4px; 
            padding-left:0; 
            position: relative; 
            z-index: 1;
        }
        .hero .bullets li { 
            list-style:none; 
            display:flex; 
            align-items:flex-start; 
            gap:8px; 
            font-size:12px;
            animation: fadeInUp 0.6s ease-out backwards;
        }
        .hero .bullets li:nth-child(1) { animation-delay: 0.2s; }
        .hero .bullets li:nth-child(2) { animation-delay: 0.3s; }
        .hero .bullets li:nth-child(3) { animation-delay: 0.4s; }
        .hero .bullets i { 
            color:#ffe082; 
            font-size:13px; 
            flex-shrink:0; 
            margin-top:1px;
        }
        .hero .bullets span { flex:1; }
      .hero-card { max-width: 560px; color:#fff; background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); border-radius: 20px; padding: 32px; box-shadow: 0 25px 60px rgba(51,74,94,.35); }
      .brand { display:flex; align-items:center; gap:10px; font-weight:700; letter-spacing:.2px; font-size:18px; position:relative; z-index:1; margin-bottom:10px; animation: fadeInUp 0.6s ease-out 0.1s backwards; }
      .brand i { background: rgba(255,255,255,.18); width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border-radius:7px; font-size:14px; }
      .hero h1 { margin: 16px 0 8px; font-size: 28px; font-weight:800; }
      .hero p { margin:0 0 18px; opacity:.9; }
      .bullets { display:grid; gap:10px; margin-top:8px; }
      .bullets li { list-style:none; display:flex; align-items:center; gap:10px; }
      .bullets i { color:#ffe082; }
      .right { display:flex; align-items:center; justify-content:center; }
            .card { 
                width: 100%; 
                max-width: 520px; /* slightly bigger form */
                background: var(--card);
                border-radius: 16px; 
                box-shadow: 0 18px 40px rgba(0,0,0,.12);
                padding: 28px;
                animation: bumpInRight 0.75s cubic-bezier(.2,.8,.2,1) both;
            }
            @keyframes bumpInRight {
                0% { opacity: 0; transform: translateX(60px); }
                60% { opacity: 1; transform: translateX(-12px); }
                80% { transform: translateX(6px); }
                100% { transform: translateX(0); }
            }
    .card h2 { margin:0 0 6px; font-size: 24px; font-weight: 700; color: var(--ink); text-align:center; }
    .sub { margin:0 0 18px; color: var(--muted); font-size: 13px; text-align:center; }
      label { display:block; font-size: 13px; margin: 10px 0 6px; color: var(--ink); font-weight: 600; }
    /* Text inputs */
    input:not([type="checkbox"]) { width: 100%; height: 46px; border-radius: 10px; border: 1px solid #d6dde5; padding: 12px 12px; font-size: 14px; }
    input:not([type="checkbox"]):focus { outline: none; border-color: var(--brand-2); box-shadow: 0 0 0 4px rgba(100,142,181,.15); }
    /* Remember-me checkbox at regular size */
    input[type="checkbox"] { width: 16px; height: 16px; margin: 0; accent-color: var(--brand-2); border-radius: 3px; vertical-align: middle; }
      .row { display:flex; align-items:center; justify-content:space-between; gap:8px; margin: 8px 0 16px; }
      .row a { font-size: 13px; color: var(--brand-1); text-decoration:none; }
      .row a:hover { text-decoration: underline; }
    .btn { width: 100%; height: 48px; border: none; border-radius: 10px; background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); color: #fff; font-weight:700; letter-spacing:.3px; cursor:pointer; box-shadow: 0 10px 22px rgba(51,74,94,.28); transition: transform .12s ease; }
      .btn:hover { transform: translateY(-1px); }
      .or { display:flex; align-items:center; gap:12px; color:#9aa9b8; margin:18px 0; font-size:12px; }
      .or:before, .or:after { content:""; height:1px; flex:1; background:#e5ecf2; }
      .ghost { width:100%; background:#fff; color:var(--brand-1); border:1px solid #d6dde5; box-shadow:none; }
      .fine { font-size: 12px; color: var(--muted); text-align:center; margin-top: 10px; }
      .footer-links { margin-top: 16px; text-align:center; font-size: 12px; color:#8a97a6; }
      .footer-links a { color: var(--brand-1); text-decoration:none; }
      .footer-links a:hover { text-decoration: underline; }
    @media (max-width: 980px) { 
        .auth-inner { 
            grid-template-columns: 1fr; 
        } 
        .auth-grid { 
            padding: 24px 16px; 
        }
        .hero { 
            min-height: auto; 
            padding: 24px; 
        }
    }
    </style>
</head>
<body>
    <div class="auth-grid">
        <div class="auth-inner">
        <div class="hero">
            <div class="brand">
                <i class="fas fa-briefcase"></i>
                JobMatcher
            </div>
            <h1 style="animation: fadeInUp 0.6s ease-out 0.15s backwards;">Welcome back!</h1>
            <p style="animation: fadeInUp 0.6s ease-out 0.2s backwards;">Sign in to access your personalized job recommendations and continue your career journey.</p>
            <ul class="bullets">
                <li><i class="fas fa-check-circle"></i> <span>Access your personalized dashboard</span></li>
                <li><i class="fas fa-check-circle"></i> <span>View tailored job recommendations</span></li>
                <li><i class="fas fa-check-circle"></i> <span>Track your applications in real-time</span></li>
            </ul>
        </div>
        <div class="right" style="width:100%;">
            <div class="card">
                <h2>Welcome back</h2>
                <div class="sub">Sign in to continue to your dashboard</div>

                @if(session('success'))
                    <div style="background:#e8f6ee; color:#17643f; border:1px solid #c7ecd6; padding:10px 12px; border-radius:10px; margin-bottom:12px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->has('email'))
                    <div style="background:#ffe8e8; color:#9b2121; border:1px solid #ffd2d2; padding:10px 12px; border-radius:10px; margin-bottom:12px;">{{ $errors->first('email') }}</div>
                @endif
                @if($errors->has('password'))
                    <div style="background:#ffe8e8; color:#9b2121; border:1px solid #ffd2d2; padding:10px 12px; border-radius:10px; margin-bottom:12px;">{{ $errors->first('password') }}</div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                      <label for="email">Email</label>
                      <input type="email" name="email" id="email" placeholder="you@example.com" value="{{ session('email') ?? old('email') }}" autocomplete="email" required autofocus>
                      <label for="password">Password</label>
                      <input type="password" name="password" id="password" placeholder="Enter your password" autocomplete="current-password" required>
                    <div class="row">
                        <label style="display:flex; align-items:center; gap:8px; font-weight:500;">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me
                        </label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn">Sign in</button>
                </form>

                <div class="or">OR</div>
                <a href="{{ route('register') }}" style="display:block; text-decoration:none;">
                    <button class="btn ghost"><i class="fa fa-user-plus" style="margin-right:8px;"></i>Create new account</button>
                </a>
                <div class="fine">By continuing, you agree to our Terms and acknowledge our Privacy Policy.</div>
                <div class="footer-links">Need help? <a href="{{ route('contact.support') }}">Contact support</a></div>
            </div>
        </div>
        </div>
    </div>
</body>
</html>