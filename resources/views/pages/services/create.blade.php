@extends('layouts.app')

@section('title', 'Create Service')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/service.css') }}">
@endpush

@section('content')
    <div class="services-page">
        <div class="services-header">
            <div>
                <h1>Create Service</h1>
                <p>Add a new cleaning service.</p>
            </div>
        </div>

        <div class="service-form-card">
            <form method="POST" action="{{ route('services.store') }}">
                @csrf

                @include('pages.services._form')

                <div class="service-form-actions">
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Services
                    </a>
                    <button type="submit" class="btn btn-primary services-primary-btn">
                        <i class="fas fa-save" aria-hidden="true"></i> Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
