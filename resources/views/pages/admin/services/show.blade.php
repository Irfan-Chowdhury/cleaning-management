@extends('layouts.app')

@section('title', 'View Service')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/service.css') }}">
@endpush

@section('content')
    <div class="services-page">
        <div class="services-header">
            <div>
                <h1>View Service</h1>
                <p>Service details and configured questionnaire.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Services
            </a>
        </div>

        <div class="service-form-card">
            <div class="service-view-summary">
                <div>
                    <span class="service-view-label">Service Title</span>
                    <h2>{{ $service->name }}</h2>
                </div>
                <span class="badge service-status-badge {{ $service->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                    {{ ucfirst($service->status) }}
                </span>
            </div>

            <div class="service-view-description">
                <span class="service-view-label">Description</span>
                <p>{{ $service->description ?: 'No description available.' }}</p>
            </div>

            <div class="service-questions-view">
                <div class="services-table-card-header">
                    <div>
                        <h2>Questionnaire</h2>
                        <p>{{ $service->serviceQuestions->count() }} questions configured</p>
                    </div>
                </div>

                @forelse ($service->serviceQuestions as $question)
                    <div class="card service-question-view-card">
                        <div class="card-body">
                            <div class="service-question-view-header">
                                <div>
                                    <span class="question-number">Q{{ $question->sort_order ?? $loop->iteration }}</span>
                                    <h3>{{ $question->title }}</h3>
                                </div>
                                <div class="question-badges">
                                    <span class="badge badge-info">{{ ucfirst($question->field_type) }}</span>
                                    <span class="badge {{ $question->required ? 'badge-primary' : 'badge-light' }}">
                                        {{ $question->required ? 'Required' : '' }}
                                    </span>
                                </div>
                            </div>

                            <div class="question-options mt-3">
                                @forelse ($question->questionOptions as $option)
                                    <span>{{ $option->label }}</span>
                                @empty
                                    <span>No options configured</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="service-empty-state">
                        <i class="fas fa-question-circle" aria-hidden="true"></i>
                        <p>No questions configured for this service.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
