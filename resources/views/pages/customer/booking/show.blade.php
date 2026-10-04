@extends('layouts.app')

@section('title', 'Booking Details #' . ('BK-' . sprintf('%03d', $booking->id)))

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    <style>
        .photo-preview-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .photo-preview-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
            border-color: #3b82f6 !important;
        }
        .photo-preview-card .overlay-icon {
            opacity: 0;
            transition: opacity 0.25s ease;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
        }
        .photo-preview-card:hover .overlay-icon {
            opacity: 1;
        }
        .mfp-close,
        .mfp-title {
            display: none !important;
        }
        .details-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
            padding: 24px;
            height: 100%;
        }

        .details-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f4f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .info-table td {
            padding: 9px 0;
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

        .booking-id-badge {
            font-family: monospace;
            font-weight: 700;
            font-size: 13px;
            color: #2563eb;
            background: #eff6ff;
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid #dbeafe;
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
            border-top: 2px solid #2563eb;
            border-bottom: none;
            font-weight: 700;
            font-size: 16px;
            color: #0f172a;
            margin-top: 8px;
            padding-top: 14px;
        }

        .home-photo-thumbnail {
            width: 100%;
            height: 130px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .home-photo-thumbnail:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Header -->
        <div class="customers-header mb-4">
            <div>
                <a href="{{ route('customer.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Back to My Bookings
                </a>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 font-weight-bold" style="font-size: 22px; color: #0f172a;">Booking Details</h1>
                    <span class="booking-id-badge ml-2">BK-{{ sprintf('%03d', $booking->id) }}</span>
                </div>
                <p class="text-muted mt-1 mb-0" style="font-size: 12.5px;">Submitted on {{ $booking->created_at ? $booking->created_at->format('F d, Y \a\t g:i A') : 'N/A' }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $booking->status_badge_class }} font-weight-bold px-3 py-2 mr-2" style="font-size: 14px; border-radius: 999px;">
                    <i class="fas fa-info-circle mr-1"></i> Status: {{ $booking->status_label }}
                </span>

                @if ($booking->status_raw === 'approved')
                    <a href="{{ route('booking-service.review-confirm', ['booking' => $booking->id]) }}" class="btn btn-success font-weight-bold px-3 py-2" style="border-radius: 8px; font-size: 13.5px;">
                        <i class="fas fa-calendar-check mr-1"></i> Proceed to Step 4 (Review &amp; Confirm)
                    </a>
                @endif
            </div>
        </div>

        <!-- Row 1: Service Details & Schedule + Pricing -->
        <div class="row mb-4">
            <!-- Service & Schedule Card -->
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-broom text-primary mr-2"></i> Service &amp; Schedule Details</span>
                    </h3>

                    <div class="d-flex align-items-center mb-3 p-3 rounded" style="background-color: #eff6ff; border: 1px solid #dbeafe;">
                        <div class="mr-3 text-primary" style="font-size: 26px;">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 16px;">{{ $booking->service->name ?? 'Cleaning Service' }}</h4>
                            <span class="badge badge-info mt-1" style="font-size: 11px;">{{ ucfirst(str_replace('_', ' ', $booking->frequency ?? 'one_time')) }}</span>
                        </div>
                    </div>

                    <table class="table table-borderless info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="far fa-calendar-alt text-primary mr-2"></i> Date:</td>
                                <td class="value-col">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y (l)') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="far fa-clock text-primary mr-2"></i> Time Slot:</td>
                                <td class="value-col">{{ $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('g:i A') : '09:00 AM' }}</td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-map-marker-alt text-primary mr-2"></i> Address:</td>
                                <td class="value-col">{{ $booking->customer_address ?? 'N/A' }}</td>
                            </tr>
                            @if (!empty($booking->unit_suite_floor))
                                <tr>
                                    <td class="label-col"><i class="fas fa-building text-primary mr-2"></i> Unit / Suite:</td>
                                    <td class="value-col">{{ $booking->unit_suite_floor }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment & Price Summary Card -->
            <div class="col-md-6">
                <div class="details-card">
                    <h3>
                        <span><i class="fas fa-receipt text-primary mr-2"></i> Payment Summary</span>
                    </h3>

                    @php
                        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);
                        $discountVal = (float) ($booking->discount_amount > 0 ? $booking->discount_amount : $booking->credit_used);
                        $totalVal = (float) $booking->total_amount;

                        $discountLabel = 'Discount';
                        if ((float) $booking->credit_used > 0) {
                            $discountLabel = 'Wallet Discount';
                        } elseif (!empty($booking->referal_code)) {
                            $discountLabel = 'Referral Code Discount';
                        } elseif (!empty($booking->promo_code)) {
                            $discountLabel = 'Promo Code Discount';
                        }
                    @endphp

                    <div class="price-summary-wrapper mb-3">
                        <div class="price-summary-row">
                            <span class="text-muted">Subtotal</span>
                            <span class="font-weight-semibold text-dark">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if ($discountVal > 0)
                            <div class="price-summary-row">
                                <span class="text-muted">{{ $discountLabel }}</span>
                                <span class="font-weight-semibold text-danger">-${{ number_format($discountVal, 2) }}</span>
                            </div>
                        @endif
                        <div class="price-summary-row total-row">
                            <span>Total Amount</span>
                            <span class="text-primary">${{ number_format($totalVal, 2) }}</span>
                        </div>
                    </div>

                    <table class="table table-borderless info-table mb-0 pt-2 border-top">
                        <tbody>
                            <tr>
                                <td class="label-col"><i class="fas fa-wallet text-primary mr-2"></i> Payment Status:</td>
                                <td class="value-col">
                                    @php
                                        $paymentStatusRaw = strtolower($booking->payment?->payment_status ?? $booking->payment_status ?? 'pending');
                                        $pBadge = $paymentStatusRaw === 'paid' ? 'badge-success' : 'badge-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $pBadge }}" style="padding: 5px 10px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($paymentStatusRaw) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-col"><i class="fas fa-credit-card text-primary mr-2"></i> Payment Method:</td>
                                <td class="value-col">{{ $booking->payment_method === 'pending' ? 'Pending Payment' : $booking->payment_method }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Row 2: Customer Uploaded Home Photos Section -->
        <div class="details-card mb-4">
            <h3>
                <span><i class="fas fa-images text-primary mr-2"></i> Uploaded Home Photos</span>
                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">{{ !empty($booking->images) ? count($booking->images) : 0 }} Photos</span>
            </h3>

            @if (!empty($booking->images) && count($booking->images) > 0)
                <div class="row image-gallery-popup">
                    @foreach ($booking->images as $img)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                            <a href="{{ asset('public/' . $img->image_path) }}" class="gallery-photo-link d-block photo-preview-card position-relative shadow-sm" style="height: 120px; background: #f8fafc;" title="{{ $img->image_name ?? 'Uploaded Home Photo' }}">
                                <img src="{{ asset('public/' . $img->image_path) }}" alt="{{ $img->image_name ?? 'Home Photo' }}" style="width: 100%; height: 100%; object-fit: cover;">
                                <div class="overlay-icon position-absolute w-100 h-100 d-flex align-items-center justify-content-center text-white" style="top:0; left:0;">
                                    <i class="fas fa-search-plus" style="font-size: 20px;"></i>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted py-2" style="font-size: 13.5px;">
                    <i class="far fa-image mr-1"></i> No home photos were uploaded for this booking during Step 1.
                </div>
            @endif
        </div>

        <!-- Row 3: Service Notes & Questionnaire Answers -->
        <div class="details-card mb-4">
            <h3>
                <span><i class="far fa-list-alt text-primary mr-2"></i> Service Notes &amp; Questionnaire</span>
            </h3>

            @if (!empty($booking->service_notes))
                <div class="mb-3 p-3 rounded border" style="background-color: #f8fafc; border-color: #e2e8f0 !important;">
                    <strong class="d-block text-dark mb-1" style="font-size: 13.5px;"><i class="far fa-sticky-note text-primary mr-1"></i> Customer Notes:</strong>
                    <p class="mb-0 text-secondary" style="font-size: 13px; line-height: 1.5;">{{ $booking->service_notes }}</p>
                </div>
            @endif

            @php
                $answersList = $booking->answers ?? [];
            @endphp

            @if (!empty($answersList) && count($answersList) > 0)
                <div class="row">
                    @foreach ($answersList as $idx => $item)
                        <div class="col-md-6 mb-3">
                            <div class="p-3 rounded border" style="background-color: #ffffff; border-color: #e2e8f0 !important;">
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
            @elseif (empty($booking->service_notes))
                <div class="text-muted py-2" style="font-size: 13.5px;">
                    <i class="far fa-question-circle mr-1"></i> No special notes or questionnaire answers provided for this booking.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.image-gallery-popup').magnificPopup({
                delegate: 'a.gallery-photo-link',
                type: 'image',
                showCloseBtn: false,
                gallery: {
                    enabled: true,
                    navigateByImgClick: true,
                    preload: [0, 1]
                },
                image: {
                    titleSrc: function() {
                        return '';
                    }
                },
                zoom: {
                    enabled: true,
                    duration: 300,
                    easing: 'ease-in-out',
                    opener: function(openerElement) {
                        return openerElement.is('img') ? openerElement : openerElement.find('img');
                    }
                }
            });
        });
    </script>
@endpush
