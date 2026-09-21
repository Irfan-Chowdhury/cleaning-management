@php
    use App\Models\Setting;
    use App\Models\WalletTransaction;
    use Illuminate\Support\Facades\Cache;

    $settings = $settings ?? Cache::rememberForever('app_settings', function () {
        return Setting::latest()->first();
    });

    $user = auth()->user();
    if (!isset($userWalletBalance)) {
        $dbTx = WalletTransaction::where('user_id', $user->id ?? 0)->get();
        $userWalletBalance = max(0, (float) $dbTx->where('type', 'credit')->sum('amount') - (float) $dbTx->where('type', 'debit')->sum('amount'));
    }

    $minBookingAmount = (float) ($settings?->minimum_booking_amount ?? 0);
    $maxWalletUsage = (float) ($settings?->max_wallet_usage ?? 0);
    $subtotalAmount = (float) (isset($latestBooking) && $latestBooking->subtotal > 0 ? $latestBooking->subtotal : ($latestBooking->total_amount ?? 0));
    $isMinAmountValid = ($subtotalAmount >= $minBookingAmount);

    $appliedCode = isset($latestBooking) && ($latestBooking->referal_code || $latestBooking->promo_code)
        ? ($latestBooking->referal_code ?: $latestBooking->promo_code)
        : session('booking_wizard.offer.code');
    $appliedDiscount = (float) (isset($latestBooking) && $latestBooking->discount_amount > 0
        ? $latestBooking->discount_amount
        : session('booking_wizard.offer.discount_amount', 0));

    $appliedOfferType = isset($latestBooking) && $latestBooking->promo_code
        ? 'promo'
        : (isset($latestBooking) && $latestBooking->referal_code
            ? 'referral'
            : session('booking_wizard.offer.type', 'wallet'));

    $hasActiveCode = !empty($appliedCode) && $appliedDiscount > 0;

    $hasCompletedReferralBooking = false;
    if ($user) {
        $hasCompletedReferralBooking = \App\Models\Booking::where('user_id', $user->id)
            ->whereNotNull('referal_code')
            ->where('referal_code', '!=', '')
            ->where('status', 'completed')
            ->exists();
    }
@endphp

