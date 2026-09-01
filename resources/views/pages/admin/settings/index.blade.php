@extends('layouts.app')

@section('title', 'Company Settings')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/settings.css') }}">
@endpush

@section('content')
    @php
        $settings = $settings ?? new \App\Models\Setting();
    @endphp

    <div class="settings-page">
        <div class="settings-header">
            <div>
                <h1>Settings</h1>
                <p>Manage company details, booking limits, rewards, and promotion rules.</p>
            </div>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form" class="settings-shell">
            @csrf

            <div class="settings-toolbar">
                <div>
                    <h2>Company and System Configuration</h2>
                    <p>These values control booking and customer reward behavior across the app.</p>
                </div>
                <button type="submit" class="btn btn-primary settings-submit-btn" id="settings-submit-btn">
                    <i class="fas fa-save" aria-hidden="true"></i> Update
                </button>
            </div>

            <section class="settings-section">
                <div class="settings-section-heading">
                    <span class="settings-section-icon"><i class="fas fa-building" aria-hidden="true"></i></span>
                    <div>
                        <h3>Company Information</h3>
                        <p>Public business identity and default regional settings.</p>
                    </div>
                </div>

                <div class="settings-grid">
                    <div class="form-group">
                        <label for="company_name">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                               value="{{ old('company_name', $settings->company_name) }}" placeholder="Clean Manage Pro">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="{{ old('phone', $settings->phone) }}" placeholder="+1 555 014 8821">
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email', $settings->email) }}" placeholder="support@example.com">
                    </div>

                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select class="form-control" id="timezone" name="timezone">
                            <option value="">Select timezone...</option>
                            @foreach ($timezoneOptions as $timezoneOption)
                                <option value="{{ $timezoneOption['zone'] }}"
                                    {{ old('timezone', $settings->timezone) === $timezoneOption['zone'] ? 'selected' : '' }}>
                                    {{ $timezoneOption['diff_from_GMT'] . ' - ' . $timezoneOption['zone'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <input type="text" class="form-control text-uppercase" id="currency" name="currency"
                               value="{{ old('currency', $settings->currency) }}" placeholder="USD" maxlength="3">
                    </div>

                    <div class="form-group settings-logo-field">
                        <label for="company_logo">Company Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="company_logo" name="company_logo" accept="image/*">
                            <label class="custom-file-label" for="company_logo">Choose logo file...</label>
                        </div>
                        <div class="settings-logo-preview" id="company-logo-preview-wrap" @if(empty($settings->company_logo)) style="display: none;" @endif>
                            <img src="{{ $settings->company_logo_url }}" alt="Current Logo" id="company-logo-preview">
                        </div>
                    </div>

                    <div class="form-group settings-grid-full">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                                  placeholder="Business address">{{ old('address', $settings->address) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="settings-section">
                <div class="settings-section-heading">
                    <span class="settings-section-icon"><i class="far fa-calendar-check" aria-hidden="true"></i></span>
                    <div>
                        <h3>Booking Configuration</h3>
                        <p>Control booking amount limits and customer scheduling rules.</p>
                    </div>
                </div>

                <div class="settings-grid">
                    <div class="form-group">
                        <label for="minimum_booking_amount">Minimum Booking Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="minimum_booking_amount"
                               name="minimum_booking_amount" value="{{ old('minimum_booking_amount', $settings->minimum_booking_amount) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="maximum_booking_amount">Maximum Booking Amount</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="maximum_booking_amount"
                               name="maximum_booking_amount" value="{{ old('maximum_booking_amount', $settings->maximum_booking_amount) }}" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="maximum_advance_booking_days">Maximum Advance Booking Days</label>
                        <input type="number" step="1" min="1" class="form-control" id="maximum_advance_booking_days"
                               name="maximum_advance_booking_days" value="{{ old('maximum_advance_booking_days', $settings->maximum_advance_booking_days) }}" placeholder="30">
                    </div>

                    <div class="form-group">
                        <label for="cancellation_notice_hours">Cancellation Notice Hours</label>
                        <input type="number" step="1" min="0" class="form-control" id="cancellation_notice_hours"
                               name="cancellation_notice_hours" value="{{ old('cancellation_notice_hours', $settings->cancellation_notice_hours) }}" placeholder="24">
                    </div>
                </div>
            </section>

            <section class="settings-section">
                <div class="settings-section-heading">
                    <span class="settings-section-icon"><i class="fas fa-gift" aria-hidden="true"></i></span>
                    <div>
                        <h3>Customer and Reward Configuration</h3>
                        <p>Set credit and reward amounts, then enable or disable each reward type.</p>
                    </div>
                </div>

                <div class="settings-grid settings-reward-grid">
                    <div class="settings-toggle-group">
                        <div class="form-group mb-0">
                            <label for="welcome_credit">Welcome Credit</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="welcome_credit"
                                   name="welcome_credit" value="{{ old('welcome_credit', $settings->welcome_credit) }}" placeholder="0.00">
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="welcome_credit_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="welcome_credit_enabled"
                                   name="welcome_credit_enabled" value="1" {{ old('welcome_credit_enabled', $settings->welcome_credit_enabled) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="welcome_credit_enabled">Enabled</label>
                        </div>
                    </div>

                    <div class="settings-toggle-group">
                        <div class="form-group mb-0">
                            <label for="referral_reward">Referral Reward</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="referral_reward"
                                   name="referral_reward" value="{{ old('referral_reward', $settings->referral_reward) }}" placeholder="0.00">
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="referral_reward_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="referral_reward_enabled"
                                   name="referral_reward_enabled" value="1" {{ old('referral_reward_enabled', $settings->referral_reward_enabled) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="referral_reward_enabled">Enabled</label>
                        </div>
                    </div>

                    <div class="settings-toggle-group">
                        <div class="form-group mb-0">
                            <label for="google_review_reward">Google Review Reward</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="google_review_reward"
                                   name="google_review_reward" value="{{ old('google_review_reward', $settings->google_review_reward) }}" placeholder="0.00">
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="google_review_enabled" value="0">
                            <input type="checkbox" class="custom-control-input" id="google_review_enabled"
                                   name="google_review_enabled" value="1" {{ old('google_review_enabled', $settings->google_review_enabled) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="google_review_enabled">Enabled</label>
                        </div>
                    </div>
                </div>
            </section>

            <section class="settings-section">
                <div class="settings-section-heading">
                    <span class="settings-section-icon"><i class="fas fa-tags" aria-hidden="true"></i></span>
                    <div>
                        <h3>Promotion Configuration</h3>
                        <p>Limit how often promotions can be used globally and per customer.</p>
                    </div>
                </div>

                <div class="settings-grid">
                    <div class="form-group">
                        <label for="promotion_max_uses">Promotion Max Uses</label>
                        <input type="number" step="1" min="0" class="form-control" id="promotion_max_uses"
                               name="promotion_max_uses" value="{{ old('promotion_max_uses', $settings->promotion_max_uses) }}" placeholder="500">
                    </div>

                    <div class="form-group">
                        <label for="promotion_max_uses_per_customer">Promotion Max Uses Per Customer</label>
                        <input type="number" step="1" min="0" class="form-control" id="promotion_max_uses_per_customer"
                               name="promotion_max_uses_per_customer" value="{{ old('promotion_max_uses_per_customer', $settings->promotion_max_uses_per_customer) }}" placeholder="1">
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#timezone').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Select timezone...',
                allowClear: false
            });

            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass('selected').html(fileName || 'Choose logo file...');
            });

            $('#currency').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });

            $('#settings-form').on('submit', function(event) {
                event.preventDefault();

                var form = this;
                var $form = $(form);
                var $submit = $('#settings-submit-btn');
                var formData = new FormData(form);

                $('.settings-field-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                $submit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Updating...');

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
                                var $input = $('[name="' + field + '"]').last();
                                $input.addClass('is-invalid');
                                $input.closest('.form-group, .settings-toggle-group').append('<span class="settings-field-error">' + messages[0] + '</span>');
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
                        $submit.prop('disabled', false).html('<i class="fas fa-save" aria-hidden="true"></i> Update');
                    }
                });
            });
        });
    </script>
@endpush
