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

        .customer-details-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
            padding: 24px;
        }

        .customer-details-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111c3a;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f4f9;
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
                <p>Modify booking status, service details, schedule date, time slot, amount, and customer details.</p>
            </div>
            <div>
                @php
                    $statusEnum = \App\Enums\BookingStatus::tryFrom($booking->status) ?? \App\Enums\BookingStatus::PENDING;
                @endphp
                <span class="badge {{ $statusEnum->badgeClass() }} font-weight-bold px-3 py-2" style="font-size: 14px; border-radius: 999px;">
                    <i class="fas fa-info-circle mr-1"></i> Status: {{ $statusEnum->label() }}
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Please fix the errors below:</strong>
                <ul class="mb-0 mt-1 pl-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Full Customer Details Section Card -->
        <div class="customer-details-card mb-4">
            <h3><i class="fas fa-user-circle text-primary mr-2"></i> Customer Information &amp; Location Details</h3>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $booking->customer_avatar }}" alt="{{ $booking->customer_name }}" class="rounded-circle mr-3" style="width: 54px; height: 54px;">
                        <div>
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 17px;">{{ $booking->customer_name }}</h4>
                            <span class="text-muted" style="font-size: 13px;">ID: #{{ $booking->id }}</span>
                        </div>
                    </div>
                    <table class="table table-sm table-borderless user-info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted font-weight-bold py-1" style="width: 35%;"><i class="fas fa-envelope mr-1 text-primary"></i> Email:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->customer_email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold py-1"><i class="fas fa-phone mr-1 text-primary"></i> Phone:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->customer_phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold py-1"><i class="fas fa-map-marker-alt mr-1 text-primary"></i> Service Address:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->customer_address }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6 mb-3">
                    <table class="table table-sm table-borderless user-info-table mb-0 mt-2">
                        <tbody>
                            <tr>
                                <td class="text-muted font-weight-bold py-1" style="width: 40%;"><i class="fas fa-building mr-1 text-primary"></i> Unit / Suite / Floor:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->unit_suite_floor }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold py-1"><i class="fas fa-city mr-1 text-primary"></i> Suburb:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->suburb }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold py-1"><i class="fas fa-mail-bulk mr-1 text-primary"></i> Postcode:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->postcode }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold py-1"><i class="fas fa-sticky-note mr-1 text-primary"></i> Special Instructions:</td>
                                <td class="font-weight-semibold text-dark py-1">{{ $booking->special_instructions }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <!-- Service Questionnaire Answers Card -->
        @php
            $answersList = $booking->answers ?? [];
        @endphp
        <div class="customer-details-card mb-4">
            <h3><i class="far fa-list-alt text-primary mr-2"></i> Service Questionnaire Answers</h3>
            @if (!empty($answersList) && count($answersList) > 0)
                <div class="row">
                    @foreach ($answersList as $idx => $item)
                        <div class="col-md-6 mb-3">
                            <div class="p-3 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                                <div class="font-weight-bold text-dark mb-1" style="font-size: 13.5px;">
                                    <span class="badge badge-primary mr-2" style="font-size: 11px; padding: 3px 6px;">Q{{ $idx + 1 }}</span>
                                    {{ $item['question'] ?? 'Question' }}
                                </div>
                                <div class="text-secondary pl-4" style="font-size: 13px; line-height: 1.4;">
                                    <strong class="text-dark">Answer:</strong> {{ $item['answer'] ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted py-2 font-size-13">
                    <i class="far fa-question-circle mr-1"></i> No questionnaire answers recorded for this booking.
                </div>
            @endif
        </div>

        <!-- Full-Width Booking Details Update Card -->
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

                <!-- Row 1: Booking Status & Service -->
                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-status">Booking Status <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-tasks" aria-hidden="true"></i>
                            <select class="form-control" id="booking-status" name="status" required>
                                @foreach (\App\Enums\BookingStatus::cases() as $statusCase)
                                    <option value="{{ $statusCase->value }}" {{ (old('status', $booking->status) == $statusCase->value) ? 'selected' : '' }}>
                                        {{ $statusCase->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <small class="form-text text-muted">Changing status from Pending to Approved will notify the customer.</small>
                    </div>

                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-service">Service <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-broom" aria-hidden="true"></i>
                            <select class="form-control" id="booking-service" name="service_id" required>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" {{ (old('service_id', $booking->service_id) == $service->id) ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Schedule Date & Time Slot -->
                <div class="row">
                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-date">Schedule Date <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="far fa-calendar-alt" aria-hidden="true"></i>
                            <input type="date" class="form-control" id="booking-date" name="date" value="{{ old('date', $booking->date) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 form-group mb-4">
                        <label for="booking-slot">Time Slot <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            <input type="text" class="form-control" id="booking-slot" name="slot" value="{{ old('slot', $booking->slot) }}" placeholder="e.g. 09:00 AM" required>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Amount, Payment Status & Payment Method -->
                <div class="row">
                    <div class="col-md-4 form-group mb-4">
                        <label for="booking-amount">Amount ($) <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                            <input type="number" step="0.01" class="form-control" id="booking-amount" name="amount" value="{{ old('amount', number_format($booking->amount, 2, '.', '')) }}" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="col-md-4 form-group mb-4">
                        <label for="payment-status">Payment Status <span class="text-danger">*</span></label>
                        <div class="booking-input-icon">
                            <i class="fas fa-credit-card" aria-hidden="true"></i>
                            <select class="form-control" id="payment-status" name="payment_status" required>
                                <option value="pending" {{ strtolower(old('payment_status', $booking->payment_status)) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ strtolower(old('payment_status', $booking->payment_status)) == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ strtolower(old('payment_status', $booking->payment_status)) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="failed" {{ strtolower(old('payment_status', $booking->payment_status)) == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ strtolower(old('payment_status', $booking->payment_status)) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 form-group mb-4">
                        <label for="payment-method">Payment Method</label>
                        <div class="booking-input-icon">
                            <i class="fas fa-wallet" aria-hidden="true"></i>
                            <select class="form-control" id="payment-method" name="payment_method">
                                <option value="pending" {{ strtolower(old('payment_method', $booking->payment_method)) == 'pending' ? 'selected' : '' }}>Pending Payment</option>
                                <option value="Credit Card" {{ old('payment_method', $booking->payment_method) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="Cash" {{ old('payment_method', $booking->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ old('payment_method', $booking->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Wallet" {{ old('payment_method', $booking->payment_method) == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="pt-3 border-top d-flex align-items-center justify-content-end" style="gap: 12px;">
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary customers-primary-btn px-4 py-2" id="btn-submit-booking-edit">
                        <i class="fas fa-save mr-1" aria-hidden="true"></i> Update Booking Details
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#booking-edit-form').on('submit', function (e) {
                var currentForm = this;
                var statusVal = $('#booking-status').val();
                var amountVal = parseFloat($('#booking-amount').val() || 0);

                if (statusVal === 'approved' && amountVal === 0 && !$(currentForm).data('swal-confirmed')) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Approve Free Booking?',
                        text: 'You are approving this booking with an amount of $0.00. Are you sure you want to proceed?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0866e8',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-check mr-1"></i> Yes, Approve Booking',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary px-4',
                            cancelButton: 'btn btn-secondary px-4'
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $(currentForm).data('swal-confirmed', true);
                            currentForm.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush
