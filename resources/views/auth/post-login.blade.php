<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signing you in… • JobMatcher</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-1: #334A5E;
            --brand-2: #648EB5;
            --ink: #1b2530;
            --muted: #6b7a8b;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            color: #fff;
            background: radial-gradient(1200px 600px at 15% 20%, rgba(255,255,255,0.12) 0%, transparent 60%),
                        linear-gradient(135deg, var(--brand-2), var(--brand-1));
            display: grid;
            place-items: center;
            overflow: hidden;
        }
        .card {
            width: min(560px, 92vw);
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            padding: 32px 28px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.25);
            animation: floatIn 600ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }
        .header {
            display: flex; align-items: center; gap: 12px; margin-bottom: 16px;
        }
        .logo {
            display: inline-grid; place-items: center;
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(255,255,255,0.2);
            color: #fff; font-size: 20px;
        }
        h1 { margin: 8px 0 4px; font-size: 24px; font-weight: 700; }
        p { margin: 0; color: rgba(255,255,255,0.95); }
        .progress {
            margin-top: 20px;
            background: rgba(255,255,255,0.22);
            height: 8px; border-radius: 999px; overflow: hidden;
        }
        .bar {
            height: 100%; width: 0;
            background: #fff;
            animation: load 2000ms ease-in-out forwards;
        }
        .dots { margin-top: 18px; display: flex; gap: 8px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.9); opacity: 0.4; animation: blink 1.2s infinite; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        .meta { margin-top: 18px; font-size: 13px; color: rgba(255,255,255,0.85); }
        .foot { margin-top: 22px; font-size: 12px; color: rgba(255,255,255,0.7); }
        @keyframes load { to { width: 100%; } }
        @keyframes blink { 0%, 100% { opacity: .4; transform: translateY(0); } 50% { opacity: 1; transform: translateY(-2px); } }
        @keyframes floatIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 1px, 1px); border: 0; }
        .delayed-link { display: inline-flex; align-items: center; gap: 8px; margin-top: 12px; color: #fff; text-decoration: none; font-weight: 600; opacity: .9; }
        .delayed-link:hover { opacity: 1; }
    </style>
</head>
<body>
    <main class="card" role="status" aria-live="polite">
        <div class="header">
            <div class="logo">💼</div>
            <div>
                <h1>Signing you in…</h1>
                <p>Preparing your dashboard and updates</p>
            </div>
        </div>
        <div class="progress" aria-hidden="true"><div class="bar"></div></div>
        <div class="dots" aria-hidden="true"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
        <div class="meta">Tip: You can update your profile and bookmarks from your dashboard anytime.</div>
        <a id="skipLink" class="delayed-link" href="#">Skip loading</a>
        <div class="foot">JobMatcher • Connecting talent with opportunity</div>
        <span class="sr-only" id="target-url">{{ $target ?? url('/') }}</span>
    </main>
    <script>
        (function(){
            const target = document.getElementById('target-url').textContent.trim();
            const skip = document.getElementById('skipLink');
            let redirected = false;
            const go = () => { if (!redirected) { redirected = true; window.location.replace(target); } };
            // Smooth timing ~2s to align with progress bar animation
            setTimeout(go, 2000);
            // Allow manual skip
            skip.href = target; skip.addEventListener('click', go);
            // Safety: if animation stalls, hard redirect after 3s
            setTimeout(go, 4000);
        })();
    </script>
</body>
</html>
