@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    <div class="booking-page">
        @include('pages.booking-service.partials.page-header')
        @include('pages.booking-service.partials.progress', ['currentStep' => 2])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 2 of 4: Date &amp; Time</h2>
                        <p>Choose your preferred date and time for your cleaning.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Please check your selection:</strong>
                            <ul class="mb-0 mt-1 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php
                        $defaultDate = old('booking_date', $step2Data['booking_date'] ?? \Carbon\Carbon::today()->format('Y-m-d'));
                        $defaultStartTime = old('start_time', $step2Data['start_time'] ?? '');
                        $defaultEndTime = old('end_time', $step2Data['end_time'] ?? '');
                    @endphp

                    <form action="{{ route('booking-service.store-step-2') }}" method="POST" id="booking-step2-form">
                        @csrf
                        <input type="hidden" name="booking_date" id="selected-booking-date" value="{{ $defaultDate }}">
                        <input type="hidden" name="start_time" id="selected-start-time" value="{{ $defaultStartTime }}">
                        <input type="hidden" name="end_time" id="selected-end-time" value="{{ $defaultEndTime }}">

                        <div class="date-time-grid">
                            <div class="booking-date-panel">
                                <label class="booking-section-label">Select a Date</label>
                                <div class="booking-calendar" 
                                     id="interactive-calendar"
                                     data-holidays='@json($holidays)'
                                     data-slots-url="{{ route('booking-service.slots-for-date') }}"
                                     data-today="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                     data-selected-date="{{ $defaultDate }}">
                                    <div class="calendar-header">
                                        <button type="button" class="calendar-nav" id="prev-month" aria-label="Previous month" disabled>
                                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                        </button>
                                        <strong id="calendar-month-year">May 2025</strong>
                                        <button type="button" class="calendar-nav" id="next-month" aria-label="Next month">
                                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="calendar-grid calendar-weekdays">
                                        <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                                    </div>
                                    <div class="calendar-grid calendar-days" id="calendar-days-container">
                                        <!-- Rendered dynamically by JavaScript -->
                                    </div>
                                </div>
                                <div class="booking-info-strip">
                                    <i class="far fa-calendar-alt" aria-hidden="true"></i> 
                                    <span id="calendar-info-text">Showing available dates</span>
                                </div>
                            </div>

                            <div class="booking-time-panel">
                                <label class="booking-section-label">Select a Time</label>
                                <div class="time-slot-grid" id="time-slots-container">
                                    <div class="text-muted p-3 text-center">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Loading available times...
                                    </div>
                                </div>
                                @php
                                    $appTz = config('app.timezone', 'UTC');
                                    $tzAbbr = \Carbon\Carbon::now($appTz)->format('T');
                                    $displayTz = (preg_match('/^[A-Z]{3,4}$/', $tzAbbr)) ? $tzAbbr : $appTz;
                                @endphp
                                <div class="booking-info-strip">
                                    <i class="far fa-clock" aria-hidden="true"></i> All times are in {{ $displayTz }}
                                </div>
                            </div>
                        </div>

                        <div class="booking-step-actions mt-4">
                            <a href="{{ route('booking-service.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Service Details
                            </a>
                            <button type="submit" class="btn btn-primary" id="continue-to-your-details">
                                Continue to Your Details <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>
                </div>

                @include('pages.booking-service.partials.trust-strip')
            </div>

            <aside class="booking-right-column">
                @include('pages.booking-service.partials.scheduling-guide')
                @include('pages.booking-service.partials.promo-card')
                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
