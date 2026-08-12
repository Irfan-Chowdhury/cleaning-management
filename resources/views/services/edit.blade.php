@extends('layouts.app')

@section('title', 'Edit Service')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/service.css') }}">
@endpush

@section('content')
    <div class="services-page">
        <div class="services-header">
            <div>
                <h1>Edit Service</h1>
                <p>Update service information.</p>
            </div>
        </div>

        <div class="service-form-card">
            <form method="POST" action="{{ route('services.update', $service) }}">
                @csrf
                @method('PUT')

                @include('services._form', ['service' => $service])

                <div class="service-form-actions">
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Services
                    </a>
                    <button type="submit" class="btn btn-primary services-primary-btn">
                        <i class="fas fa-save" aria-hidden="true"></i> Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
