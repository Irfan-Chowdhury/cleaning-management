@extends('layouts.app')

@section('title', 'Customer Details #' . $customer->id)

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .customer-details-card-container {
            width: 50%;
            margin: 0 auto;
        }

        @media (max-width: 991.98px) {
            .customer-details-card-container {
                width: 100%;
            }
        }

        .customer-show-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.04);
            padding: 24px;
        }

        .customer-info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .customer-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #edf1f7;
        }

        .customer-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .customer-info-label {
            font-size: 13px;
            font-weight: 600;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .customer-info-value {
            font-size: 14px;
            font-weight: 600;
            color: #13213c;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Header -->
        <div class="customers-header mb-3">
            <div>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Customers
                </a>
                <h1>Customer Details</h1>
                <p>Preview complete details for {{ trim($customer->first_name . ' ' . $customer->last_name) ?: 'Customer #' . $customer->id }}</p>
            </div>
        </div>

        <!-- Centered Card with 50% width -->
        <div class="customer-details-card-container">
            <div class="customer-show-card">
                @php
                    $fullName = trim($customer->first_name . ' ' . $customer->last_name) ?: 'Customer #' . $customer->id;
                    $avatarUrl = $customer->photo
                        ? (filter_var($customer->photo, FILTER_VALIDATE_URL) ? $customer->photo : asset('public/' . ltrim($customer->photo, '/')))
                        : 'https://ui-avatars.com/api/?background=0866e8&color=fff&name=' . urlencode($fullName);
                @endphp

                <!-- Customer Avatar & Top Banner -->
                <div class="text-center pb-3 mb-3 border-bottom">
                    <img src="{{ $avatarUrl }}" alt="{{ $fullName }}" class="customer-avatar mb-2" style="width: 80px; height: 80px; border-width: 3px; object-fit: cover;">
                    <h3 class="font-weight-bold text-dark mb-0">{{ $fullName }}</h3>
                </div>

                <!-- Customer Details List -->
                <ul class="customer-info-list">
                    <!-- 1. Email -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-envelope mr-2 text-primary"></i> Email</span>
                        <span class="customer-info-value">{{ $customer->email }}</span>
                    </li>

                    <!-- 2. Phone -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-phone mr-2 text-primary"></i> Phone</span>
                        <span class="customer-info-value">{{ $customer->phone ?: 'N/A' }}</span>
                    </li>

                    <!-- 3. Gender -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-venus-mars mr-2 text-primary"></i> Gender</span>
                        <span class="customer-info-value">{{ ucfirst($customer->gender ?: 'Not specified') }}</span>
                    </li>

                    <!-- 4. Address -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-map-marker-alt mr-2 text-primary"></i> Address</span>
                        <span class="customer-info-value text-right" style="max-width: 60%;">{{ $customer->address ?: 'N/A' }}</span>
                    </li>

                    <!-- 5. Status -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-toggle-on mr-2 text-primary"></i> Status</span>
                        <div class="customer-info-value">
                            <span class="badge {{ $customer->is_active ? 'badge-success' : 'badge-secondary' }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                {{ $customer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </li>

                    <!-- 6. Created By -->
                    <li class="customer-info-item">
                        <span class="customer-info-label"><i class="fas fa-user-shield mr-2 text-primary"></i> Created By</span>
                        <span class="customer-info-value">
                            @if ($customer->creator)
                                {{ trim($customer->creator->first_name . ' ' . $customer->creator->last_name) ?: $customer->creator->email }}
                            @elseif ($customer->created_by)
                                User #{{ $customer->created_by }}
                            @else
                                Self-registered
                            @endif
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
