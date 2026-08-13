@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('pages.booking-service.partials.page-header')
        @include('pages.booking-service.partials.progress', ['currentStep' => 4])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 4 of 4: Review &amp; Confirm</h2>
                        <p>Please review your booking details and confirm.</p>
                    </div>

                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="fas fa-broom" aria-hidden="true"></i> Service &amp; Schedule</h3>
                            <div>
                                <a href="{{ route('booking-service.create') }}">Edit Service</a>
                                <a href="{{ route('booking-service.date-time') }}">Edit Time</a>
                            </div>
                        </div>
                        <div class="review-service-grid">
                            <img src="https://picsum.photos/seed/dust2glow-review/240/160" alt="Regular home cleaning" class="review-image">
                            <div>
                                <h4>Regular Home Cleaning</h4>
                                <span class="booking-badge">Weekly</span>
                                <ul class="summary-list">
                                    <li><i class="far fa-calendar-alt" aria-hidden="true"></i> 15 May 2025 (Thu)</li>
                                    <li><i class="far fa-clock" aria-hidden="true"></i> 9:00 AM - 10:00 AM</li>
                                    <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> 25 King St, Sydney NSW 2000</li>
                                </ul>
                            </div>
                            <div class="estimated-price-box">
                                <span>Estimated Price</span>
                                <strong>$180.00</strong>
                            </div>
                        </div>
                    </div>

                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="far fa-user" aria-hidden="true"></i> Your Details</h3>
                            <a href="{{ route('booking-service.your-details') }}">Edit</a>
                        </div>
                        <div class="review-details-grid">
                            <div class="review-contact-list">
                                <strong>MD. JAHEDUL DINER</strong>
                                <span>md.jahedulalam99@gmail.com</span>
                                <span>+61 412 345 678</span>
                                <span>25 King St, Sydney NSW 2000, Australia</span>
                            </div>
                            <dl class="review-detail-table">
                                <div><dt>Unit / Suite / Floor</dt><dd>-</dd></div>
                                <div><dt>Suburb</dt><dd>Sydney</dd></div>
                                <div><dt>Postcode</dt><dd>2000</dd></div>
                                <div><dt>Special Instructions</dt><dd>None</dd></div>
                            </dl>
                        </div>
                    </div>

                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="fas fa-tag" aria-hidden="true"></i> Payment &amp; Offers</h3>
                            <a href="#">Edit</a>
                        </div>
                        <div class="payment-review-grid">
                            <div class="applied-promo-panel">
                                <span>Referral / Promo Code</span>
                                <strong>REF-JAHEDUL <em>Applied</em></strong>
                                <p>You saved $18.00</p>
                            </div>
                            <div class="payment-total-panel">
                                <div><span>Discount</span><strong>- $18.00</strong></div>
                                <hr>
                                <div class="grand-total"><span>Total (estimated)</span><strong>$162.00</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="booking-step-actions review-actions">
                        <a href="{{ route('booking-service.your-details') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Your Details</a>
                        <button type="button" class="btn btn-primary confirm-pay-btn"><i class="fas fa-lock" aria-hidden="true"></i> Confirm Booking &amp; Pay</button>
                    </div>
                    <p class="secure-checkout"><i class="fas fa-shield-alt" aria-hidden="true"></i> Secure checkout. Your payment is safe with us.</p>
                </div>
            </div>

            <aside class="booking-right-column">
                <div class="booking-summary-card filled-summary-card final-summary-card">
                    <div class="summary-title-row">
                        <h2>Booking Summary</h2>
                        <a href="{{ route('booking-service.create') }}">Edit Booking</a>
                    </div>
                    <img src="https://picsum.photos/seed/dust2glow-final/420/260" alt="Clean living area" class="summary-image">
                    <h3>Regular Home Cleaning</h3>
                    <span class="booking-badge">Weekly</span>
                    <ul class="summary-list">
                        <li><i class="far fa-calendar-alt" aria-hidden="true"></i> 15 May 2025 (Thu)</li>
                        <li><i class="far fa-clock" aria-hidden="true"></i> 9:00 AM - 10:00 AM</li>
                        <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> 25 King St, Sydney NSW 2000</li>
                    </ul>
                    <div class="price-breakdown">
                        <div><span>Price</span><strong>$180.00</strong></div>
                        <div><span>Discount (REF-JAHEDUL)</span><strong class="discount-value">- $18.00</strong></div>
                        <div class="summary-total"><span>Total (estimated)</span><strong>$162.00</strong></div>
                    </div>
                </div>

                <div class="booking-side-card referral-success-card">
                    <h2><i class="fas fa-check-circle" aria-hidden="true"></i> Referral Code Applied</h2>
                    <p>Great! You saved $18.00 on this booking.</p>
                </div>

                <div class="booking-side-card why-book-card">
                    <h2>Why book with Dust2Glow?</h2>
                    <ul>
                        <li><i class="fas fa-check" aria-hidden="true"></i> 100% Satisfaction Guarantee</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Police Checked &amp; Verified Cleaners</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Secure Payments</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Trusted by 1,000+ Customers</li>
                    </ul>
                </div>

                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
