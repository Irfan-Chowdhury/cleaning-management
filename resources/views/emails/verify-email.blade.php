<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f4f6fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">

    <!-- Email Container Wrapper -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f6fb; padding: 40px 0;">
        <tr>
            <td align="center">

                <!-- Main Card (600px Max Width) -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">

                    <!-- Brand Header: Logo (if setting.company_logo exists) or Company Name -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #0866e8 0%, #004bb5 100%); padding: 36px 40px; text-align: center;">
                            @if($companyLogo)
                                <div style="display: inline-block; background-color: #ffffff; padding: 12px 24px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0,0,0,0.15);">
                                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-height: 55px; max-width: 220px; width: auto; height: auto; display: block; border: 0;" />
                                </div>
                            @else
                                <h1 style="color: #ffffff; font-size: 26px; font-weight: 800; margin: 0; letter-spacing: -0.5px;">{{ $companyName }}</h1>
                            @endif
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: left;">
                            <div style="display: inline-block; background-color: #eff6ff; color: #0866e8; border-radius: 50px; padding: 6px 16px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
                                Email Verification Required
                            </div>
                            <h2 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">
                                Hello {{ $userName ?: 'Valued Customer' }},
                            </h2>
                            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 20px;">
                                Thank you for registering with <strong>{{ $companyName }}</strong>! To complete your registration and secure your account, please confirm your email address by clicking the button below.
                            </p>

                            @if(!empty($welcomeCreditBonus) && $welcomeCreditBonus > 0)
                            <!-- Special Bonus Badge Box -->
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td width="36" valign="top" style="font-size: 24px; line-height: 1;">🎁</td>
                                        <td style="padding-left: 8px; font-size: 14px; color: #166534; line-height: 1.5;">
                                            <strong style="color: #15803d; font-size: 15px;">Welcome Reward Waiting!</strong><br>
                                            Verify your email now to receive a <strong>{{ $currencySymbol }}{{ number_format($welcomeCreditBonus, 2) }} welcome credit</strong> directly into your wallet!
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Call To Action Button -->
                    <tr>
                        <td align="center" style="padding: 0 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td align="center" style="border-radius: 10px; background-color: #0866e8; box-shadow: 0 4px 14px rgba(8, 102, 232, 0.35);">
                                        <a href="{{ $url }}" target="_blank" style="font-size: 16px; font-weight: 700; color: #ffffff; text-decoration: none; padding: 15px 36px; display: inline-block; border-radius: 10px; border: 1px solid #0866e8;">
                                            Verify Email Address &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Security & Fallback Link -->
                    <tr>
                        <td style="padding: 0 40px 32px; font-size: 13px; line-height: 1.6; color: #64748b;">
                            <p style="margin: 0 0 16px;">
                                If you did not register for an account on {{ $companyName }}, no further action is required and you can safely disregard this email.
                            </p>
                            <div style="background-color: #f8fafc; border-radius: 8px; padding: 14px; border: 1px dashed #cbd5e1; word-break: break-all;">
                                <span style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 4px; font-weight: 600; text-transform: uppercase;">Having trouble clicking the button?</span>
                                <a href="{{ $url }}" style="color: #0866e8; text-decoration: underline; font-size: 12px;">{{ $url }}</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                            <p style="margin: 0 0 6px; font-weight: 600; color: #64748b;">
                                &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
                            </p>
                            @if(!empty($supportEmail) || !empty($supportPhone))
                            <p style="margin: 0 0 6px;">
                                Need help? Contact us at
                                @if(!empty($supportEmail)) <a href="mailto:{{ $supportEmail }}" style="color: #0866e8; text-decoration: none;">{{ $supportEmail }}</a> @endif
                                @if(!empty($supportEmail) && !empty($supportPhone)) | @endif
                                @if(!empty($supportPhone)) <a href="tel:{{ $supportPhone }}" style="color: #0866e8; text-decoration: none;">{{ $supportPhone }}</a> @endif
                            </p>
                            @endif
                            <p style="margin: 0; font-size: 11px; color: #cbd5e1;">
                                This is an automated email. Please do not reply directly to this email address.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Main Card -->

            </td>
        </tr>
    </table>
    <!-- /Email Container Wrapper -->

</body>
</html>
