@extends('layouts.app')

@section('title', 'Booking Details #' . $booking->id)

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .booking-details-card-container {
            width: 50%;
            margin: 0 auto;
        }

        @media (max-width: 991.98px) {
            .booking-details-card-container {
                width: 100%;
            }
        }

        .booking-show-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.04);
            padding: 24px;
        }

        .booking-info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .booking-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #edf1f7;
        }

        .booking-info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .booking-info-label {
            font-size: 13px;
            font-weight: 600;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .booking-info-value {
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
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
                </a>
                <h1>Booking Details</h1>
                <p>Preview complete details for Booking #{{ $booking->id }}</p>
            </div>
        </div>

        <!-- Centered Card with 50% width -->
        <div class="booking-details-card-container">
            <div class="booking-show-card">
                <!-- Customer Avatar & Top Banner -->
                <div class="text-center pb-3 mb-3 border-bottom">
                    <img src="{{ $booking->customer_avatar }}" alt="{{ $booking->customer_name }}" class="customer-avatar mb-2" style="width: 80px; height: 80px; border-width: 3px;">
                    <h3 class="font-weight-bold text-dark mb-1">{{ $booking->customer_name }}</h3>
                </div>

                <!-- Booking Info One By One -->
                <ul class="booking-info-list">
                    <!-- Customer Email -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-envelope mr-2 text-primary"></i> Email</span>
                        <span class="booking-info-value">{{ $booking->customer_email }}</span>
                    </li>

                    <!-- Contact / Phone -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-phone mr-2 text-primary"></i> Contact</span>
                        <span class="booking-info-value">{{ $booking->customer_phone }}</span>
                    </li>

                    <!-- Gender -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-venus-mars mr-2 text-primary"></i> Gender</span>
                        <span class="booking-info-value">{{ $booking->customer_gender }}</span>
                    </li>

                    <!-- Address -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-map-marker-alt mr-2 text-primary"></i> Address</span>
                        <span class="booking-info-value text-right" style="max-width: 60%;">{{ $booking->customer_address }}</span>
                    </li>

                    <!-- Service Name -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-broom mr-2 text-primary"></i> Service Name</span>
                        <span class="booking-info-value text-primary">{{ $booking->service_name }}</span>
                    </li>

                    <!-- 5. Booking Schedule -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="far fa-calendar-alt mr-2 text-primary"></i> Booking Schedule</span>
                        <span class="booking-info-value">
                            {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }} at {{ $booking->slot }}
                        </span>
                    </li>

                    <!-- 6. Amount -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-dollar-sign mr-2 text-primary"></i> Amount</span>
                        <span class="booking-info-value font-weight-bold" style="font-size: 16px;">${{ number_format($booking->amount, 2) }}</span>
                    </li>

                    <!-- 7. Payment Status -->
                    <li class="booking-info-item">
                        <span class="booking-info-label"><i class="fas fa-credit-card mr-2 text-primary"></i> Payment Status</span>
                        <div class="booking-info-value">
                            @php
                                $statusClass = 'badge-success';
                                if ($booking->payment_status === 'pending') {
                                    $statusClass = 'badge-warning text-dark';
                                } elseif ($booking->payment_status === 'failed') {
                                    $statusClass = 'badge-danger';
                                } elseif ($booking->payment_status === 'refunded') {
                                    $statusClass = 'badge-secondary';
                                }
                            @endphp
                            <span class="badge {{ $statusClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
