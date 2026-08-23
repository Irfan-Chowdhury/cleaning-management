@extends('layouts.app')

@section('title', 'Company Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .settings-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.035);
            padding: 28px 32px;
            width: 100%;
        }

        .settings-card .card-title {
            color: #13213c;
            font-size: 20px;
            font-weight: 700;
        }

        .settings-card label {
            color: #13213c;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .settings-input-icon {
            position: relative;
        }

        .settings-input-icon i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #98a2b3;
            font-size: 15px;
            z-index: 4;
        }

        .settings-input-icon .form-control {
            padding-left: 44px;
            min-height: 48px;
            border-radius: 8px;
            border-color: #e1e7f0;
            font-size: 14px;
            color: #17233c;
        }

        .settings-input-icon .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }

        .logo-preview-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            display: inline-block;
        }

        .logo-preview-img {
            max-height: 50px;
            object-fit: contain;
        }

        .settings-field-error {
            color: #dc3545;
            display: block;
            font-size: 12px;
            margin-top: 6px;
        }
    </style>
@endpush

@section('content')
    @php
        $settings = $settings ?? new \App\Models\Setting();
    @endphp

    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <h1>Settings</h1>
                <p>Manage system parameters, rewards, and booking rules.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Settings Card Container -->
        <div class="settings-card mb-4">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                <h2 class="card-title mb-0">
                    <i class="fas fa-sliders-h text-primary mr-2"></i> Company &amp; System Configuration
                </h2>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form">
                @csrf

                <!-- Section 1: General Info -->
                <h5 class="text-dark font-weight-bold mb-3 pb-2 border-bottom" style="font-size: 16px;">
                    <i class="fas fa-building text-primary mr-2"></i> General Information
                </h5>

                <div class="row">
                    <!-- Company Name -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="company_name">Company Name <span class="text-danger">*</span></label>
                        <div class="settings-input-icon">
                            <i class="fas fa-building" aria-hidden="true"></i>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $settings->company_name) }}" placeholder="Enter Company Name" required>
                        </div>
                    </div>

                    <!-- Company Logo -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="company_logo">Company Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="company_logo" name="company_logo" accept="image/*">
                            <label class="custom-file-label" for="company_logo" style="min-height: 48px; padding-top: 12px; border-radius: 8px; border-color: #e1e7f0;">Choose logo file...</label>
                        </div>
                        <div class="mt-2" id="company-logo-preview-wrap" @if(empty($settings->company_logo)) style="display: none;" @endif>
                            <span class="text-muted small d-block mb-1">Current Logo Preview:</span>
                            <div class="logo-preview-box">
                                <img src="{{ $settings->company_logo_url }}" alt="Current Logo" class="logo-preview-img" id="company-logo-preview">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Credits & Rewards Configuration -->
                <h5 class="text-dark font-weight-bold mb-3 mt-2 pb-2 border-bottom" style="font-size: 16px;">
                    <i class="fas fa-gift text-primary mr-2"></i> Credits &amp; Rewards Configuration
                </h5>

                <div class="row">
                    <!-- Welcome Credit -->
                    <div class="col-md-4 form-group mb-4">
                        <label for="welcome_credit">Welcome Credit ($)</label>
                        <div class="settings-input-icon">
                            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                            <input type="number" step="0.01" class="form-control" id="welcome_credit" name="welcome_credit" value="{{ old('welcome_credit', $settings->welcome_credit) }}" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Referral Reward -->
                    <div class="col-md-4 form-group mb-4">
                        <label for="referral_reward">Referral Reward ($)</label>
                        <div class="settings-input-icon">
                            <i class="fas fa-user-friends" aria-hidden="true"></i>
                            <input type="number" step="0.01" class="form-control" id="referral_reward" name="referral_reward" value="{{ old('referral_reward', $settings->referral_reward) }}" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Google Review Reward -->
                    <div class="col-md-4 form-group mb-4">
                        <label for="google_review_reward">Google Review Reward ($)</label>
                        <div class="settings-input-icon">
                            <i class="fab fa-google" aria-hidden="true"></i>
                            <input type="number" step="0.01" class="form-control" id="google_review_reward" name="google_review_reward" value="{{ old('google_review_reward', $settings->google_review_reward) }}" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Booking Rules -->
                <h5 class="text-dark font-weight-bold mb-3 mt-2 pb-2 border-bottom" style="font-size: 16px;">
                    <i class="far fa-calendar-check text-primary mr-2"></i> Booking Rules &amp; Limits
                </h5>

                <div class="row">
                    <!-- Maximum Advance Booking Days -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="maximum_advance_booking_days">Maximum Advance Booking Days</label>
                        <div class="settings-input-icon">
                            <i class="far fa-calendar-alt" aria-hidden="true"></i>
                            <input type="number" step="1" min="1" class="form-control" id="maximum_advance_booking_days" name="maximum_advance_booking_days" value="{{ old('maximum_advance_booking_days', $settings->maximum_advance_booking_days) }}" placeholder="e.g. 30">
                        </div>
                        <small class="form-text text-muted">How many days in advance customers are allowed to schedule services.</small>
                    </div>

                    <!-- Cancellation Notice Hours -->
                    <div class="col-md-6 form-group mb-4">
                        <label for="cancellation_notice_hours">Cancellation Notice Hours</label>
                        <div class="settings-input-icon">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <input type="number" step="1" min="0" class="form-control" id="cancellation_notice_hours" name="cancellation_notice_hours" value="{{ old('cancellation_notice_hours', $settings->cancellation_notice_hours) }}" placeholder="e.g. 24">
                        </div>
                        <small class="form-text text-muted">Minimum hours notice required before canceling a booking.</small>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="pt-3 border-top d-flex align-items-center justify-content-end">
                    <button type="submit" class="btn btn-primary customers-primary-btn px-4 py-2" id="settings-submit-btn">
                        <i class="fas fa-save mr-1" aria-hidden="true"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Display filename in file input label on selection
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose logo file...');
            });

            $('#settings-form').on('submit', function(event) {
                event.preventDefault();

                var form = this;
                var $form = $(form);
                var $submit = $('#settings-submit-btn');
                var formData = new FormData(form);

                $('.settings-field-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1" aria-hidden="true"></i> Updating...');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.company_logo_url) {
                            $('#company-logo-preview').attr('src', response.company_logo_url);
                            $('#company-logo-preview-wrap').show();
                            $('#sidebar-company-logo').attr('src', response.company_logo_url);
                        }

                        $('#company_logo').val('');
                        $('.custom-file-label').removeClass('selected').html('Choose logo file...');

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            icon: 'success',
                            title: response.message || 'Company settings updated successfully!'
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                var $input = $('[name="' + field + '"]');
                                $input.addClass('is-invalid');
                                $input.closest('.form-group').append('<span class="settings-field-error">' + messages[0] + '</span>');
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation failed',
                                text: 'Please check the highlighted fields.'
                            });
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Update failed',
                            text: 'Something went wrong. Please try again.'
                        });
                    },
                    complete: function() {
                        $submit.prop('disabled', false).html('<i class="fas fa-save mr-1" aria-hidden="true"></i> Update');
                    }
                });
            });
        });
    </script>
@endpush
