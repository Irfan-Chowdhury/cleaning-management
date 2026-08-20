@extends('layouts.app')

@section('title', 'Edit Booking #' . $booking->id)

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/wallet.css') }}">
    <style>
        .booking-edit-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.035);
            padding: 28px 32px;
            width: 100%;
        }

        .booking-edit-card .card-title {
            color: #13213c;
            font-size: 20px;
            font-weight: 700;
        }

        .booking-edit-card label {
            color: #13213c;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .booking-input-icon {
            position: relative;
        }

        .booking-input-icon i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #98a2b3;
            font-size: 15px;
            z-index: 4;
        }

        .booking-input-icon .form-control {
            padding-left: 44px;
            min-height: 48px;
            border-radius: 8px;
            border-color: #e1e7f0;
            font-size: 14px;
            color: #17233c;
        }

        .booking-input-icon .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }

        .booking-questionnaire {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            margin-top: 8px;
            margin-bottom: 24px;
        }

        .booking-questionnaire-empty {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            padding: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
                </a>
                <h1>Edit Booking #{{ $booking->id }}</h1>
                <p>Modify service details, schedule date, time slot, amount, and payment status.</p>
            </div>
        </div>

        <!-- Profile Details Section (Label Left, Value Right) -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="wallet-user-card h-100">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <img src="{{ $booking->customer_avatar }}" alt="{{ $booking->customer_name }}" class="wallet-user-avatar rounded-circle mr-3">
                        <div>
                            <h4 class="mb-0 font-weight-bold text-dark">{{ $booking->customer_name }}</h4>
                            <span class="badge badge-light border text-muted px-2 py-1 mt-1" style="font-size: 11px;">Customer Profile</span>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless user-info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2" style="width: 30%;">
                                    <i class="fas fa-envelope mr-1 text-primary"></i> Email
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $booking->customer_email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-phone mr-1 text-primary"></i> Phone
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $booking->customer_phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-venus-mars mr-1 text-primary"></i> Gender
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $booking->customer_gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Full-Width Card Container -->
        <div class="booking-edit-card mb-4">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                <h2 class="card-title mb-0">
                    <i class="fas fa-edit text-primary mr-2"></i> Update Booking Details
                </h2>
                <span class="badge badge-light border text-muted px-3 py-2" style="font-size: 13px;">ID: #{{ $booking->id }}</span>
            </div>

            <form action="{{ route('bookings.update', $booking->id) }}" method="POST" id="booking-edit-form">
                @csrf
                @method('PUT')

                <!-- Row 1: Service & Amount -->
                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-service">Service <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-broom" aria-hidden="true"></i>
                            <select class="form-control" id="booking-service" name="service_id" data-questionnaire-url="{{ url('/booking-service/questionnaire') }}" required>
                                <option value="">Select Service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ (isset($booking->service_id) && $booking->service_id == $service->id) || strtolower($service->name) == strtolower($booking->service_name ?? '') ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-amount">Amount ($) <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                            <input type="number" step="0.01" class="form-control" id="booking-amount" name="amount" value="{{ number_format($booking->amount, 2, '.', '') }}" placeholder="0.00" required>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Service Questionnaire Container (Full Width) -->
                <div class="row">
                    <div class="col-12">
                        <div id="booking-questionnaire" class="booking-questionnaire" data-empty-text="Select a service to load related questions.">
                            <div class="booking-questionnaire-empty">
                                <i class="far fa-list-alt mr-2" aria-hidden="true"></i>
                                <span>Select a service to load related questionnaire options.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Date & Time Slot -->
                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-date">Schedule Date <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="far fa-calendar-alt" aria-hidden="true"></i>
                            <input type="date" class="form-control" id="booking-date" name="date" value="{{ $booking->date }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-slot">Time Slot <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <select class="form-control" id="booking-slot" name="slot" required>
                                <option value="07:00 AM" {{ $booking->slot == '07:00 AM' ? 'selected' : '' }}>07:00 AM</option>
                                <option value="09:30 AM" {{ $booking->slot == '09:30 AM' ? 'selected' : '' }}>09:30 AM</option>
                                <option value="11:00 AM" {{ $booking->slot == '11:00 AM' ? 'selected' : '' }}>11:00 AM</option>
                                <option value="02:00 PM" {{ $booking->slot == '02:00 PM' ? 'selected' : '' }}>02:00 PM</option>
                                <option value="04:30 PM" {{ $booking->slot == '04:30 PM' ? 'selected' : '' }}>04:30 PM</option>
                                <option value="07:00 PM" {{ $booking->slot == '07:00 PM' ? 'selected' : '' }}>07:00 PM</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Payment Status -->
                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label for="payment-status">Payment Status <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-credit-card" aria-hidden="true"></i>
                            <select class="form-control" id="payment-status" name="payment_status" required>
                                <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ $booking->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $booking->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="pt-3 border-top d-flex align-items-center justify-content-end" style="gap: 12px;">
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary customers-primary-btn px-4 py-2">
                        <i class="fas fa-save mr-1" aria-hidden="true"></i> Update Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Trigger initial change to load questionnaire for selected service if any
            if ($('#booking-service').val()) {
                $('#booking-service').trigger('change');
            }
        });
    </script>
@endpush
