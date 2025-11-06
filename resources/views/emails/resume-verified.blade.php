<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Verification Update</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            @if($status === 'verified')
                background: linear-gradient(135deg, #28a745, #20c997);
            @elseif($status === 'rejected')
                background: linear-gradient(135deg, #dc3545, #e74c3c);
            @else
                background: linear-gradient(135deg, #ffc107, #ff9800);
            @endif
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            line-height: 1.8;
            color: #495057;
            margin-bottom: 25px;
        }
        .status-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .status-verified {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        .status-review {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        .status-incomplete {
            background: #cce5ff;
            color: #004085;
            border: 2px solid #b8daff;
        }
        .score-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .score-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .score-value {
            font-size: 36px;
            font-weight: 700;
            @if($score >= 80)
                color: #28a745;
            @elseif($score >= 50)
                color: #ffc107;
            @else
                color: #dc3545;
            @endif
        }
        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            margin-top: 15px;
        }
        .progress-fill {
            height: 100%;
            @if($score >= 80)
                background: linear-gradient(90deg, #28a745, #20c997);
            @elseif($score >= 50)
                background: linear-gradient(90deg, #ffc107, #ff9800);
            @else
                background: linear-gradient(90deg, #dc3545, #e74c3c);
            @endif
            width: {{ $score }}%;
            transition: width 0.5s ease;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: #004085;
        }
        .features-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .features-list li {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #495057;
        }
        .features-list li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }
        .cta-button {
            display: inline-block;
            @if($status === 'verified')
                background: #28a745;
            @elseif($status === 'rejected')
                background: #dc3545;
            @else
                background: #ffc107;
                color: #000;
            @endif
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.3s;
            text-align: center;
        }
        .cta-button:hover {
            opacity: 0.9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .footer-links {
            margin-top: 15px;
        }
        .footer-links a {
            color: #648EB5;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">
                @if($status === 'verified')
                    ✅
                @elseif($status === 'rejected')
                    ❌
                @elseif($status === 'incomplete')
                    ⚠️
                @else
                    🔍
                @endif
            </div>
            <h1>
                @if($status === 'verified')
                    Resume Verified Successfully!
                @elseif($status === 'rejected')
                    Resume Verification Failed
                @elseif($status === 'incomplete')
                    Resume Needs Improvement
                @else
                    Resume Under Review
                @endif
            </h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Hi {{ $user->first_name ?? 'Job Seeker' }},</p>

            @if($status === 'verified')
                <p class="message">
                    🎉 <strong>Congratulations!</strong> Your resume has been verified and approved by our system. 
                    Your profile is now complete and ready to make a great impression on potential employers!
                </p>

                <div class="status-badge status-verified">
                    ✓ Resume Verified
                </div>

                @if($score > 0)
                    <div class="score-section">
                        <div class="score-label">Quality Score</div>
                        <div class="score-value">{{ $score }}<span style="font-size: 24px; color: #6c757d;">/100</span></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                @endif

                <div class="info-box">
                    <strong>✨ What's Next?</strong>
                    You can now apply for jobs with confidence! Your verified resume will be automatically included with your applications.
                </div>

                <ul class="features-list">
                    <li>Apply for unlimited job postings</li>
                    <li>Verified badge on your profile</li>
                    <li>Higher visibility to employers</li>
                    <li>Faster application processing</li>
                </ul>

                <div style="text-align: center;">
                    <a href="{{ route('recommendation') }}" class="cta-button">Browse Jobs Now</a>
                </div>

            @elseif($status === 'needs_review')
                <p class="message">
                    Your resume has been uploaded successfully and is currently under review by our administration team. 
                    We'll notify you once the review is complete.
                </p>

                <div class="status-badge status-review">
                    ⏳ Under Admin Review
                </div>

                @if($score > 0)
                    <div class="score-section">
                        <div class="score-label">Initial Quality Score</div>
                        <div class="score-value">{{ $score }}<span style="font-size: 24px; color: #6c757d;">/100</span></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                @endif

                <div class="info-box">
                    <strong>📋 What Happens Next?</strong>
                    Our admin team will review your resume within 24-48 hours. You'll receive an email notification once the review is complete.
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('dashboard') }}" class="cta-button">View Dashboard</a>
                </div>

            @elseif($status === 'incomplete')
                <p class="message">
                    Your resume has been uploaded, but it appears to be missing some important information. 
                    Please review and update your resume to include all necessary details.
                </p>

                <div class="status-badge status-incomplete">
                    ⚠️ Resume Incomplete
                </div>

                @if($score > 0)
                    <div class="score-section">
                        <div class="score-label">Completeness Score</div>
                        <div class="score-value">{{ $score }}<span style="font-size: 24px; color: #6c757d;">/100</span></div>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                @endif

                <div class="info-box">
                    <strong>📝 Recommended Improvements:</strong>
                    Make sure your resume includes:
                    <ul style="margin: 10px 0 0 20px; padding: 0;">
                        <li>Contact information (email, phone)</li>
                        <li>Work experience or skills</li>
                        <li>Educational background</li>
                        <li>Professional summary</li>
                    </ul>
                </div>

                <div style="text-align: center;">
                    <a href="{{ route('settings') }}" class="cta-button">Update Resume</a>
                </div>

            @else
                <p class="message">
                    Your resume verification status has been updated. Please check your dashboard for more details.
                </p>

                <div style="text-align: center;">
                    <a href="{{ route('dashboard') }}" class="cta-button">Go to Dashboard</a>
                </div>
            @endif

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="font-size: 13px; color: #6c757d; margin: 0;">
                    <strong>Need help?</strong> If you have any questions, please contact our support team.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>Job Portal Mandaluyong</strong><br>
                Connecting Job Seekers with Opportunities
            </p>
            <div class="footer-links">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('settings') }}">Settings</a>
                <a href="{{ route('contact.support') }}">Support</a>
            </div>
            <p style="margin: 15px 0 0 0; font-size: 11px; color: #adb5bd;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
