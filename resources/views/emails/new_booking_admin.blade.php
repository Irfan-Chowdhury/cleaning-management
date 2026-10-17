<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6fb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f4f6fb; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background: #0866e8; padding: 25px 30px; text-align: center;">
                            <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: -0.3px;">{{ $companyName }}</h1>
                            <p style="color: #dbeafe; font-size: 13px; margin: 4px 0 0 0;">New Booking Request Notification</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 30px 30px 20px; text-align: left;">
                            <p style="font-size: 15px; line-height: 1.5; color: #334155; margin: 0 0 18px;">
                                Hello Admin,
                            </p>
                            <p style="font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 20px;">
                                A new booking request has been submitted by <strong>{{ $booking->customer_name }}</strong>. Here are the booking details:
                            </p>

                            <!-- Booking Details Table -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; width: 35%; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;">Booking ID</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 700; font-size: 14px; color: #0866e8;">#{{ $booking->id }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;">Service</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b; font-weight: 600;">{{ $serviceName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;">Customer</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b;">
                                        <strong>{{ $booking->customer_name }}</strong><br>
                                        <a href="mailto:{{ $booking->customer_email }}" style="color: #0866e8; text-decoration: none; font-size: 13px;">{{ $booking->customer_email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;">Phone</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b;">
                                        {{ $booking->customer_phone ?: 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase;">Date & Time</td>
                                    <td style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #1e293b;">
                                        {{ $formattedDate }} at {{ $formattedTime }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 16px; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase; vertical-align: top; {{ $booking->special_instructions ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">Address</td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #1e293b; {{ $booking->special_instructions ? 'border-bottom: 1px solid #e2e8f0;' : '' }}">
                                        {{ $fullAddress ?: 'N/A' }}
                                    </td>
                                </tr>
                                @if(!empty($booking->special_instructions))
                                <tr>
                                    <td style="padding: 12px 16px; font-weight: 600; font-size: 13px; color: #64748b; text-transform: uppercase; vertical-align: top;">Instructions</td>
                                    <td style="padding: 12px 16px; font-size: 14px; color: #475569; font-style: italic;">
                                        {{ $booking->special_instructions }}
                                    </td>
                                </tr>
                                @endif
                            </table>

                            <p style="font-size: 14px; line-height: 1.5; color: #64748b; margin: 0 0 24px;">
                                Please review this booking request in the admin panel to approve the schedule or manage the booking details.
                            </p>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 0 30px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                                <tr>
                                    <td align="center" style="border-radius: 8px; background-color: #0866e8;">
                                        <a href="{{ $actionUrl }}" target="_blank" style="font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none; padding: 12px 28px; display: inline-block; border-radius: 8px; border: 1px solid #0866e8;">
                                            View & Manage Booking
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 18px 30px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                            <p style="margin: 0;">
                                This is an automated notification from <strong>{{ $companyName }}</strong>.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
