<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Verification Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 30px; background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%); text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 600;">
                                <i style="font-size: 32px;">📱</i><br>
                                Phone Verification
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                You requested to verify your phone number: <strong>{{ $phone }}</strong>
                            </p>

                            <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 1.6; color: #333333;">
                                Use the verification code below:
                            </p>

                            <!-- OTP Code Box -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                                <tr>
                                    <td align="center" style="padding: 20px; background-color: #f8f9fa; border: 2px dashed #648EB5; border-radius: 8px;">
                                        <span style="font-size: 36px; font-weight: 700; color: #334A5E; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                            {{ $otp }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <div style="padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 20px; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #856404;">
                                    <strong>⚠️ Important:</strong> This code expires in <strong>10 minutes</strong>.
                                </p>
                            </div>

                            <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 1.6; color: #666666;">
                                If you didn't request this verification, please ignore this email or contact support if you have concerns.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px; background-color: #f8f9fa; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666666;">
                                Job Portal Mandaluyong
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                This is an automated message. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
