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
            max-width: 860px;
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
            box-shadow: 0 6px 16px rgba(19, 33, 60, 0.12);
        }
        .btn-change-photo {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 18px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-change-photo:hover {
            background-color: #0866e8;
            color: #ffffff;
            border-color: #0866e8;
        }
        .profile-form .form-group label {
            font-size: 13.5px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .profile-form .form-control {
            min-height: 44px;
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
            color: #475569;
            cursor: not-allowed;
        }
        .gender-radio-group {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-top: 8px;
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
            padding: 8px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .gender-radio-custom:hover {
            border-color: #0866e8;
            background: #f0f7ff;
        }
        .gender-radio-custom input[type="radio"] {
            width: 16px;
            height: 16px;
            accent-color: #0866e8;
            cursor: pointer;
        }
        .referral-copy-btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-color: #cbd5e1;
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
            padding: 0 16px;
            height: 44px;
        }
        .referral-copy-btn:hover {
            background: #0866e8;
            color: #ffffff;
            border-color: #0866e8;
        }
        .readonly-tag {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }
        .copied-badge-popup {
            position: absolute;
            top: -34px;
            right: 0;
            background: #0f172a;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
            z-index: 100;
            pointer-events: none;
            white-space: nowrap;
        }
        .copied-badge-popup::after {
            content: '';
            position: absolute;
            bottom: -4px;
            right: 14px;
            width: 8px;
            height: 8px;
            background: #0f172a;
            transform: rotate(45deg);
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <h1>Profile Settings</h1>
                <p>Manage your account details, contact info, security, and profile photo.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show max-width-860 mx-auto mb-4" role="alert" style="max-width: 860px; border-radius: 10px;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show max-width-860 mx-auto mb-4" role="alert" style="max-width: 860px; border-radius: 10px;">
                <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Please correct the errors below:</strong>
                <ul class="mb-0 mt-1 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Profile Form Card -->
        <div class="profile-card">
            <form id="customer-profile-form" action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
                @csrf

                <!-- Profile Photo Section -->
                <div class="text-center mb-4 pb-4 border-bottom">
                    <div class="profile-avatar-wrapper">
                        <img id="avatar-preview" src="{{ $user->photo_url }}" alt="{{ $user->first_name }}" class="profile-avatar-img">
                    </div>
                    <div>
                        <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;">
                        <button type="button" id="btn-trigger-photo" class="btn btn-change-photo">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">Allowed formats: JPG, PNG, WEBP, GIF (Max 2MB)</small>
                </div>

                <!-- Personal Info Section -->
                <h6 class="font-weight-bold text-dark mb-3" style="font-size: 15px;">
                    <i class="fas fa-user-circle text-primary mr-1"></i> Personal Details
                </h6>

                <div class="row">
                    <!-- First Name Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="first_name">
                            First Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name', $user->first_name) }}" placeholder="First name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Last Name Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="last_name">
                            Last Name
                        </label>
                        <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name', $user->last_name) }}" placeholder="Last name">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="email">
                            <i class="fas fa-envelope text-primary mr-1"></i> Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" placeholder="example@domain.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="phone">
                            <i class="fas fa-phone-alt text-primary mr-1"></i> Phone Number
                        </label>
                        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gender Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label class="d-block">
                            <i class="fas fa-venus-mars text-primary mr-1"></i> Gender
                        </label>
                        @php $currentGender = strtolower(old('gender', $user->gender ?? '')); @endphp
                        <div class="gender-radio-group">
                            <label class="gender-radio-custom">
                                <input type="radio" name="gender" value="male" {{ $currentGender === 'male' ? 'checked' : '' }}>
                                <span>Male</span>
                            </label>
                            <label class="gender-radio-custom">
                                <input type="radio" name="gender" value="female" {{ $currentGender === 'female' ? 'checked' : '' }}>
                                <span>Female</span>
                            </label>
                            <label class="gender-radio-custom">
                                <input type="radio" name="gender" value="other" {{ $currentGender === 'other' ? 'checked' : '' }}>
                                <span>Other</span>
                            </label>
                        </div>
                    </div>

                    <!-- Referral Code Field (Read Only with Tooltip) -->
                    <div class="col-md-6 form-group mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="referral_code" class="mb-0">
                                <i class="fas fa-ticket-alt text-primary mr-1"></i> Referral Code
                            </label>
                            <span class="readonly-tag"><i class="fas fa-lock mr-1"></i> Read Only</span>
                        </div>
                        <div class="input-group">
                            <input type="text" id="referral_code" class="form-control font-weight-bold" value="{{ $user->referral_code }}" readonly>
                            <div class="input-group-append position-relative">
                                <button type="button" class="btn referral-copy-btn" id="btn-copy-referral"
                                        data-toggle="tooltip" data-placement="top" data-trigger="manual" title="Copied!">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <span id="copied-badge" class="copied-badge-popup" style="display: none;">Copied!</span>
                            </div>
                        </div>
                    </div>

                    <!-- Address Field -->
                    <div class="col-12 form-group mb-3">
                        <label for="address">
                            <i class="fas fa-map-marker-alt text-primary mr-1"></i> Address
                        </label>
                        <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Change Password Section -->
                <hr class="my-4">
                <h6 class="font-weight-bold text-dark mb-3" style="font-size: 15px;">
                    <i class="fas fa-lock text-primary mr-1"></i> Security &amp; Change Password
                </h6>

                <div class="row">
                    <!-- Password Field -->
                    <div class="col-md-6 form-group mb-3">
                        <label for="password">
                            New Password
                        </label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="Leave blank to keep current password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="password_confirmation">
                            Confirm Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                               placeholder="Re-enter new password">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="text-right pt-3 border-top">
                    <button type="submit" id="btn-submit-profile" class="btn btn-primary px-4" style="height: 44px; font-weight: 600; border-radius: 10px;">
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
            // Initialize Bootstrap Tooltip on Copy Button
            var $copyBtn = $('#btn-copy-referral');
            if (typeof $copyBtn.tooltip === 'function') {
                $copyBtn.tooltip({ trigger: 'manual', placement: 'top' });
            }

            // Trigger File Upload when clicking Change Photo
            $('#btn-trigger-photo').on('click', function () {
                $('#photo-input').click();
            });

            // Preview Uploaded Image
            $('#photo-input').on('change', function (event) {
                var file = event.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#avatar-preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Copy Referral Code to Clipboard & Show Tooltip/Popup Message
            $copyBtn.on('click', function () {
                var codeInput = document.getElementById('referral_code');
                if (codeInput && codeInput.value) {
                    navigator.clipboard.writeText(codeInput.value).then(function () {
                        // Show Bootstrap Tooltip if available
                        if (typeof $copyBtn.tooltip === 'function') {
                            $copyBtn.attr('data-original-title', 'Copied!').tooltip('show');
                            setTimeout(function () {
                                $copyBtn.tooltip('hide');
                            }, 1800);
                        }

                        // Show Popup Badge for instant visual feedback
                        $('#copied-badge').stop(true, true).fadeIn(200).delay(1500).fadeOut(300);
                    }).catch(function() {
                        // Fallback selection copy if clipboard API fails
                        codeInput.select();
                        document.execCommand('copy');
                        $('#copied-badge').stop(true, true).fadeIn(200).delay(1500).fadeOut(300);
                    });
                }
            });

            // Handle Profile Form Submit
            $('#customer-profile-form').on('submit', function (e) {
                var pwd = $('#password').val();
                var pwdConfirm = $('#password_confirmation').val();

                if (pwd && pwd !== pwdConfirm) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Password Mismatch',
                            text: 'New Password and Confirm Password do not match!'
                        });
                    } else {
                        alert('New Password and Confirm Password do not match!');
                    }
                    return false;
                }

                var $btn = $('#btn-submit-profile');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
            });
        });
    </script>
@endpush
