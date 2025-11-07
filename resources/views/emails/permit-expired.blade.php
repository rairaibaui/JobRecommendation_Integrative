<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Permit Expired</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 28px;">❌ Business Permit Expired</h1>
    </div>

    <div style="background-color: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <p style="font-size: 16px;">Dear {{ $user->first_name }} {{ $user->last_name }},</p>

        <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <p style="margin: 0; font-size: 16px; font-weight: bold; color: #721c24;">
                Your business permit has expired and requires immediate renewal!
            </p>
        </div>

        <p style="font-size: 15px; margin-bottom: 15px;">
            Your account has been temporarily restricted until you upload a valid, up-to-date business permit. You will not be able to post new job listings until this is resolved.
        </p>

        <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e0e0e0;">
            <h3 style="color: #dc3545; margin-top: 0;">📋 Expired Permit Details</h3>
            <p style="margin: 5px 0;"><strong>Company Name:</strong> {{ $user->company_name }}</p>
            <p style="margin: 5px 0;"><strong>Expiry Date:</strong> {{ $permit->permit_expiry_date->format('F d, Y') }}</p>
            <p style="margin: 5px 0;"><strong>Status:</strong> <span style="color: #dc3545; font-weight: bold;">Expired</span></p>
        </div>

        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="color: #0066cc; margin-top: 0;">🔧 How to Restore Your Account:</h4>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li style="margin-bottom: 8px;"><strong>Renew your business permit</strong> with your local government unit (Barangay/City Hall/DTI/SEC)</li>
                <li style="margin-bottom: 8px;"><strong>Upload the new permit</strong> to your account settings</li>
                <li style="margin-bottom: 8px;"><strong>Wait for verification</strong> (usually within 24 hours)</li>
                <li style="margin-bottom: 8px;"><strong>Resume posting jobs</strong> once approved</li>
            </ol>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/employer/settings') }}" 
               style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 16px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                Upload New Permit Now
            </a>
        </div>

        <div style="background-color: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #dc3545;">
            <p style="margin: 0; font-size: 14px; color: #721c24;">
                <strong>⚠️ Account Restrictions:</strong>
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; color: #721c24; font-size: 14px;">
                <li>Cannot create new job postings</li>
                <li>Existing job postings may be hidden from job seekers</li>
                <li>Cannot receive new applications</li>
            </ul>
        </div>

        <div style="background-color: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0c5460;">
            <p style="margin: 0; font-size: 14px; color: #0c5460;">
                <strong>💡 Need Help?</strong> Contact our support team if you need assistance with:
            </p>
            <ul style="margin: 10px 0; padding-left: 20px; color: #0c5460; font-size: 14px;">
                <li>Understanding acceptable business permit documents</li>
                <li>Uploading your permit</li>
                <li>Checking verification status</li>
            </ul>
        </div>

        <p style="font-size: 14px; color: #666; margin-top: 30px;">
            We appreciate your cooperation in maintaining valid business credentials on our platform.
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
