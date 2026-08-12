@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('booking-service.partials.page-header')
        @include('booking-service.partials.progress', ['currentStep' => 2])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 2 of 4: Date &amp; Time</h2>
                        <p>Choose your preferred date and time for your cleaning.</p>
                    </div>

                    <div class="date-time-grid">
                        <div class="booking-date-panel">
                            <label class="booking-section-label">Select a Date</label>
                            <div class="booking-calendar">
                                <div class="calendar-header">
                                    <button type="button" class="calendar-nav" aria-label="Previous month"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
                                    <strong>May 2025</strong>
                                    <button type="button" class="calendar-nav" aria-label="Next month"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                                </div>
                                <div class="calendar-grid calendar-weekdays">
                                    <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                </div>
                                <div class="calendar-grid calendar-days">
                                    <button type="button" class="outside-month">28</button>
                                    <button type="button" class="outside-month">29</button>
                                    <button type="button" class="outside-month">30</button>
                                    <button type="button">1</button>
                                    <button type="button">2</button>
                                    <button type="button">3</button>
                                    <button type="button">4</button>
                                    <button type="button">5</button>
                                    <button type="button">6</button>
                                    <button type="button">7</button>
                                    <button type="button">8</button>
                                    <button type="button">9</button>
                                    <button type="button">10</button>
                                    <button type="button">11</button>
                                    <button type="button">12</button>
                                    <button type="button">13</button>
                                    <button type="button">14</button>
                                    <button type="button" class="selected">15</button>
                                    <button type="button">16</button>
                                    <button type="button">17</button>
                                    <button type="button">18</button>
                                    <button type="button">19</button>
                                    <button type="button">20</button>
                                    <button type="button">21</button>
                                    <button type="button">22</button>
                                    <button type="button">23</button>
                                    <button type="button">24</button>
                                    <button type="button">25</button>
                                    <button type="button">26</button>
                                    <button type="button">27</button>
                                    <button type="button">28</button>
                                    <button type="button">29</button>
                                    <button type="button">30</button>
                                    <button type="button">31</button>
                                    <button type="button" class="outside-month">1</button>
                                </div>
                            </div>
                            <div class="booking-info-strip"><i class="far fa-calendar-alt" aria-hidden="true"></i> Showing available dates</div>
                        </div>

                        <div class="booking-time-panel">
                            <label class="booking-section-label">Select a Time</label>
                            <div class="time-slot-grid">
                                <button type="button" class="time-slot">7:00 AM</button>
                                <button type="button" class="time-slot">1:00 PM</button>
                                <button type="button" class="time-slot">8:00 AM</button>
                                <button type="button" class="time-slot">2:00 PM</button>
                                <button type="button" class="time-slot selected">9:00 AM</button>
                                <button type="button" class="time-slot">3:00 PM</button>
                                <button type="button" class="time-slot">10:00 AM</button>
                                <button type="button" class="time-slot">4:00 PM</button>
                                <button type="button" class="time-slot">11:00 AM</button>
                                <button type="button" class="time-slot">5:00 PM</button>
                                <button type="button" class="time-slot">12:00 PM</button>
                                <button type="button" class="time-slot">6:00 PM</button>
                            </div>
                            <div class="booking-info-strip"><i class="far fa-clock" aria-hidden="true"></i> All times are in AEST</div>
                        </div>
                    </div>

                    <div class="booking-step-actions">
                        <a href="{{ route('booking-service.create') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Service Details</a>
                        <a href="{{ route('booking-service.your-details') }}" class="btn btn-primary">Continue to Your Details <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
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