<div class="booking-side-card booking-discount-offer-card mb-3"
     id="discount-offer-card"
     data-subtotal="{{ $subtotalAmount }}"
     data-min-amount="{{ $minBookingAmount }}"
     data-max-wallet-usage="{{ $maxWalletUsage }}"
     data-wallet-balance="{{ $userWalletBalance }}">

    <!-- Section Header -->
    <div class="discount-offer-header mb-3">
        <h2 class="discount-offer-title font-weight-bold d-flex align-items-center mb-1" style="font-size: 16px; color: #0f172a; line-height: 1.3;">
            <i class="fas fa-gift text-primary mr-2" aria-hidden="true" style="font-size: 17px;"></i>
            Discount Offer
        </h2>
        <p class="discount-offer-subtitle text-muted m-0" style="font-size: 12px; color: #64748b; line-height: 1.4; padding-left: 25px;">
            Choose how you would like to save on this booking.
        </p>
    </div>

    @if ($minBookingAmount > 0)
        <div class="alert alert-info p-2 mb-3" style="font-size: 11.5px; border-radius: 8px; line-height: 1.4; background-color: #f0f9ff; border-color: #bae6fd; color: #0369a1;">
            <i class="fas fa-info-circle mr-1 text-info"></i>
            <strong>Note:</strong> Minimum total booking amount to apply offer is <strong>${{ number_format($minBookingAmount, 2) }}</strong>.
        </div>
    @endif

    <!-- Discount Type Section -->
    <div class="discount-type-group mb-3">
        <label class="discount-type-label d-block mb-2 font-weight-bold" style="font-size: 11px; color: #334155; text-transform: uppercase; letter-spacing: 0.4px;">
            Discount Type
        </label>

        <div class="discount-radios d-flex flex-column" style="gap: 10px;">
            <label class="discount-radio-card {{ $appliedOfferType === 'wallet' ? 'active' : '' }} p-3 border rounded-lg d-flex align-items-start m-0" for="offer-type-wallet" style="cursor: pointer;">
                <input type="radio" name="offer_type" value="wallet" id="offer-type-wallet" class="discount-radio-input mt-1" {{ $appliedOfferType === 'wallet' ? 'checked' : '' }} style="accent-color: #2563eb; width: 16px; height: 16px; flex-shrink: 0; margin-right: 14px;">
                <div class="discount-card-body d-flex align-items-start">
                    <span class="discount-card-icon text-primary mr-2" style="font-size: 15px; margin-top: 1px; flex-shrink: 0; margin-left: 4px;">
                        <i class="fas fa-wallet" aria-hidden="true"></i>
                    </span>
                    <div>
                        <strong class="discount-card-title d-block" style="font-size: 13.5px; color: #0f172a; line-height: 1.3;">Use Wallet</strong>
                        <span class="discount-card-desc d-block text-muted" style="font-size: 11.5px; color: #64748b; line-height: 1.35; margin-top: 2px;">Apply your available wallet balance credit.</span>
                    </div>
                </div>
            </label>

            <label class="discount-radio-card {{ $appliedOfferType === 'referral' ? 'active' : '' }} p-3 border rounded-lg d-flex align-items-start m-0" for="offer-type-referral" style="cursor: pointer;">
                <input type="radio" name="offer_type" value="referral" id="offer-type-referral" class="discount-radio-input mt-1" {{ $appliedOfferType === 'referral' ? 'checked' : '' }} style="accent-color: #2563eb; width: 16px; height: 16px; flex-shrink: 0; margin-right: 14px;">
                <div class="discount-card-body d-flex align-items-start">
                    <span class="discount-card-icon text-primary mr-2" style="font-size: 15px; margin-top: 1px; flex-shrink: 0; margin-left: 4px;">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </span>
                    <div>
                        <strong class="discount-card-title d-block" style="font-size: 13.5px; color: #0f172a; line-height: 1.3;">Referral Code</strong>
                        <span class="discount-card-desc d-block text-muted" style="font-size: 11.5px; color: #64748b; line-height: 1.35; margin-top: 2px;">Apply a referral discount code.</span>
                    </div>
                </div>
            </label>

            <label class="discount-radio-card {{ $appliedOfferType === 'promo' ? 'active' : '' }} p-3 border rounded-lg d-flex align-items-start m-0" for="offer-type-promo" style="cursor: pointer;">
                <input type="radio" name="offer_type" value="promo" id="offer-type-promo" class="discount-radio-input mt-1" {{ $appliedOfferType === 'promo' ? 'checked' : '' }} style="accent-color: #2563eb; width: 16px; height: 16px; flex-shrink: 0; margin-right: 14px;">
                <div class="discount-card-body d-flex align-items-start">
                    <span class="discount-card-icon text-primary mr-2" style="font-size: 15px; margin-top: 1px; flex-shrink: 0; margin-left: 4px;">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                    </span>
                    <div>
                        <strong class="discount-card-title d-block" style="font-size: 13.5px; color: #0f172a; line-height: 1.3;">Promotional Offer</strong>
                        <span class="discount-card-desc d-block text-muted" style="font-size: 11.5px; color: #64748b; line-height: 1.35; margin-top: 2px;">Apply a promo code discount.</span>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <!-- Option 1: Use Wallet Panel -->
    <div id="wallet-offer-panel" class="discount-panel-section" style="{{ $appliedOfferType === 'wallet' ? 'display: block;' : 'display: none;' }}">
        <!-- Wallet Summary Box -->
        <div class="wallet-balance-summary mb-3 p-3 rounded-lg" style="background-color: #eff6ff; border: 1px solid #dbeafe; border-radius: 10px;">
            <div class="d-flex flex-column" style="gap: 8px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="wallet-summary-label" style="font-size: 12.5px; color: #475569; font-weight: 500;">Available Wallet Balance</span>
                    <strong class="wallet-summary-value text-primary font-weight-bold" style="font-size: 14px; white-space: nowrap; color: #2563eb;">${{ number_format($userWalletBalance, 2) }}</strong>
                </div>
                @if ($maxWalletUsage > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="wallet-summary-label" style="font-size: 12px; color: #64748b; font-weight: 500;">Max usage per booking</span>
                        <strong class="wallet-summary-value font-weight-bold" style="font-size: 13px; white-space: nowrap; color: #1e293b;">${{ number_format($maxWalletUsage, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Amount Input Section -->
        <div class="form-group mb-2">
            <label for="wallet-amount-input" class="d-block mb-1.5 font-weight-bold" style="font-size: 12px; color: #0f172a;">Amount to use</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text font-weight-bold" style="background: #f1f5f9; color: #334155; font-size: 14px; border-color: #cbd5e1; border-top-left-radius: 8px; border-bottom-left-radius: 8px; padding: 0 14px;">$</span>
                </div>
                <input type="number"
                       id="wallet-amount-input"
                       class="form-control wallet-input-field"
                       step="0.01"
                       min="0"
                       max="{{ min($userWalletBalance, $maxWalletUsage > 0 ? $maxWalletUsage : $userWalletBalance) }}"
                       placeholder="0.00"
                       style="font-size: 14px; height: 44px; border-color: #cbd5e1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; color: #0f172a; font-weight: 600;">
            </div>
        </div>

        <div id="wallet-feedback-msg" class="small mt-2 font-weight-semibold" style="display: none; font-size: 12px;"></div>
    </div>

    <!-- Option 2 & 3: Referral / Promo Code Panel -->
    <div id="promo-offer-panel" class="discount-panel-section" style="{{ $appliedOfferType !== 'wallet' ? 'display: block;' : 'display: none;' }}">
        <h3 class="mb-2 font-weight-bold text-dark" id="code-section-title" style="font-size: 13px;">
            {{ $appliedOfferType === 'promo' ? 'Have a Promotional Code?' : 'Have a Referral Code?' }}
        </h3>

        @if ($hasCompletedReferralBooking && !$hasActiveCode && $appliedOfferType === 'referral')
            <div class="alert alert-danger p-2.5 mb-0 mt-2" id="referral-block-alert" style="font-size: 12px; border-radius: 8px; line-height: 1.45; background-color: #fef2f2; border-color: #fecaca; color: #991b1b;">
                <i class="fas fa-times-circle mr-1 text-danger"></i>
                You can not use any referal code second time.
            </div>
        @endif

        <!-- Input Wrapper (Visible when no code applied) -->
        <div id="promo-input-wrapper" style="{{ $hasActiveCode ? 'display: none;' : 'display: block;' }}">
            <div class="booking-promo-row">
                <div class="booking-input-icon">
                    <i class="fas fa-tag" aria-hidden="true"></i>
                    <input type="text" id="promo-code-input" class="form-control" placeholder="{{ $appliedOfferType === 'promo' ? 'Enter promo code' : 'Enter referral code' }}">
                </div>
                <button type="button" class="btn btn-primary" id="btn-apply-promo">Apply</button>
            </div>
            <div id="promo-feedback-msg" class="small mt-1.5 text-danger font-weight-semibold" style="display: none; font-size: 12px; color: #dc2626;"></div>
            <div class="booking-promo-note mt-2" id="promo-default-note">
                <i class="fas fa-gift" aria-hidden="true"></i>
                <span id="promo-note-text">
                    {{ $appliedOfferType === 'promo' ? 'Use a promotional code to save on your booking!' : 'Use a referral code and get credit when you book!' }}
                </span>
            </div>
        </div>

        <!-- Applied Success Wrapper (Visible when code applied) -->
        <div id="promo-applied-wrapper" class="p-2.5 rounded border border-success mt-2" style="background-color: #f0fdf4; border-color: #bbf7d0 !important; {{ $hasActiveCode ? 'display: block;' : 'display: none;' }}">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success mr-2" style="font-size: 18px;"></i>
                    <div>
                        <strong class="text-dark d-block" style="font-size: 13px;">Code <span id="applied-code-text" class="text-success font-weight-bold">{{ $appliedCode }}</span> Applied!</strong>
                        <small class="text-muted d-block" style="font-size: 11.5px;">Discount: <strong id="applied-discount-text" class="text-success">-${{ number_format($appliedDiscount, 2) }}</strong></small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-1 m-0" id="btn-remove-promo" title="Remove Code" style="text-decoration: none;">
                    <i class="fas fa-times-circle" style="font-size: 19px;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
