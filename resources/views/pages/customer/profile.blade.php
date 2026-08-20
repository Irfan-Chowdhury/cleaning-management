@extends('layouts.app')

@section('title', 'Profile Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .profile-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0 4px 18px rgba(19, 33, 60, 0.03);
            max-width: 800px;
            margin: 0 auto;
        }
        .profile-avatar-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 12px;
        }
        .profile-avatar-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }
        .btn-change-photo {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 16px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-change-photo:hover {
            background-color: #0866e8;
            color: #ffffff;
            border-color: #0866e8;
        }
        .profile-form .form-group label {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        .profile-form .form-control {
            height: 44px;
            font-size: 14px;
            border-radius: 10px;
            border-color: #cbd5e1;
            padding: 10px 14px;
        }
        .profile-form .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }
        .profile-form .form-control[readonly] {
            background-color: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }
        .gender-radio-group {
            display: flex;
            align-items: center;
            gap: 24px;
            padding-top: 6px;
        }
        .gender-radio-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin: 0;
        }
        .gender-radio-custom input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #0866e8;
            cursor: pointer;
        }
        .readonly-tag {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <h1>Profile Settings</h1>
                <p>Manage your account details, contact info, security, and preferences.</p>
            </div>
        </div>

        <!-- Profile Form Card (Wireframe Lines 505-548) -->
        <div class="profile-card">
            <form id="customer-profile-form" action="#" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Customer Photo Section (Wireframe Line 514-515) -->
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="profile-avatar-wrapper">
                        <img id="avatar-preview" src="{{ $user->avatar }}" alt="{{ $user->name }}" class="profile-avatar-img">
                    </div>
                    <div>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display: none;">
                        <button type="button" id="btn-trigger-avatar" class="btn btn-change-photo">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                    </div>
                </div>

                <!-- Name Field (Wireframe Line 517-518) -->
                <div class="form-group mb-3">
                    <label for="profile-name">
                        <i class="fas fa-user text-primary mr-1"></i> Full Name
                    </label>
                    <input type="text" id="profile-name" name="name" class="form-control" value="{{ $user->name }}" placeholder="Enter your full name" required>
                </div>

                <!-- Email Field (Wireframe Line 520-521) -->
                <div class="form-group mb-3">
                    <label for="profile-email">
                        <i class="fas fa-envelope text-primary mr-1"></i> Email Address
                    </label>
                    <input type="email" id="profile-email" name="email" class="form-control" value="{{ $user->email }}" placeholder="Enter your email address" required>
                </div>

                <!-- Phone Field (Wireframe Line 523-524) -->
                <div class="form-group mb-3">
                    <label for="profile-phone">
                        <i class="fas fa-phone-alt text-primary mr-1"></i> Phone Number
                    </label>
                    <input type="text" id="profile-phone" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="Enter your phone number">
                </div>

                <!-- Gender Field (Wireframe Line 526-527) -->
                <div class="form-group mb-3">
                    <label class="d-block">
                        <i class="fas fa-venus-mars text-primary mr-1"></i> Gender
                    </label>
                    <div class="gender-radio-group">
                        <label class="gender-radio-custom">
                            <input type="radio" name="gender" value="male" {{ strtolower($user->gender) === 'male' ? 'checked' : '' }}>
                            <span>Male</span>
                        </label>
                        <label class="gender-radio-custom">
                            <input type="radio" name="gender" value="female" {{ strtolower($user->gender) === 'female' ? 'checked' : '' }}>
                            <span>Female</span>
                        </label>
                    </div>
                </div>

                <!-- Referral Code Field (Read Only) (Wireframe Line 529-530) -->
                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="profile-referral-code" class="mb-0">
                            <i class="fas fa-ticket-alt text-primary mr-1"></i> Referral Code
                        </label>
                        <span class="readonly-tag"><i class="fas fa-lock mr-1"></i> Read Only</span>
                    </div>
                    <input type="text" id="profile-referral-code" class="form-control font-weight-bold" value="{{ $user->referral_code }}" readonly>
                </div>

                <!-- Change Password Section -->
                <hr class="my-4">
                <h6 class="font-weight-bold text-dark mb-3" style="font-size: 14.5px;">
                    <i class="fas fa-lock text-primary mr-1"></i> Security &amp; Change Password
                </h6>

                <div class="row">
                    <!-- Password Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="profile-password">
                            New Password
                        </label>
                        <input type="password" id="profile-password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="profile-password-confirmation">
                            Confirm Password
                        </label>
                        <input type="password" id="profile-password-confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter new password">
                    </div>
                </div>

                <!-- Action Button (Wireframe Line 532) -->
                <div class="text-right pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4" style="height: 44px; font-weight: 600; border-radius: 10px;">
                        <i class="fas fa-save mr-1"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Trigger File Upload when clicking Change Photo
            $('#btn-trigger-avatar').on('click', function () {
                $('#avatar-input').click();
            });

            // Preview Uploaded Image
            $('#avatar-input').on('change', function (event) {
                var file = event.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#avatar-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Handle Profile Form Submit (Dummy Handler)
            $('#customer-profile-form').on('submit', function (e) {
                e.preventDefault();
                var pwd = $('#profile-password').val();
                var pwdConfirm = $('#profile-password-confirmation').val();

                if (pwd && pwd !== pwdConfirm) {
                    alert('Password and Confirm Password do not match!');
                    return;
                }

                alert('Profile updated successfully!');
            });
        });
    </script>
@endpush
