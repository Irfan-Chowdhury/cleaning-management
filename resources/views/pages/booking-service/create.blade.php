@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        <div class="booking-page-header">
            <h1>Book Your Cleaning</h1>
            <p>Fast. Easy. Reliable. That&rsquo;s the Dust2Glow promise.</p>
        </div>

        <div class="booking-progress">
            <div class="booking-step active">
                <span class="booking-step-circle">1</span>
                <span class="booking-step-label">Service Details</span>
            </div>
            <div class="booking-step">
                <span class="booking-step-circle">2</span>
                <span class="booking-step-label">Date &amp; Time</span>
            </div>
            <div class="booking-step">
                <span class="booking-step-circle">3</span>
                <span class="booking-step-label">Your Details</span>
            </div>
            <div class="booking-step">
                <span class="booking-step-circle">4</span>
                <span class="booking-step-label">Review &amp; Confirm</span>
            </div>
        </div>

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 1 of 4: Service Details</h2>
                        <p>Tell us what you need and how often.</p>
                    </div>

                    <form>
                        <div class="form-group booking-field">
                            <label for="booking-service">Choose Your Dust2Glow Service <span>*</span></label>
                            <div class="booking-input-icon">
                                <i class="fas fa-broom" aria-hidden="true"></i>
                                <select class="form-control" id="booking-service">
                                    <option value="">Select</option>
                                    <option>Regular Home Cleaning</option>
                                    <option>Deep Cleaning</option>
                                    <option>End of Lease Cleaning</option>
                                    <option>Office Cleaning</option>
                                    <option>Window Cleaning</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group booking-field">
                            <label for="service-frequency">Service Frequency <span>*</span></label>
                            <div class="booking-input-icon">
                                <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                <select class="form-control" id="service-frequency">
                                    <option value="">Please Choose...</option>
                                    <option>One Time</option>
                                    <option>Weekly</option>
                                    <option>Fortnightly</option>
                                    <option>Monthly</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group booking-field booking-notes-field">
                            <label for="booking-notes">Have a note, request, or fun fact? Drop it here.</label>
                            <textarea class="form-control" id="booking-notes" maxlength="500" placeholder="Go ahead, we&rsquo;re all ears."></textarea>
                            <div class="booking-counter"><span id="booking-notes-count">0</span> / 500</div>
                        </div>

                        <button type="button" id="continue-to-date-time" class="btn btn-primary btn-block booking-continue-btn">
                            Continue to Date &amp; Time <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>

                <div class="booking-trust-strip">
                    <div class="booking-trust-item">
                        <span><i class="fas fa-shield-alt" aria-hidden="true"></i></span>
                        <div>
                            <strong>Satisfaction Guaranteed</strong>
                            <p>100% Happiness Promise</p>
                        </div>
                    </div>
                    <div class="booking-trust-item">
                        <span><i class="fas fa-star" aria-hidden="true"></i></span>
                        <div>
                            <strong>Trusted Cleaners</strong>
                            <p>Police Checked &amp; Verified</p>
                        </div>
                    </div>
                    <div class="booking-trust-item">
                        <span><i class="fas fa-lock" aria-hidden="true"></i></span>
                        <div>
                            <strong>Secure Payments</strong>
                            <p>SSL Encrypted</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="booking-right-column">
                <div class="booking-summary-card">
                    <h2><i class="fas fa-magic" aria-hidden="true"></i> Booking Summary</h2>
                    <p>Your booking details will appear here.</p>
                    <div class="booking-empty-state">
                        <span><i class="far fa-clipboard" aria-hidden="true"></i></span>
                        <i class="far fa-calendar-check" aria-hidden="true"></i>
                    </div>
                    <strong>Fill in the details to see your booking summary.</strong>
                </div>

                <div class="booking-side-card booking-promo-card">
                    <h2>Have a Referral or Promo Code?</h2>
                    <div class="booking-promo-row">
                        <div class="booking-input-icon">
                            <i class="fas fa-tag" aria-hidden="true"></i>
                            <input type="text" class="form-control" placeholder="Enter referral or promo code">
                        </div>
                        <button type="button" class="btn btn-primary">Apply</button>
                    </div>
                    <div class="booking-promo-note">
                        <i class="fas fa-gift" aria-hidden="true"></i>
                        <span>Use a referral code and get credit when you book!</span>
                    </div>
                </div>

                <div class="booking-side-card booking-support-card">
                    <span class="booking-support-icon"><i class="fas fa-headset" aria-hidden="true"></i></span>
                    <h2>Need help with your booking?</h2>
                    <p>Our support team is here for you!</p>
                    <a href="#">Contact Support <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
