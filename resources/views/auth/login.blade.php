@php
    $setting = $setting ?? \Illuminate\Support\Facades\Cache::rememberForever('app_settings', function () {
        return \App\Models\Setting::latest()->first();
    }) ?? \App\Models\Setting::latest()->first();
    $companyName = $setting?->company_name ?? config('app.name', 'Dust2Glow');
    $companyLogoUrl = $setting?->company_logo_url ?? asset('public/assets/images/company_logo/brand_logo.png');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ $companyName }}</title>

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
            max-width: 460px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            box-shadow: 0 18px 45px rgba(19, 33, 60, 0.08);
        }

        .auth-brand {
            padding: 30px 30px 22px;
            text-align: center;
            border-bottom: 1px solid #eef1f5;
        }

        .auth-logo-wrap {
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-brand-logo {
            max-width: 210px;
            max-height: 60px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: inline-block;
        }

        .auth-logo {
            display: inline-flex;
            width: 54px;
            height: 54px;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            color: #ffffff;
            font-size: 22px;
            background: #0866e8;
            border-radius: 14px;
        }

        .auth-brand h1 {
            margin: 0;
            color: #13213c;
            font-size: 26px;
            font-weight: 700;
        }

        .auth-brand p {
            margin: 8px 0 0;
            color: #667085;
            font-size: 14px;
        }

        .auth-body {
            padding: 28px 30px 30px;
        }

        .form-group label {
            color: #13213c;
            font-size: 14px;
            font-weight: 600;
        }

        .input-icon {
            position: relative;
        }

        .input-icon > i {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #0866e8;
            transform: translateY(-50%);
            z-index: 2;
        }

        .input-icon .form-control {
            height: 48px;
            padding-left: 42px;
            border-color: #e1e7f0;
            border-radius: 8px;
        }

        .input-icon .form-control.has-toggle {
            padding-right: 44px;
        }

        .input-icon .form-control.has-toggle.is-invalid {
            background-image: none;
        }

        .password-toggle-btn {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 6px;
            color: #667085;
            cursor: pointer;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            line-height: 1;
            transition: color 0.15s ease-in-out;
        }

        .password-toggle-btn:hover,
        .password-toggle-btn:focus {
            color: #0866e8;
            outline: none;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
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
                @if(!empty($companyLogoUrl))
                    <div class="auth-logo-wrap">
                        <a href="{{ url('/') }}" aria-label="{{ $companyName }}">
                            <img src="{{ $companyLogoUrl }}" alt="{{ $companyName }}" class="auth-brand-logo">
                        </a>
                    </div>
                @else
                    <span class="auth-logo"><i class="fas fa-broom" aria-hidden="true"></i></span>
                @endif
                <h1>Welcome back</h1>
                <p>Log in to manage your cleaning bookings with {{ $companyName }}.</p>
            </div>

            <div class="auth-body">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="far fa-envelope" aria-hidden="true"></i>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between">
                            <label for="password">Password</label>
                        </div>
                        <div class="input-icon">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            <input type="password" class="form-control has-toggle @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter your password">
                            <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Show password" tabindex="-1">
                                <i class="far fa-eye" id="togglePasswordIcon" aria-hidden="true"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group d-flex align-items-center justify-content-between">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label text-muted" for="remember">Remember me</label>
                        </div>
                        <div>
                            <a href="#" class="auth-link small" data-toggle="modal" data-target="#forgotPasswordModal">Forgot password?</a>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block auth-btn">Login</button>

                    <p class="mt-4 mb-0 text-center text-muted">
                        New to {{ $companyName }}?
                        <a href="{{ route('register') }}" class="auth-link">Create an account</a>
                    </p>
                </form>
            </div>
        </section>
    </main>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content style-modal-card" style="border-radius: 14px; border: 1px solid #e8edf5;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold" id="forgotPasswordModalLabel" style="color: #13213c;">
                        <i class="fas fa-unlock-alt text-primary mr-2"></i> Forgot Password
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted small mb-3">
                            Enter your registered email address below and we'll send you a password reset link.
                        </p>
                        <div class="form-group mb-0">
                            <label for="forgot_email" style="color: #13213c; font-size: 14px; font-weight: 600;">Email Address</label>
                            <div class="input-icon">
                                <i class="far fa-envelope" aria-hidden="true"></i>
                                <input type="email" class="form-control" id="forgot_email" name="email" placeholder="Enter your registered email" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light font-weight-bold px-3" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4" style="background: #0866e8; border-color: #0866e8; border-radius: 8px;">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (togglePasswordBtn && passwordInput && toggleIcon) {
                togglePasswordBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    if (isPassword) {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                        togglePasswordBtn.setAttribute('aria-label', 'Hide password');
                    } else {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                        togglePasswordBtn.setAttribute('aria-label', 'Show password');
                    }
                });
            }
        });
    </script>
</body>
</html>
