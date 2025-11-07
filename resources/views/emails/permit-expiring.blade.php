<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Permit Expiring Soon</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">⚠️ Business Permit Expiring Soon</h1>
    </div>

    <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <p style="font-size: 16px;">Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px; font-weight: bold; color: #856404;">
                Your business permit expires in {{ $daysRemaining }} day{{ $daysRemaining > 1 ? 's' : '' }}!
            </p>
        </div>

        <p style="font-size: 15px; margin-bottom: 15px;">
            To ensure uninterrupted access to job posting services, please upload your renewed business permit before it expires.
        </p>

        <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e0e0e0;">
            <h3 style="color: #667eea; margin-top: 0;">📋 Current Permit Details</h3>
            <p style="margin: 5px 0;"><strong>Company Name:</strong> {{ $user->company_name }}</p>
            <p style="margin: 5px 0;"><strong>Expiry Date:</strong> {{ $permit->permit_expiry_date->format('F d, Y') }}</p>
            <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #ffc107; font-weight: bold;">Expiring Soon</span></p>
        </div>

        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="color: #0066cc; margin-top: 0;">📝 What You Need to Do:</h4>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;">Renew your business permit with your local government</li>
                <li style="margin-bottom: 8px;">Log in to your account</li>
                <li style="margin-bottom: 8px;">Go to Settings → Upload your new business permit</li>
                <li style="margin-bottom: 8px;">Wait for verification (usually within 24 hours)</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/employer/settings') }}" 
               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                Upload New Permit Now
            </a>
        </div>

        <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <p style="margin: 0; font-size: 14px; color: #856404;">
                <strong>⏰ Important:</strong> After your permit expires, you will not be able to post new job listings until a valid permit is uploaded and verified.
            </p>
        </div>

        <p style="font-size: 14px; color: #666; margin-top: 30px;">
            If you have any questions or need assistance, please contact our support team.
        </p>

        <p style="font-size: 14px; margin-top: 20px;">
            Best regards,<br>
            <strong>The Job Recommendation Platform Team</strong>
        </p>
    </div>

    <div style="text-align: center; padding: 20px; color: #999; font-size: 12px;">
        <p style="margin: 5px 0;">This is an automated notification from Job Recommendation Platform</p>
        <p style="margin: 5px 0;">Please do not reply to this email</p>
    </div>
</body>
</html>
