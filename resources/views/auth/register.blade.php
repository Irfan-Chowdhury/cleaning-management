<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Dust2Glow</title>

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
            max-width: 620px;
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

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 14px;
            color: #0866e8;
            transform: translateY(-50%);
        }

        .input-icon .form-control {
            height: 48px;
            padding-left: 42px;
            border-color: #e1e7f0;
            border-radius: 8px;
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

        @media (max-width: 575.98px) {
            .auth-brand,
            .auth-body {
                padding-right: 20px;
                padding-left: 20px;
            }
        }
    </style>
</head>
<body>
    <main class="auth-page d-flex align-items-center justify-content-center">
        <section class="auth-card">
            <div class="auth-brand">
                <span class="auth-logo"><i class="fas fa-spray-can" aria-hidden="true"></i></span>
                <h1>Create your account</h1>
                <p>Book trusted cleaners and manage every visit from one place.</p>
            </div>

            <div class="auth-body">
                <form method="POST" action="#">
                    @csrf

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Full Name</label>
                            <div class="input-icon">
                                <i class="far fa-user" aria-hidden="true"></i>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="phone">Phone Number</label>
                            <div class="input-icon">
                                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="far fa-envelope" aria-hidden="true"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="password">Password</label>
                            <div class="input-icon">
                                <i class="fas fa-lock" aria-hidden="true"></i>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Create password">
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="input-icon">
                                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
                            </div>
                        </div>
                    </div>

                    <div class="custom-control custom-checkbox mb-4">
                        <input type="checkbox" class="custom-control-input" id="terms" name="terms">
                        <label class="custom-control-label text-muted" for="terms">
                            I agree to the terms and privacy policy.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block auth-btn">Create Account</button>

                    <p class="mt-4 mb-0 text-center text-muted">
                        Already have an account?
                        <a href="#" class="auth-link">Login</a>
                    </p>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
