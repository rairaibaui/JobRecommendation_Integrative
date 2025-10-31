<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobMatcher - Find Your Dream Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brand-1: #334A5E;
            --brand-2: #648EB5;
            --ink: #1b2530;
            --muted: #6b7a8b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Inter, sans-serif; color: var(--ink); background: #fff; line-height: 1.6; }
        header { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 16px 0; position: sticky; top: 0; z-index: 100; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 24px; font-weight: 700; color: var(--ink); text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .logo i { color: var(--brand-2); }
        .header-btns { display: flex; gap: 12px; }
        .btn { padding: 10px 24px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.2s; display: inline-block; }
        .btn-outline { border: 1px solid var(--brand-2); color: var(--brand-2); background: transparent; }
        .btn-outline:hover { background: var(--brand-2); color: white; }
        .btn-primary { background: var(--brand-2); color: white; border: none; }
        .btn-primary:hover { background: var(--brand-1); }
        .hero { background: linear-gradient(180deg, #f5f7fa 0%, #fff 100%); padding: 80px 0; }
        .hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .hero h1 { font-size: 48px; font-weight: 700; margin-bottom: 20px; line-height: 1.2; }
        .hero p { font-size: 18px; color: var(--muted); margin-bottom: 32px; }
        .hero-btns { display: flex; gap: 16px; flex-wrap: wrap; }
        .hero-btns .btn { padding: 14px 32px; font-size: 16px; }
        .hero-visual { background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); padding: 40px; border-radius: 8px; }
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stat-box { background: rgba(255,255,255,0.15); padding: 24px; border-radius: 8px; text-align: center; backdrop-filter: blur(10px); }
        .stat-box h3 { font-size: 36px; color: white; font-weight: 700; margin-bottom: 4px; }
        .stat-box p { color: rgba(255,255,255,0.95); font-size: 14px; }
        .features { padding: 80px 0; }
        .section-title { text-align: center; margin-bottom: 48px; }
        .section-title h2 { font-size: 36px; font-weight: 700; margin-bottom: 12px; }
        .section-title p { font-size: 18px; color: var(--muted); }
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
        .feature-card { padding: 32px; background: #f8f9fa; border-radius: 8px; transition: transform 0.2s; }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-icon { width: 56px; height: 56px; background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .feature-icon i { font-size: 28px; color: white; }
        .feature-card h3 { font-size: 20px; font-weight: 600; margin-bottom: 12px; }
        .feature-card p { color: var(--muted); font-size: 14px; line-height: 1.6; }
        .cta { background: linear-gradient(135deg, var(--brand-2), var(--brand-1)); padding: 80px 0; text-align: center; }
        .cta h2 { font-size: 36px; font-weight: 700; color: white; margin-bottom: 16px; }
        .cta p { font-size: 18px; color: rgba(255,255,255,0.95); margin-bottom: 32px; }
        .cta .btn-primary { background: white; color: var(--brand-2); }
        .cta .btn-primary:hover { background: #f0f0f0; }
        footer { background: #1b2530; color: white; padding: 48px 0 24px; }
        .footer-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-bottom: 32px; }
        .footer-col h4 { font-size: 16px; margin-bottom: 16px; font-weight: 600; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 8px; }
        .footer-col a { color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; }
        .footer-col a:hover { color: white; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 24px; text-align: center; color: rgba(255,255,255,0.6); font-size: 14px; }
        @media (max-width: 768px) {
            .hero-grid, .features-grid, .footer-grid { grid-template-columns: 1fr; }
            .hero h1 { font-size: 32px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="#" class="logo"><i class="fas fa-briefcase"></i> JobMatcher</a>
                <div class="header-btns">
                    <a href="/login" class="btn btn-outline">Sign in</a>
                    <a href="/register" class="btn btn-primary">Sign up</a>
                </div>
            </div>
        </div>
    </header>
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <h1>Find your dream job in Mandaluyong</h1>
                    <p>Connect with top employers and discover opportunities that match your skills and experience.</p>
                    <div class="hero-btns">
                        <a href="/register" class="btn btn-primary"><i class="fas fa-rocket"></i> Get started</a>
                        <a href="#features" class="btn btn-outline">Learn more</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="stats">
                        <div class="stat-box"><h3>1,000+</h3><p>Active Jobs</p></div>
                        <div class="stat-box"><h3>500+</h3><p>Companies</p></div>
                        <div class="stat-box"><h3>5,000+</h3><p>Job Seekers</p></div>
                        <div class="stat-box"><h3>95%</h3><p>Success Rate</p></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Why choose JobMatcher?</h2>
                <p>Everything you need to advance your career</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-search"></i></div>
                    <h3>Smart Matching</h3>
                    <p>AI-powered algorithm matches you with jobs that fit your skills and experience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-building"></i></div>
                    <h3>Top Employers</h3>
                    <p>Connect with leading companies actively hiring talented professionals.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Career Growth</h3>
                    <p>Track applications and access resources to advance your career.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="cta">
        <div class="container">
            <h2>Ready to start your journey?</h2>
            <p>Join thousands of professionals who found their dream careers</p>
            <a href="/register" class="btn btn-primary"><i class="fas fa-user-plus"></i> Create free account</a>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Job Seekers</h4>
                    <ul>
                        <li><a href="#">Browse Jobs</a></li>
                        <li><a href="#">Career Advice</a></li>
                        <li><a href="#">Resume Tips</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Employers</h4>
                    <ul>
                        <li><a href="#">Post a Job</a></li>
                        <li><a href="#">Find Talent</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 JobMatcher. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
