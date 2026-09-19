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

                    <form action="{{ route('booking-service.store-step-1') }}" method="POST">
                        @csrf

                        <div class="form-group booking-field">
                            <label for="booking-service">Choose Your Dust2Glow Service <span>*</span></label>
                            <div class="booking-input-icon">
                                <i class="fas fa-broom" aria-hidden="true"></i>
                                @php
                                    $selectedServiceId = old('service_id', '');
                                    $savedQuestions = old('questions', $step1Data['questions'] ?? []);
                                @endphp
                                <select class="form-control @error('service_id') is-invalid @enderror" id="booking-service" name="service_id" data-questionnaire-url="{{ url('/booking-service/questionnaire') }}">
                                    <option value="" {{ (string)$selectedServiceId === '' ? 'selected' : '' }}>Select</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" {{ (string)$selectedServiceId === (string)$service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('service_id')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div id="booking-questionnaire" 
                             class="booking-questionnaire" 
                             data-empty-text="Select a service to load related questions."
                             data-saved-questions='@json($savedQuestions)'>
                            <div class="booking-questionnaire-empty">
                                <i class="far fa-list-alt" aria-hidden="true"></i>
                                <span>Select a service to load related questions.</span>
                            </div>
                        </div>

                        <div class="form-group booking-field booking-notes-field">
                            <label for="booking-notes">Have a note, request, or fun fact? Drop it here.</label>
                            <textarea class="form-control @error('service_notes') is-invalid @enderror" 
                                      id="booking-notes" 
                                      name="service_notes" 
                                      maxlength="500" 
                                      placeholder="Go ahead, we&rsquo;re all ears.">{{ old('service_notes', $step1Data['service_notes'] ?? '') }}</textarea>
                            @error('service_notes')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <div class="booking-counter"><span id="booking-notes-count">0</span> / 500</div>
                        </div>

                        <button type="submit" id="continue-to-date-time" class="btn btn-primary btn-block booking-continue-btn">
                            Continue to Date &amp; Time <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>

                @include('pages.booking-service.partials.trust-strip')
            </div>

            <aside class="booking-right-column">
                @include('pages.booking-service.partials.service-guide-card')
                @include('pages.booking-service.partials.promo-card')
                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@php
    $servicesData = $services->map(function ($service) {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'whats_included' => $service->whats_included ?? [],
        ];
    })->values();
@endphp

@push('scripts')
    <script>
        window.bookingServicesData = @json($servicesData);
    </script>
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
