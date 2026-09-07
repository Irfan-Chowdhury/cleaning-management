@php
    $setting = \App\Models\Setting::first();
    $companyName = $setting?->company_name ?? config('app.name', 'Dust2Glow');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | {{ $companyName }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            color: #17233c;
            font-family: 'Inter', Arial, sans-serif;
            background: #f7f9fc;
        }

        .auth-page {
            min-height: 100vh;
            padding: 32px 15px;
        }

        .auth-card {
            width: 100%;
            max-width: 540px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            box-shadow: 0 18px 45px rgba(19, 33, 60, 0.08);
        }

        .auth-brand {
            padding: 32px 30px 22px;
            text-align: center;
            border-bottom: 1px solid #eef1f5;
        }

        .auth-logo {
            display: inline-flex;
            width: 58px;
            height: 58px;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            color: #ffffff;
            font-size: 24px;
            background: #0866e8;
            border-radius: 14px;
        }

        .auth-brand h1 {
            margin: 0;
            color: #13213c;
            font-size: 24px;
            font-weight: 700;
        }

        .auth-body {
            padding: 28px 30px 32px;
        }

        .verify-text {
            color: #475467;
            font-size: 15px;
            line-height: 1.6;
        }

        .auth-btn {
            min-height: 48px;
            font-weight: 700;
            background: #0866e8;
            border-color: #0866e8;
            border-radius: 8px;
        }

        .auth-btn:hover,
        .auth-btn:focus {
            background: #006ce5;
            border-color: #006ce5;
        }

        .auth-link {
            color: #0866e8;
            font-weight: 600;
        }

        .auth-link:hover {
            color: #006ce5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <main class="auth-page d-flex align-items-center justify-content-center">
        <section class="auth-card">
            <div class="auth-brand">
                <span class="auth-logo"><i class="far fa-envelope-open" aria-hidden="true"></i></span>
                <h1>Verify Your Email Address</h1>
            </div>

            <div class="auth-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <p class="verify-text">
                    Thanks for registering with {{ $companyName }}! Before getting started, please check your inbox and verify your email address by clicking on the link we just sent to you.
                </p>

                <p class="verify-text">
                    If you didn't receive the email, click the button below to request another verification link.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block auth-btn">Resend Verification Email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted" style="font-size: 14px; text-decoration: none;">
                        <i class="fas fa-sign-out-alt mr-1"></i> Log Out
                    </button>
                </form>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
