@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('pages.booking-service.partials.page-header')
        @include('pages.booking-service.partials.progress', ['currentStep' => 3])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 3 of 4: Your Details</h2>
                        <p>Please provide your contact and location details.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Please check your details:</strong>
                            <ul class="mb-0 mt-1 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('booking-service.store-step-3') }}"
                          method="POST"
                          class="your-details-form account-mode"
                          id="your-details-form"
                          data-user-name="{{ old('customer_name', $accountData['name'] ?: 'MD. JAHEDUL DINER') }}"
                          data-user-email="{{ old('customer_email', $accountData['email'] ?: 'md.jahedulalam99@gmail.com') }}"
                          data-user-phone="{{ old('customer_phone', $accountData['phone'] ?: '+61 412 345 678') }}"
                          data-user-address="{{ old('customer_address', $accountData['address'] ?: '25 King St, Sydney NSW 2000, Australia') }}"
                          data-user-unit="{{ old('unit_suite_floor', $accountData['unit'] ?? '') }}"
                          data-user-suburb="{{ old('suburb', $accountData['suburb'] ?: 'Sydney') }}"
                          data-user-postcode="{{ old('postcode', $accountData['postcode'] ?: '2000') }}">
                        @csrf

                        <div class="detail-mode-grid mb-4">
                            <label class="detail-mode-card {{ old('detail_mode', 'account') === 'account' ? 'active' : '' }}">
                                <input type="radio" name="detail_mode" value="account" {{ old('detail_mode', 'account') === 'account' ? 'checked' : '' }}>
                                <span class="detail-radio"></span>
                                <strong>Use my account information</strong>
                                <small>We&rsquo;ll use your saved details below.</small>
                            </label>
                            <label class="detail-mode-card {{ old('detail_mode') === 'new' ? 'active' : '' }}">
                                <input type="radio" name="detail_mode" value="new" {{ old('detail_mode') === 'new' ? 'checked' : '' }}>
                                <span class="detail-radio"></span>
                                <strong>Enter new details</strong>
                                <small>Add your details manually for this booking.</small>
                            </label>
                        </div>

                        <div class="saved-details-panel" style="{{ old('detail_mode', 'account') === 'new' ? 'display: none;' : '' }}">
                            <div>
                                <strong>{{ $accountData['name'] ?: 'MD. JAHEDUL DINER' }}</strong>
                                <p>{{ $accountData['email'] ?: 'md.jahedulalam99@gmail.com' }}</p>
                                <p>{{ $accountData['phone'] ?: '+61 412 345 678' }}</p>
                                <p>{{ $accountData['address'] ?: '25 King St, Sydney NSW 2000, Australia' }}</p>
                            </div>
                            <a href="#">Edit</a>
                        </div>

                        <div class="details-form-grid">
                            <div class="form-group booking-field">
                                <label for="full-name">Full Name <span>*</span></label>
                                <input type="text"
                                       name="customer_name"
                                       class="form-control booking-detail-input"
                                       id="full-name"
                                       value="{{ old('customer_name', $step3Data['customer_name'] ?? ($accountData['name'] ?: 'MD. JAHEDUL DINER')) }}"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                       required>
                            </div>
                            <div class="form-group booking-field">
                                <label for="email-address">Email Address <span>*</span></label>
                                <input type="email"
                                       name="customer_email"
                                       class="form-control booking-detail-input"
                                       id="email-address"
                                       value="{{ old('customer_email', $step3Data['customer_email'] ?? ($accountData['email'] ?: 'md.jahedulalam99@gmail.com')) }}"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                       required>
                            </div>
                            <div class="form-group booking-field">
                                <label for="phone-number">Phone Number <span>*</span></label>
                                <input type="text"
                                       name="customer_phone"
                                       class="form-control booking-detail-input"
                                       id="phone-number"
                                       value="{{ old('customer_phone', $step3Data['customer_phone'] ?? ($accountData['phone'] ?: '+61 412 345 678')) }}"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                       required>
                            </div>
                        </div>

                        <div class="form-group booking-field address-field">
                            <label for="service-address">Service Address <span>*</span></label>
                            <input type="text"
                                   name="customer_address"
                                   class="form-control booking-detail-input"
                                   id="service-address"
                                   value="{{ old('customer_address', $step3Data['customer_address'] ?? ($accountData['address'] ?: '25 King St, Sydney NSW 2000, Australia')) }}"
                                   {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                   required>
                            <i class="fas fa-check-circle address-check" aria-hidden="true"></i>
                        </div>

                        <div class="details-form-grid address-extra-grid">
                            <div class="form-group booking-field">
                                <label for="unit-suite">Unit / Suite / Floor (Optional)</label>
                                <input type="text"
                                       name="unit_suite_floor"
                                       class="form-control booking-detail-input"
                                       id="unit-suite"
                                       value="{{ old('unit_suite_floor', $step3Data['unit_suite_floor'] ?? ($accountData['unit'] ?? '')) }}"
                                       placeholder="e.g. Unit 5, Floor 2"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}>
                            </div>
                            <div class="form-group booking-field">
                                <label for="suburb">Suburb <span>*</span></label>
                                <input type="text"
                                       name="suburb"
                                       class="form-control booking-detail-input"
                                       id="suburb"
                                       value="{{ old('suburb', $step3Data['suburb'] ?? ($accountData['suburb'] ?: 'Sydney')) }}"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                       required>
                            </div>
                            <div class="form-group booking-field">
                                <label for="postcode">Postcode <span>*</span></label>
                                <input type="text"
                                       name="postcode"
                                       class="form-control booking-detail-input"
                                       id="postcode"
                                       value="{{ old('postcode', $step3Data['postcode'] ?? ($accountData['postcode'] ?: '2000')) }}"
                                       {{ old('detail_mode', 'account') === 'account' ? 'readonly' : '' }}
                                       required>
                            </div>
                        </div>

                        <div class="form-group booking-field booking-notes-field">
                            <label for="special-instructions">Special Instructions (Optional)</label>
                            <textarea name="special_instructions"
                                      class="form-control"
                                      id="special-instructions"
                                      maxlength="250"
                                      placeholder="Any special instructions for our team?">{{ old('special_instructions', $step3Data['special_instructions'] ?? '') }}</textarea>
                            <div class="booking-counter"><span id="special-instructions-count">0</span> / 250</div>
                        </div>

                        <div class="booking-step-actions">
                            <a href="{{ route('booking-service.date-time') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Date &amp; Time
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Submit Booking <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>
                </div>

                @include('pages.booking-service.partials.trust-strip')
            </div>

            <aside class="booking-right-column">
                @include('pages.booking-service.partials.booking-summary')
                @include('pages.booking-service.partials.promo-card')
                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
