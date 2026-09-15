@extends('layouts.app')

@section('title', 'Booking Details #' . ($booking->booking_id ?? $booking->id))

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking.css') }}">
    <style>
        .details-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
            padding: 24px;
            height: 100%;
        }

        .details-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111c3a;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f4f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .info-table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 13.5px;
        }

        .info-table td.label-col {
            color: #64748b;
            font-weight: 600;
            width: 40%;
        }

        .info-table td.value-col {
            color: #0f172a;
            font-weight: 600;
        }

        .booking-id-pill {
            font-family: monospace;
            font-weight: 700;
            font-size: 13px;
            color: #0866e8;
            background: #f0f6fe;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .price-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 13.5px;
        }

        .price-summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .price-summary-row.total-row {
            border-top: 2px solid #0866e8;
            border-bottom: none;
            font-weight: 700;
            font-size: 16px;
            color: #0f172a;
            margin-top: 8px;
            padding-top: 14px;
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
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0">Booking Details</h1>
                    <span class="booking-id-pill ml-2">{{ $booking->booking_id ?? ('BK-' . sprintf('%03d', $booking->id)) }}</span>
                </div>
                <p class="text-muted mt-1 mb-0">Created on {{ $booking->created_at }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php
                    $statusEnum = \App\Enums\BookingStatus::tryFrom($booking->status) ?? \App\Enums\BookingStatus::PENDING;
                    $statusLower = strtolower($booking->status);
                @endphp
                <span class="badge {{ $statusEnum->badgeClass() }} font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 999px;">
                    <i class="fas fa-info-circle mr-1"></i> Status: {{ $statusEnum->label() }}
                </span>
                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-primary customers-primary-btn">
                    <i class="fas fa-edit mr-1"></i> Edit Booking
                </a>
            </div>
        </div>

        <!-- Row 1: Customer Profile & Service Address -->
        <div class="row mb-4">
            <!-- Customer Information Card -->
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-user-circle text-primary mr-2"></i> Customer Profile</span>
                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">ID #{{ $booking->user_id ?? $booking->id }}</span>
                    </h3>

                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $booking->customer_avatar }}" alt="{{ $booking->customer_name }}" class="rounded-circle mr-3" style="width: 60px; height: 60px; border: 2px solid #0866e8;">
                        <div>
                            <h4 class="mb-1 font-weight-bold text-dark" style="font-size: 18px;">{{ $booking->customer_name }}</h4>
                        </div>
                    </div>

                    <table class="table table-borderless info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="fas fa-envelope text-primary mr-2"></i> Email:</td>
                                <td class="value-col">{{ $booking->customer_email }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-phone text-primary mr-2"></i> Phone:</td>
                                <td class="value-col">{{ $booking->customer_phone }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-venus-mars text-primary mr-2"></i> Gender:</td>
                                <td class="value-col">{{ $booking->customer_gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Service Address & Location Details Card -->
            <div class="col-md-6">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-map-marker-alt text-primary mr-2"></i> Service Address &amp; Instructions</span>
                    </h3>

                    <table class="table table-borderless info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="fas fa-location-arrow text-primary mr-2"></i> Street Address:</td>
                                <td class="value-col">{{ $booking->customer_address }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-building text-primary mr-2"></i> Unit / Suite / Floor:</td>
                                <td class="value-col">{{ $booking->unit_suite_floor }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-city text-primary mr-2"></i> Suburb / Postcode:</td>
                                <td class="value-col">{{ $booking->suburb }} {{ $booking->postcode }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-sticky-note text-primary mr-2"></i> Special Instructions:</td>
                                <td class="value-col text-dark">{{ $booking->special_instructions }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="far fa-comment-alt text-primary mr-2"></i> Service Notes:</td>
                                <td class="value-col text-muted">{{ $booking->service_notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 2: Booking Schedule & Payment Details -->
        <div class="row mb-4">
            <!-- Booking & Schedule Details Card -->
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-calendar-alt text-primary mr-2"></i> Booking &amp; Schedule Details</span>
                    </h3>

                    <table class="table table-borderless info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="fas fa-broom text-primary mr-2"></i> Service Selected:</td>
                                <td class="value-col font-weight-bold text-primary" style="font-size: 15px;">{{ $booking->service_name }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="far fa-calendar-check text-primary mr-2"></i> Scheduled Date:</td>
                                <td class="value-col">
                                    <span class="text-dark font-weight-bold">
                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>{{ \Carbon\Carbon::parse($booking->date)->format('F d, Y (l)') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="far fa-clock text-primary mr-2"></i> Time Slot:</td>
                                <td class="value-col">
                                    <span class="badge badge-light border px-3 py-2" style="font-size: 13px; font-weight: 600;">
                                        <i class="far fa-clock mr-1 text-primary"></i>{{ $booking->slot }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-sync-alt text-primary mr-2"></i> Frequency:</td>
                                <td class="value-col"><span class="badge badge-info px-2 py-1">{{ $booking->frequency }}</span></td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-history text-primary mr-2"></i> Submitted At:</td>
                                <td class="value-col text-muted">{{ $booking->created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment & Billing Breakdown Card -->
            <div class="col-md-6">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-credit-card text-primary mr-2"></i> Payment &amp; Billing Breakdown</span>
                    </h3>

                    <div class="price-summary-wrapper mb-3">
                        <div class="price-summary-row">
                            <span class="text-muted">Subtotal</span>
                            <span class="font-weight-semibold text-dark">${{ number_format($booking->subtotal, 2) }}</span>
                        </div>
                        <div class="price-summary-row">
                            <span class="text-muted">Discount Amount</span>
                            <span class="font-weight-semibold text-danger">-${{ number_format($booking->discount_amount, 2) }}</span>
                        </div>
                        <div class="price-summary-row">
                            <span class="text-muted">Wallet Credit Used</span>
                            <span class="font-weight-semibold text-warning">-${{ number_format($booking->credit_used, 2) }}</span>
                        </div>
                        <div class="price-summary-row total-row">
                            <span>Total Amount</span>
                            <span class="text-primary">${{ number_format($booking->amount, 2) }}</span>
                        </div>
                    </div>

                    <table class="table table-borderless info-table mb-0 pt-2 border-top">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="fas fa-receipt text-primary mr-2"></i> Payment Status:</td>
                                <td class="value-col">
                                    @php
                                        $paymentLower = strtolower($booking->payment_status);
                                        $paymentBadgeClass = 'badge-success';
                                        if ($paymentLower === 'pending') {
                                            $paymentBadgeClass = 'badge-warning text-dark';
                                        } elseif ($paymentLower === 'unpaid') {
                                            $paymentBadgeClass = 'badge-warning text-dark';
                                        } elseif ($paymentLower === 'failed') {
                                            $paymentBadgeClass = 'badge-danger';
                                        } elseif ($paymentLower === 'refunded') {
                                            $paymentBadgeClass = 'badge-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $paymentBadgeClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-wallet text-primary mr-2"></i> Payment Method:</td>
                                <td class="value-col">{{ $booking->payment_method === 'pending' ? 'Pending Payment' : $booking->payment_method }}</td>
                            </tr>
                            @if ($booking->promo_code || $booking->referal_code)
                                <tr>
                                    <td class="label-col"><i class="fas fa-tag text-primary mr-2"></i> Applied Code:</td>
                                    <td class="value-col">
                                        <span class="badge badge-success px-2 py-1">{{ $booking->promo_code ?? $booking->referal_code }}</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 3: Service Questionnaire Answers (Full-Width Card) -->
        <div class="details-card mb-4">
            <h3>
                <span><i class="far fa-list-alt text-primary mr-2"></i> Service Questionnaire Answers</span>
            </h3>

            @php
                $answersList = $booking->answers ?? [];
            @endphp

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
                <div class="text-muted py-2" style="font-size: 13.5px;">
                    <i class="far fa-question-circle mr-1"></i> No questionnaire answers recorded for this booking.
                </div>
            @endif
        </div>
    </div>
@endsection
