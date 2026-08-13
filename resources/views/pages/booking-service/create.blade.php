@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('pages.booking-service.partials.page-header')
        @include('pages.booking-service.partials.progress', ['currentStep' => 1])

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

                        <a href="{{ route('booking-service.date-time') }}" id="continue-to-date-time" class="btn btn-primary btn-block booking-continue-btn">
                            Continue to Date &amp; Time <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </form>
                </div>

                @include('pages.booking-service.partials.trust-strip')
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

                @include('pages.booking-service.partials.promo-card')
                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
