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
                                <strong id="main-estimated-total">${{ number_format((float) $latestBooking->total_amount, 2) }}</strong>
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
                            @if (!empty($latestBooking->referal_code))
                                <div class="applied-promo-panel">
                                    <span>Referral Code</span>
                                    <strong>{{ $latestBooking->referal_code }} <em>Applied</em></strong>
                                    <p>Discount: ${{ number_format((float) $latestBooking->discount_amount, 2) }}</p>
                                </div>
                            @else
                                <div class="applied-promo-panel">
                                    <span>Payment Status</span>
                                    <strong>{{ ucfirst($latestBooking->payment_status ?? 'Pending') }}</strong>
                                </div>
                            @endif
                            <div class="payment-total-panel">
                                <div><span>Subtotal</span><strong id="main-subtotal-val">${{ number_format((float) ($latestBooking->subtotal > 0 ? $latestBooking->subtotal : $latestBooking->total_amount), 2) }}</strong></div>
                                <div id="main-discount-row" style="{{ (float) $latestBooking->discount_amount > 0 ? '' : 'display: none;' }}">
                                    <span>Discount / Wallet</span>
                                    <strong id="main-discount-val" class="text-success">- ${{ number_format((float) $latestBooking->discount_amount, 2) }}</strong>
                                </div>
                                <hr>
                                <div class="grand-total"><span>Total</span><strong id="main-grand-total-val">${{ number_format((float) $latestBooking->total_amount, 2) }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Form -->
                    <form action="{{ route('booking-service.confirm') }}" method="POST" id="confirm-booking-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $latestBooking->id }}">
                        <input type="hidden" name="wallet_amount" id="applied-wallet-amount-hidden" value="0">
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

            <!-- Sidebar -->
            <aside class="booking-right-column">
                @include('pages.booking-service.partials.promo-card')

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
    <script>
        $(document).ready(function () {
            // Radio button toggle logic
            $('input[name="offer_type"]').on('change', function () {
                var val = $(this).val();
                $('.discount-radio-card').removeClass('active');
                $(this).closest('.discount-radio-card').addClass('active');

                if (val === 'wallet') {
                    $('#wallet-offer-panel').slideDown(200);
                    $('#promo-offer-panel').slideUp(200);
                } else {
                    $('#wallet-offer-panel').slideUp(200);
                    $('#promo-offer-panel').slideDown(200);
                    // Reset wallet deduction when switching to promo/referral
                    $('#wallet-amount-input').val('').trigger('input');

                    if (val === 'promo') {
                        $('#code-section-title').text('Have a Promotional Code?');
                        $('#promo-code-input').attr('placeholder', 'Enter promo code');
                        $('#promo-note-text').text('Use a promotional code to save on your booking!');
                        $('#referral-block-alert').hide();
                    } else {
                        $('#code-section-title').text('Have a Referral Code?');
                        $('#promo-code-input').attr('placeholder', 'Enter referral code');
                        $('#promo-note-text').text('Use a referral code and get credit when you book!');
                        $('#referral-block-alert').show();
                    }
                }
            });

            // Live Wallet Calculation
            $('#wallet-amount-input').on('input keyup change', function () {
                var $card = $('#discount-offer-card');
                var subtotal = parseFloat($card.attr('data-subtotal')) || 0;
                var minAmount = parseFloat($card.attr('data-min-amount')) || 0;
                var maxWalletUsage = parseFloat($card.attr('data-max-wallet-usage')) || 0;
                var walletBalance = parseFloat($card.attr('data-wallet-balance')) || 0;

                var rawVal = $(this).val();
                var inputVal = parseFloat(rawVal) || 0;
                var $feedback = $('#wallet-feedback-msg');

                if (!rawVal || inputVal <= 0) {
                    $feedback.hide().html('');
                    updateLivePricing(subtotal, 0);
                    return;
                }

                if (subtotal < minAmount) {
                    $feedback.show().html('<span class="text-danger"><i class="fas fa-times-circle"></i> The total amount ($' + subtotal.toFixed(2) + ') is less than minimum booking amount ($' + minAmount.toFixed(2) + '), wallet cannot be used.</span>');
                    updateLivePricing(subtotal, 0);
                    return;
                }

                if (inputVal > walletBalance) {
                    $feedback.show().html('<span class="text-danger"><i class="fas fa-times-circle"></i> Insufficient balance. Remaining wallet balance is $' + walletBalance.toFixed(2) + '.</span>');
                    updateLivePricing(subtotal, 0);
                    return;
                }

                if (maxWalletUsage > 0 && inputVal > maxWalletUsage) {
                    $feedback.show().html('<span class="text-danger"><i class="fas fa-times-circle"></i> Maximum wallet usage allowed per booking is $' + maxWalletUsage.toFixed(2) + '.</span>');
                    updateLivePricing(subtotal, 0);
                    return;
                }

                if (inputVal > subtotal) {
                    $feedback.show().html('<span class="text-danger"><i class="fas fa-times-circle"></i> Wallet amount cannot exceed the booking subtotal ($' + subtotal.toFixed(2) + ').</span>');
                    updateLivePricing(subtotal, 0);
                    return;
                }

                // Valid amount!
                $feedback.show().html('<span class="text-success"><i class="fas fa-check-circle"></i> Wallet credit of $' + inputVal.toFixed(2) + ' applied!</span>');
                updateLivePricing(subtotal, inputVal);
            });

            // Apply Referral or Promo Code via AJAX
            $('#btn-apply-promo').on('click', function (e) {
                e.preventDefault();
                var code = $('#promo-code-input').val().trim();
                var bookingId = $('input[name="booking_id"]').val();
                var offerType = $('input[name="offer_type"]:checked').val() || 'promo';
                var $feedback = $('#promo-feedback-msg');

                if (!code) {
                    var emptyMsg = offerType === 'promo' ? 'Please enter a valid promotional code.' : 'Please enter a valid referral code.';
                    $feedback.show().html('<i class="fas fa-exclamation-circle mr-1"></i> ' + emptyMsg);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Code Required',
                            text: emptyMsg,
                            confirmButtonColor: '#2563eb'
                        });
                    }
                    return;
                }

                $feedback.hide().text('');

                $.ajax({
                    url: "{{ route('booking-service.apply-promo') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        booking_id: bookingId,
                        code: code,
                        type: offerType
                    },
                    success: function (res) {
                        if (res.success) {
                            $('#applied-code-text').text(res.code);
                            $('#applied-discount-text').text('- $' + parseFloat(res.discount_amount).toFixed(2));

                            $('#promo-input-wrapper').hide();
                            $('#promo-applied-wrapper').slideDown(200);

                            updateLivePricing(res.subtotal, res.discount_amount);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Offer Applied!',
                                    text: res.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        }
                    },
                    error: function (xhr) {
                        var errorMsg = 'Failed to apply code. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $feedback.show().html('<i class="fas fa-times-circle mr-1"></i> ' + errorMsg);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Discount Code Error',
                                text: errorMsg,
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    }
                });
            });

            // Remove Referral or Promo Code via AJAX with SweetAlert Confirmation
            $(document).on('click', '#btn-remove-promo', function (e) {
                e.preventDefault();
                var bookingId = $('input[name="booking_id"]').val();

                var performRemoval = function () {
                    $.ajax({
                        url: "{{ route('booking-service.remove-promo') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            booking_id: bookingId
                        },
                        success: function (res) {
                            $('#promo-code-input').val('');
                            $('#promo-feedback-msg').hide().text('');
                            $('#promo-applied-wrapper').hide();
                            $('#promo-input-wrapper').slideDown(200);

                            updateLivePricing(res.subtotal, 0);

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Offer Removed',
                                    text: 'Discount code has been removed.',
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Remove Offer Code?',
                        text: 'Are you sure you want to remove this discount code?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, remove it!'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            performRemoval();
                        }
                    });
                } else {
                    if (confirm('Are you sure you want to remove this discount code?')) {
                        performRemoval();
                    }
                }
            });

            function updateLivePricing(subtotal, discount) {
                var finalTotal = Math.max(0, subtotal - discount);

                $('#applied-wallet-amount-hidden').val(discount);

                // Update main review panel price box & payment breakdown
                $('#main-estimated-total').text('$' + finalTotal.toFixed(2));
                if (discount > 0) {
                    $('#main-discount-row').show();
                    $('#main-discount-val').text('- $' + discount.toFixed(2));
                } else {
                    $('#main-discount-row').hide();
                }
                $('#main-grand-total-val').text('$' + finalTotal.toFixed(2));

                // Update right sidebar summary card if present
                if ($('#summary-discount-row').length) {
                    if (discount > 0) {
                        $('#summary-discount-row').show();
                        $('#summary-discount-val').text('- $' + discount.toFixed(2));
                    } else {
                        $('#summary-discount-row').hide();
                    }
                }
                if ($('#summary-total-val').length) {
                    $('#summary-total-val').text('$' + finalTotal.toFixed(2));
                }
            }
        });
    </script>
@endpush
