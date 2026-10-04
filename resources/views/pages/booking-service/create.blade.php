@extends('layouts.app')

@section('title', 'Book Your Cleaning')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
    <style>
        .image-preview-item {
            position: relative;
            width: 86px;
            height: 86px;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .image-preview-item:hover {
            border-color: #ef4444 !important;
        }
        .image-preview-item .remove-img-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: rgba(220, 38, 38, 0.9);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .image-preview-item:hover .remove-img-btn {
            opacity: 1;
            transform: scale(1.05);
        }
        .image-preview-item .remove-img-btn:hover {
            background: #b91c1c;
            transform: scale(1.15);
        }
    </style>
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
                                            <div class="image-preview-item position-relative border rounded p-1">
                                                <img src="{{ asset('public/' . $imgPath) }}" alt="Home Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
                                                <button type="button" class="remove-img-btn js-remove-file" title="Remove image">
                                                    <i class="fas fa-times"></i>
                                                </button>
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
            var dt = new DataTransfer();

            function renderPreviews() {
                var $grid = $('#booking-images-preview-grid');
                $grid.find('.new-preview-item').remove();

                if (dt.files && dt.files.length > 0) {
                    $.each(dt.files, function (index, file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var $item = $('<div class="image-preview-item new-preview-item position-relative border rounded p-1" data-index="' + index + '">' +
                                '<img src="' + e.target.result + '" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">' +
                                '<button type="button" class="remove-img-btn js-remove-file" title="Remove image">' +
                                    '<i class="fas fa-times"></i>' +
                                '</button>' +
                            '</div>');
                            $grid.append($item);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }

            $('#booking-images').on('change', function () {
                var newFiles = this.files;
                if (newFiles && newFiles.length > 0) {
                    for (var i = 0; i < newFiles.length; i++) {
                        dt.items.add(newFiles[i]);
                    }
                    this.files = dt.files;
                    renderPreviews();
                }
            });

            $(document).on('click', '.js-remove-file', function (e) {
                e.preventDefault();
                var $item = $(this).closest('.image-preview-item');
                var indexToRemove = $item.attr('data-index');

                if (typeof indexToRemove !== 'undefined' && indexToRemove !== false && indexToRemove !== '') {
                    var targetIdx = parseInt(indexToRemove, 10);
                    var newDt = new DataTransfer();
                    for (var i = 0; i < dt.files.length; i++) {
                        if (i !== targetIdx) {
                            newDt.items.add(dt.files[i]);
                        }
                    }
                    dt = newDt;
                    var fileInput = document.getElementById('booking-images');
                    if (fileInput) {
                        fileInput.files = dt.files;
                    }
                    renderPreviews();
                } else {
                    $item.fadeOut(200, function() { $(this).remove(); });
                }
            });
        });
    </script>
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
