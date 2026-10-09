<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Invited to {{ $companyName }}!</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #334155;
            line-height: 1.6;
        }
        table {
            border-collapse: collapse;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7fa;
            padding: 40px 0;
        }
        .main-card {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0866e8 0%, #004fb6 100%);
            padding: 36px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .header p {
            color: #e0f2fe;
            font-size: 14px;
            margin: 6px 0 0 0;
            font-weight: 400;
        }
        .body-content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .intro-text {
            font-size: 15px;
            color: #475569;
            margin-bottom: 24px;
        }
        .reward-card {
            background-color: #f0f6fe;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
            text-align: center;
        }
        .reward-card .badge {
            display: inline-block;
            background-color: #0866e8;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .reward-card h2 {
            margin: 0 0 8px 0;
            color: #1e3a8a;
            font-size: 22px;
            font-weight: 700;
        }
        .reward-card p {
            margin: 0;
            color: #3b82f6;
            font-size: 14px;
            font-weight: 500;
        }
        .personal-message-box {
            background-color: #f8fafc;
            border-left: 4px solid #0866e8;
            border-radius: 0 8px 8px 0;
            padding: 18px 20px;
            margin-bottom: 28px;
        }
        .personal-message-title {
            font-size: 13px;
            font-weight: 700;
            color: #0866e8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .personal-message-text {
            font-size: 14px;
            font-style: italic;
            color: #334155;
            margin: 0;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0;
        }
        .cta-button {
            display: inline-block;
            background-color: #0866e8;
            color: #ffffff !important;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(8, 102, 232, 0.35);
        }
        .steps-container {
            border-top: 1px solid #f1f5f9;
            padding-top: 28px;
            margin-top: 28px;
        }
        .steps-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            text-align: center;
        }
        .step-item {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .step-number {
            display: table-cell;
            width: 28px;
            height: 28px;
            background-color: #eff6ff;
            color: #0866e8;
            font-weight: 700;
            font-size: 13px;
            border-radius: 50%;
            text-align: center;
            vertical-align: middle;
        }
        .step-text {
            display: table-cell;
            padding-left: 12px;
            font-size: 13.5px;
            color: #475569;
            vertical-align: middle;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 4px 0;
        }
        .footer a {
            color: #0866e8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-card">
            <!-- Header -->
            <div class="header">
                <h1>{{ $companyName }}</h1>
                <p>Exclusive Referral Invitation</p>
            </div>

            <!-- Body Content -->
            <div class="body-content">
                <div class="greeting">Hello!</div>
                <div class="intro-text">
                    <strong>{{ $senderName }}</strong> thinks you would love <strong>{{ $companyName }}</strong> for your home or office cleaning needs and has sent you an exclusive invitation!
                </div>

                <!-- Reward Highlight Card -->
                <div class="reward-card">
                    <span class="badge">Special Gift For You</span>
                    <h2>Claim Your {{ $formattedReward }} Reward Credit</h2>
                    <p>Register today and receive {{ $formattedReward }} in wallet credits towards your first booking!</p>
                </div>

                @if(!empty($customMessage))
                    <!-- Personal Note -->
                    <div class="personal-message-box">
                        <div class="personal-message-title">Personal Note from {{ $senderName }}</div>
                        <p class="personal-message-text">"{{ $customMessage }}"</p>
                    </div>
                @endif

                <!-- Call to Action -->
                <div class="cta-container">
                    <a href="{{ $registrationUrl }}" class="cta-button" target="_blank">
                        Accept Invitation &amp; Register Now
                    </a>
                </div>

                <!-- Simple How It Works Steps -->
                <div class="steps-container">
                    <div class="steps-title">How to Redeem Your Reward:</div>
                    
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-text">Click the button above to register your new account.</div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-text">Your <strong>{{ $formattedReward }}</strong> credit will automatically be ready for your first booking.</div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-text">Schedule your preferred cleaning service and enjoy a sparkling fresh home!</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Sent with ❤️ by <strong>{{ $companyName }}</strong></p>
                <p>Need help? Visit <a href="{{ url('/') }}" target="_blank">{{ config('app.url', 'our website') }}</a> or reply to this email.</p>
                <p style="margin-top: 12px; font-size: 11px; color: #cbd5e1;">
                    If you received this email by mistake, please disregard it.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
