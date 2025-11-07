<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Permit Verification</title>
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
            background: linear-gradient(135deg, #334A5E, #648EB5);
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
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .status-approved {
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
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #334A5E;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: #334A5E;
        }
        .cta-button {
            display: inline-block;
            background: #334A5E;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .cta-button:hover {
            background: #648EB5;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .verified-badge {
            display: inline-block;
            background: #e3f8ef;
            color: #0f5132;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        ul {
            line-height: 1.8;
            color: #495057;
        }
        .confidence-bar {
            background: #e9ecef;
            height: 24px;
            border-radius: 12px;
            overflow: hidden;
            margin: 10px 0;
        }
        .confidence-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">
                @if($isApproved)
                    ✅
                @elseif($isRejected)
                    ❌
                @else
                    ⚠️
                @endif
            </div>
            <h1>Business Permit Verification Update</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 16px; color: #495057;">
                Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
            </p>

            <p style="color: #6c757d; line-height: 1.6;">
                We have completed the verification of your business permit for <strong>{{ $user->company_name }}</strong>.
                @if($isApproved)
                    <span class="verified-badge">✔ Verified</span>
                @endif
            </p>

            <!-- Status Badge -->
            @if($isApproved)
                <div class="status-badge status-approved">
                    ✅ Business Permit Approved
                </div>

                <div class="info-box">
                    <strong>🎉 Congratulations!</strong>
                    Your business permit has been verified and approved. You can now:
                    <ul>
                        <li>Post job openings</li>
                        <li>Manage applications</li>
                        <li>Access all employer features</li>
                    </ul>
                </div>

                @if($validation->confidence_score)
                    <p style="font-size: 13px; color: #6c757d; margin-top: 15px;">
                        <strong>Confidence Score:</strong>
                    </p>
                    <div class="confidence-bar">
                        <div class="confidence-fill" style="width: {{ $validation->confidence_score }}%">
                            {{ $validation->confidence_score }}% Confidence
                        </div>
                    </div>
                @endif

                <a href="{{ config('app.url') }}/employer/dashboard" class="cta-button">
                    Go to Dashboard →
                </a>

            @elseif($isRejected)
                <div class="status-badge status-rejected">
                    ❌ Business Permit Rejected
                </div>

                <div class="info-box">
                    <strong>⚠️ Verification Failed</strong>
                    Unfortunately, we could not verify your business permit. 
                    
                    @if($validation->reason)
                        <p style="margin-top: 10px; color: #721c24;">
                            <strong>Reason:</strong> {{ $validation->reason }}
                        </p>
                    @endif
                </div>

                <p style="color: #495057; line-height: 1.6;">
                    <strong>Next Steps:</strong>
                </p>
                <ul>
                    <li>Please upload a clear, valid Philippine business permit (DTI, SEC, or Barangay clearance)</li>
                    <li>Ensure the document shows your business name, registration number, and official seals</li>
                    <li>Upload in PDF, JPG, or PNG format (max 5MB)</li>
                </ul>

                <a href="{{ config('app.url') }}/settings" class="cta-button">
                    Re-upload Business Permit →
                </a>

            @else
                <div class="status-badge status-review">
                    ⚠️ Manual Review Required
                </div>

                <div class="info-box">
                    <strong>👁️ Under Review</strong>
                    Your business permit requires manual verification by our team.
                    
                    @if($validation->reason)
                        <p style="margin-top: 10px; color: #856404;">
                            <strong>Reason:</strong> {{ $validation->reason }}
                        </p>
                    @endif
                </div>

                @if($validation->confidence_score)
                    <p style="font-size: 13px; color: #6c757d; margin-top: 15px;">
                        <strong>Confidence Score:</strong>
                    </p>
                    <div class="confidence-bar">
                        <div class="confidence-fill" style="width: {{ $validation->confidence_score }}%; background: linear-gradient(90deg, #ffc107, #ff9800);">
                            {{ $validation->confidence_score }}% Confidence
                        </div>
                    </div>
                @endif

                <p style="color: #495057; line-height: 1.6;">
                    <strong>What happens next?</strong>
                </p>
                <ul>
                    <li>Our admin team will review your business permit within 24-48 hours</li>
                    <li>You'll receive another email once the review is complete</li>
                    <li>In the meantime, you can update your company profile</li>
                </ul>

                <a href="{{ config('app.url') }}/employer/dashboard" class="cta-button">
                    View Dashboard →
                </a>
            @endif

            <!-- Validation Details -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p style="font-size: 13px; color: #6c757d;">
                    <strong>Validation Details:</strong><br>
                    Document Type: Business Permit<br>
                    Validated By: {{ $validation->validated_by === 'ai' ? 'Automated System' : ucfirst($validation->validated_by) }}<br>
                    Validation Date: {{ $validation->validated_at ? $validation->validated_at->format('F d, Y \a\t g:i A') : 'Pending' }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>Job Portal Mandaluyong</strong><br>
                This is an automated message. Please do not reply to this email.
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ config('app.url') }}" style="color: #334A5E; text-decoration: none;">Visit Website</a> |
                <a href="{{ config('app.url') }}/contact" style="color: #334A5E; text-decoration: none;">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>
