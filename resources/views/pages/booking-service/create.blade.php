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

                    <form action="{{ route('booking-service.store-step-1') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group booking-field">
                            <label for="booking-service">Choose Your Dust2Glow Service <span>*</span></label>
                            <div class="booking-input-icon">
                                <i class="fas fa-broom" aria-hidden="true"></i>
                                @php
                                    $selectedServiceId = old('service_id', $step1Data['service_id'] ?? '');
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

                        <!-- Upload Home Images Section -->
                        <div class="form-group booking-field booking-images-field">
                            <label for="booking-images" class="font-weight-bold" style="font-size: 13.5px; color: #0f172a;">
                                Upload Photos of Your Space <span class="text-muted font-weight-normal">(Optional, max 10 photos)</span>
                            </label>
                            <div class="custom-file-upload-wrapper p-3 text-center border rounded-lg" style="border: 2px dashed #cbd5e1 !important; background-color: #f8fafc; border-radius: 12px; cursor: pointer;">
                                <label for="booking-images" class="m-0 d-block" style="cursor: pointer;">
                                    <i class="fas fa-cloud-upload-alt text-primary mb-2" style="font-size: 28px;"></i>
                                    <strong class="d-block text-dark" style="font-size: 13.5px;">Click to select photos of your home/space</strong>
                                    <span class="text-muted d-block mt-1" style="font-size: 11.5px;">Supported formats: JPG, PNG, WEBP (Max 5MB per file)</span>
                                    <input type="file" 
                                           id="booking-images" 
                                           name="images[]" 
                                           multiple 
                                           accept="image/jpeg,image/png,image/jpg,image/webp" 
                                           class="d-none">
                                </label>
                            </div>
                            @error('images')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @error('images.*')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <!-- Preview Grid -->
                            <div id="booking-images-preview-grid" class="d-flex flex-wrap mt-3" style="gap: 10px;">
                                @if (!empty($step1Data['images']) && is_array($step1Data['images']))
                                    @foreach ($step1Data['images'] as $img)
                                        @php
                                            $imgPath = is_array($img) ? ($img['path'] ?? '') : $img;
                                        @endphp
                                        @if (!empty($imgPath))
                                            <div class="image-preview-item position-relative border rounded p-1" style="width: 80px; height: 80px; overflow: hidden; background: #ffffff; border-color: #cbd5e1 !important; border-radius: 8px;">
                                                <img src="{{ asset('public/' . $imgPath) }}" alt="Home Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
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
        $(document).ready(function () {
            $('#booking-images').on('change', function () {
                var files = this.files;
                var $grid = $('#booking-images-preview-grid');
                $grid.empty();
                if (files && files.length > 0) {
                    $.each(files, function (i, file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var $item = $('<div class="image-preview-item position-relative border rounded p-1" style="width: 80px; height: 80px; overflow: hidden; background: #ffffff; border-color: #cbd5e1 !important; border-radius: 8px;">' +
                                '<img src="' + e.target.result + '" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">' +
                                '</div>');
                            $grid.append($item);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            });
        });
    </script>
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
