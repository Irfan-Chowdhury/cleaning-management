@extends('layouts.app')

@section('title', 'Review & Confirm Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/booking_service.css') }}">
@endpush

@section('content')
    @php
        $serviceName = $latestBooking->service?->name ?? 'Cleaning Service';
        $frequencyLabel = ucfirst(str_replace('_', ' ', $latestBooking->frequency ?? 'one_time'));
        $formattedDate = $latestBooking->booking_date
            ? \Carbon\Carbon::parse($latestBooking->booking_date)->format('d M Y (D)')
            : 'N/A';
        $formattedTime = $latestBooking->start_time
            ? (\Carbon\Carbon::parse($latestBooking->start_time)->format('g:i A') . ($latestBooking->end_time ? ' - ' . \Carbon\Carbon::parse($latestBooking->end_time)->format('g:i A') : ''))
            : '09:00 AM';
        $appliedCode = $latestBooking->referal_code ?: ($latestBooking->promo_code ?: null);
        $answers = is_array($latestBooking->answers) ? $latestBooking->answers : [];
    @endphp

    <div class="booking-page">
        @include('pages.booking-service.partials.page-header')
        @include('pages.booking-service.partials.progress', ['currentStep' => 4, 'latestBooking' => $latestBooking])

        <div class="booking-main-grid">
            <div class="booking-left-column">
                <div class="booking-form-card">
                    <div class="booking-card-intro">
                        <h2>Step 4 of 4: Review &amp; Confirm</h2>
                        <p>Please review your booking details below and confirm to complete your booking.</p>
                    </div>

                    <!-- Service & Schedule -->
                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="fas fa-broom" aria-hidden="true"></i> Service &amp; Schedule</h3>
                            <span class="badge badge-info">Booking #BK-{{ sprintf('%03d', $latestBooking->id) }}</span>
                        </div>
                        <div class="review-service-grid">
                            <img src="https://picsum.photos/seed/dust2glow-review/240/160" alt="{{ $serviceName }}" class="review-image">
                            <div>
                                <h4>{{ $serviceName }}</h4>
                                <span class="booking-badge">{{ $frequencyLabel }}</span>
                                <ul class="summary-list mt-2">
                                    <li><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $formattedDate }}</li>
                                    <li><i class="far fa-clock" aria-hidden="true"></i> {{ $formattedTime }}</li>
                                    <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $latestBooking->customer_address }}</li>
                                </ul>
                            </div>
                            <div class="estimated-price-box">
                                <span>Total Price</span>
                                <strong>${{ number_format((float) $latestBooking->total_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="far fa-user" aria-hidden="true"></i> Your Details</h3>
                        </div>
                        <div class="review-details-grid">
                            <div class="review-contact-list">
                                <strong>{{ $latestBooking->customer_name ?: 'Customer' }}</strong>
                                <span><i class="far fa-envelope mr-1"></i> {{ $latestBooking->customer_email ?: 'N/A' }}</span>
                                <span><i class="fas fa-phone-alt mr-1"></i> {{ $latestBooking->customer_phone ?: 'N/A' }}</span>
                                <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $latestBooking->customer_address }}</span>
                            </div>
                            <dl class="review-detail-table">
                                <div><dt>Unit / Suite / Floor</dt><dd>{{ $latestBooking->unit_suite_floor ?: '-' }}</dd></div>
                                <div><dt>Suburb</dt><dd>{{ $latestBooking->suburb ?: '-' }}</dd></div>
                                <div><dt>Postcode</dt><dd>{{ $latestBooking->postcode ?: '-' }}</dd></div>
                                <div><dt>Special Instructions</dt><dd>{{ $latestBooking->special_instructions ?: 'None' }}</dd></div>
                            </dl>
                        </div>
                    </div>

                    <!-- Questionnaire Answers if any -->
                    @if (!empty($answers) && count($answers) > 0)
                        <div class="review-section">
                            <div class="review-section-header">
                                <h3><i class="fas fa-list-check" aria-hidden="true"></i> Service Requirements</h3>
                            </div>
                            <dl class="review-detail-table" style="grid-template-columns: 1fr;">
                                @foreach ($answers as $item)
                                    <div>
                                        <dt>{{ $item['question'] ?? 'Question' }}</dt>
                                        <dd>{{ $item['answer'] ?? 'N/A' }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endif

                    <!-- Payment & Offers -->
                    <div class="review-section">
                        <div class="review-section-header">
                            <h3><i class="fas fa-tag" aria-hidden="true"></i> Payment &amp; Offers</h3>
                        </div>
                        <div class="payment-review-grid">
                            @if ($appliedCode)
                                <div class="applied-promo-panel">
                                    <span>Referral / Promo Code</span>
                                    <strong>{{ $appliedCode }} <em>Applied</em></strong>
                                    <p>Discount: ${{ number_format((float) $latestBooking->discount_amount, 2) }}</p>
                                </div>
                            @else
                                <div class="applied-promo-panel">
                                    <span>Payment Status</span>
                                    <strong>{{ ucfirst($latestBooking->payment_status ?? 'Pending') }}</strong>
                                </div>
                            @endif
                            <div class="payment-total-panel">
                                <div><span>Subtotal</span><strong>${{ number_format((float) $latestBooking->subtotal, 2) }}</strong></div>
                                @if ((float) $latestBooking->discount_amount > 0)
                                    <div><span>Discount</span><strong>- ${{ number_format((float) $latestBooking->discount_amount, 2) }}</strong></div>
                                @endif
                                @if ((float) $latestBooking->credit_used > 0)
                                    <div><span>Wallet Credit</span><strong>- ${{ number_format((float) $latestBooking->credit_used, 2) }}</strong></div>
                                @endif
                                <hr>
                                <div class="grand-total"><span>Total</span><strong>${{ number_format((float) $latestBooking->total_amount, 2) }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Form -->
                    <form action="{{ route('booking-service.confirm') }}" method="POST" id="confirm-booking-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $latestBooking->id }}">
                        <div class="booking-step-actions review-actions">
                            <a href="{{ route('customer.bookings.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to My Bookings
                            </a>
                            <button type="submit" class="btn btn-primary confirm-pay-btn">
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                    <p class="secure-checkout"><i class="fas fa-shield-alt" aria-hidden="true"></i> Confirmation will lock in your schedule with Dust2Glow.</p>
                </div>
            </div>

            <!-- Sidebar Summary -->
            <aside class="booking-right-column">
                <div class="booking-summary-card filled-summary-card final-summary-card">
                    <div class="summary-title-row">
                        <h2>Booking Summary</h2>
                    </div>
                    <img src="https://picsum.photos/seed/dust2glow-final/420/260" alt="{{ $serviceName }}" class="summary-image">
                    <h3>{{ $serviceName }}</h3>
                    <span class="booking-badge">{{ $frequencyLabel }}</span>
                    <ul class="summary-list">
                        <li><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $formattedDate }}</li>
                        <li><i class="far fa-clock" aria-hidden="true"></i> {{ $formattedTime }}</li>
                        <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $latestBooking->customer_address }}</li>
                    </ul>
                    <div class="price-breakdown">
                        <div><span>Subtotal</span><strong>${{ number_format((float) $latestBooking->subtotal, 2) }}</strong></div>
                        @if ((float) $latestBooking->discount_amount > 0)
                            <div><span>Discount</span><strong class="discount-value">- ${{ number_format((float) $latestBooking->discount_amount, 2) }}</strong></div>
                        @endif
                        <div class="summary-total"><span>Total</span><strong>${{ number_format((float) $latestBooking->total_amount, 2) }}</strong></div>
                    </div>
                </div>

                @if ($appliedCode)
                    <div class="booking-side-card referral-success-card">
                        <h2><i class="fas fa-check-circle" aria-hidden="true"></i> Code {{ $appliedCode }} Applied</h2>
                        <p>Discount of ${{ number_format((float) $latestBooking->discount_amount, 2) }} applied to this booking.</p>
                    </div>
                @endif

                <div class="booking-side-card why-book-card">
                    <h2>Why book with Dust2Glow?</h2>
                    <ul>
                        <li><i class="fas fa-check" aria-hidden="true"></i> 100% Satisfaction Guarantee</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Police Checked &amp; Verified Cleaners</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Secure Payments</li>
                        <li><i class="fas fa-check" aria-hidden="true"></i> Trusted by 1,000+ Customers</li>
                    </ul>
                </div>

                @include('pages.booking-service.partials.support-card')
            </aside>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/booking_service.js') }}"></script>
@endpush
