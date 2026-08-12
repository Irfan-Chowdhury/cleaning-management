@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('booking-service.partials.page-header')
        @include('booking-service.partials.progress', ['currentStep' => 3])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 3 of 4: Your Details</h2>
                        <p>Please provide your contact and location details.</p>
                    </div>

                    <div class="detail-mode-grid">
                        <label class="detail-mode-card active">
                            <input type="radio" name="detail_mode" value="account" checked>
                            <span class="detail-radio"></span>
                            <strong>Use my account information</strong>
                            <small>We&rsquo;ll use your saved details below.</small>
                        </label>
                        <label class="detail-mode-card">
                            <input type="radio" name="detail_mode" value="new">
                            <span class="detail-radio"></span>
                            <strong>Enter new details</strong>
                            <small>Add your details manually for this booking.</small>
                        </label>
                    </div>

                    <div class="saved-details-panel">
                        <div>
                            <strong>MD. JAHEDUL DINER</strong>
                            <p>md.jahedulalam99@gmail.com</p>
                            <p>+61 412 345 678</p>
                            <p>25 King St, Sydney NSW 2000, Australia</p>
                        </div>
                        <a href="#">Edit</a>
                    </div>

                    <form class="your-details-form account-mode">
                        <div class="details-form-grid">
                            <div class="form-group booking-field">
                                <label for="full-name">Full Name <span>*</span></label>
                                <input type="text" class="form-control booking-detail-input" id="full-name" value="MD. JAHEDUL DINER" readonly>
                            </div>
                            <div class="form-group booking-field">
                                <label for="email-address">Email Address <span>*</span></label>
                                <input type="email" class="form-control booking-detail-input" id="email-address" value="md.jahedulalam99@gmail.com" readonly>
                            </div>
                            <div class="form-group booking-field">
                                <label for="phone-number">Phone Number <span>*</span></label>
                                <div class="phone-input-wrap">
                                    <span class="phone-country">AU</span>
                                    <input type="text" class="form-control booking-detail-input" id="phone-number" value="+61 412 345 678" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group booking-field address-field">
                            <label for="service-address">Service Address <span>*</span></label>
                            <input type="text" class="form-control booking-detail-input" id="service-address" value="25 King St, Sydney NSW 2000, Australia" readonly>
                            <i class="fas fa-check-circle address-check" aria-hidden="true"></i>
                        </div>

                        <div class="details-form-grid address-extra-grid">
                            <div class="form-group booking-field">
                                <label for="unit-suite">Unit / Suite / Floor (Optional)</label>
                                <input type="text" class="form-control booking-detail-input" id="unit-suite" placeholder="e.g. Unit 5, Floor 2" readonly>
                            </div>
                            <div class="form-group booking-field">
                                <label for="suburb">Suburb <span>*</span></label>
                                <input type="text" class="form-control booking-detail-input" id="suburb" value="Sydney" readonly>
                            </div>
                            <div class="form-group booking-field">
                                <label for="postcode">Postcode <span>*</span></label>
                                <input type="text" class="form-control booking-detail-input" id="postcode" value="2000" readonly>
                            </div>
                        </div>

                        <div class="form-group booking-field booking-notes-field">
                            <label for="special-instructions">Special Instructions (Optional)</label>
                            <textarea class="form-control booking-detail-input" id="special-instructions" maxlength="250" placeholder="Any special instructions for our team?" readonly></textarea>
                            <div class="booking-counter"><span id="special-instructions-count">0</span> / 250</div>
                        </div>
                    </form>

                    <div class="booking-step-actions">
                        <a href="{{ route('booking-service.date-time') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Date &amp; Time</a>
                        <a href="{{ route('booking-service.review-confirm') }}" class="btn btn-primary">Continue to Review &amp; Confirm <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                    </div>
                </div>

                @include('booking-service.partials.trust-strip')
            </div>

            <aside class="booking-right-column">
                @include('booking-service.partials.booking-summary')
                @include('booking-service.partials.promo-card')
                @include('booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
