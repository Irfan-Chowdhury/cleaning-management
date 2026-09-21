<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Promotion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PromotionDiscountService
{
    /**
     * Validate a promotional code for a given user and booking subtotal.
     *
     * @param string $code
     * @param User $user
     * @param float $subtotal
     * @param int|null $bookingId
     * @return array
     */
    public function validatePromotionCode(string $code, User $user, float $subtotal, ?int $bookingId = null): array
    {
        $code = trim($code);

        if (empty($code)) {
            return [
                'valid'   => false,
                'message' => 'Please enter a valid promotional code.',
            ];
        }

        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::latest()->first();
        });

        $minBookingAmount = (float) ($settings?->minimum_booking_amount ?? 0);

        // 1. Check minimum booking amount requirement
        if ($subtotal < $minBookingAmount) {
            return [
                'valid'   => false,
                'message' => 'The total amount ($' . number_format($subtotal, 2) . ') is less than minimum booking amount ($' . number_format($minBookingAmount, 2) . ').',
            ];
        }

        // 2. Lookup promo code in database (case-insensitive search, stored uppercase)
        $promotion = Promotion::where('code', Str::upper($code))->first();

        if (!$promotion) {
            return [
                'valid'   => false,
                'message' => 'The entered promotional code does not exist. Please enter a valid code.',
            ];
        }

        // 3. Status check: must be active (1)
        $statusVal = \App\Enums\PromotionStatus::fromValue($promotion->status)->value;

        if ($statusVal !== \App\Enums\PromotionStatus::ACTIVE->value) {
            if ($statusVal === \App\Enums\PromotionStatus::EXPIRED->value) {
                return [
                    'valid'   => false,
                    'message' => 'This promotional code has expired.',
                ];
            }

            return [
                'valid'   => false,
                'message' => 'This promotional code is currently inactive.',
            ];
        }

        // 4. Date range validation
        $now = Carbon::now();

        if ($promotion->start_at && $now->lt($promotion->start_at)) {
            return [
                'valid'   => false,
                'message' => 'This promotional code is not yet active.',
            ];
        }

        if ($promotion->expires_at && $now->gt($promotion->expires_at)) {
            return [
                'valid'   => false,
                'message' => 'This promotional code has expired.',
            ];
        }

        // 5. Customer eligibility check
        $isNewCustomer = $this->isNewCustomer($user, $bookingId);

        if ($promotion->new_customers_only && !$isNewCustomer) {
            return [
                'valid'   => false,
                'message' => 'This promotional code is for new customers only.',
            ];
        }

        if ($promotion->existing_customers_only && $isNewCustomer) {
            return [
                'valid'   => false,
                'message' => 'This promotional code is for existing customers only.',
            ];
        }

        // 6. Discount calculation
        $discountValue = (float) $promotion->discount_value;
        $calculatedDiscount = 0.00;

        if ($promotion->discount_type === 'percentage') {
            if ($discountValue <= 0 || $discountValue > 100) {
                return [
                    'valid'   => false,
                    'message' => 'Invalid percentage discount configured for this promotional code.',
                ];
            }
            $calculatedDiscount = ($subtotal * $discountValue) / 100.0;
        } elseif ($promotion->discount_type === 'fixed') {
            if ($discountValue <= 0) {
                return [
                    'valid'   => false,
                    'message' => 'Invalid fixed discount configured for this promotional code.',
                ];
            }
            $calculatedDiscount = $discountValue;
        } else {
            return [
                'valid'   => false,
                'message' => 'Unsupported discount type for this promotional code.',
            ];
        }

        // Cap discount at subtotal so total payable never becomes negative
        $appliedDiscount = min($calculatedDiscount, $subtotal);
        $appliedDiscount = round($appliedDiscount, 2);
        $finalTotal = max(0.00, round($subtotal - $appliedDiscount, 2));

        return [
            'valid'           => true,
            'message'         => 'Promotional code applied successfully!',
            'type'            => 'promo',
            'code'            => $promotion->code,
            'promotion_id'    => $promotion->id,
            'discount_type'   => $promotion->discount_type,
            'discount_value'  => $discountValue,
            'discount_amount' => $appliedDiscount,
            'subtotal'        => $subtotal,
            'total_amount'    => $finalTotal,
        ];
    }

    /**
     * Determine whether the given user is a new customer.
     * A user is considered a new customer if they have no prior non-cancelled bookings.
     *
     * @param User $user
     * @param int|null $currentBookingId
     * @return bool
     */
    public function isNewCustomer(User $user, ?int $currentBookingId = null): bool
    {
        $query = Booking::where('user_id', $user->id);

        if ($currentBookingId) {
            $query->where('id', '!=', $currentBookingId);
        }

        return !$query->get()->contains(function ($b) {
            $status = $b->status instanceof BookingStatus ? $b->status->value : strtolower((string) $b->status);
            return in_array($status, ['completed', 'confirmed', 'approved', 'processing']);
        });
    }

    /**
     * Apply promotional code to a booking instance and update session.
     *
     * @param Booking $booking
     * @param string $code
     * @param User $user
     * @return array
     */
    public function applyPromotionToBooking(Booking $booking, string $code, User $user): array
    {
        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);

        $result = $this->validatePromotionCode($code, $user, $subtotal, $booking->id);

        if (!$result['valid']) {
            return [
                'success' => false,
                'message' => $result['message'],
            ];
        }

        $discountAmount = (float) $result['discount_amount'];
        $newTotal = max(0.00, $subtotal - $discountAmount);

        $booking->promo_code = $result['code'];
        $booking->referal_code = null;
        $booking->subtotal = $subtotal;
        $booking->discount_amount = $discountAmount;
        $booking->credit_used = 0; // Reset wallet credit when code is applied
        $booking->total_amount = $newTotal;
        $booking->save();

        session(['booking_wizard.offer' => [
            'type'            => 'promo',
            'code'            => $result['code'],
            'discount_amount' => $discountAmount,
        ]]);

        return [
            'success'         => true,
            'message'         => $result['message'],
            'type'            => 'promo',
            'code'            => $result['code'],
            'discount_amount' => $discountAmount,
            'subtotal'        => $subtotal,
            'total_amount'    => $newTotal,
        ];
    }

    /**
     * Remove applied promotional code from booking instance and clear session.
     *
     * @param Booking|null $booking
     * @return array
     */
    public function removePromotionFromBooking(?Booking $booking): array
    {
        if ($booking) {
            $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);
            $booking->promo_code = null;
            $booking->referal_code = null;
            $booking->discount_amount = 0;
            $booking->total_amount = $subtotal;
            $booking->save();
        }

        session()->forget('booking_wizard.offer');

        return [
            'success'      => true,
            'message'      => 'Promotional code removed successfully.',
            'subtotal'     => $booking ? (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount) : 0,
            'total_amount' => $booking ? (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount) : 0,
        ];
    }
}
