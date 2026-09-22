<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ReferralService
{
    /**
     * Validate a referral code for a given user and booking subtotal.
     *
     * @param string $code
     * @param User $currentUser
     * @param float $bookingSubtotal
     * @return array
     */
    public function validateCode(string $code, User $currentUser, float $bookingSubtotal, ?int $currentBookingId = null): array
    {
        $code = trim($code);

        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::latest()->first();
        });

        $minBookingAmount = (float) ($settings?->minimum_booking_amount ?? 0);

        // 1. Check minimum booking amount requirement
        if ($bookingSubtotal < $minBookingAmount) {
            return [
                'valid'   => false,
                'message' => 'The total amount ($' . number_format($bookingSubtotal, 2) . ') is less than minimum booking amount ($' . number_format($minBookingAmount, 2) . ').',
            ];
        }

        // 2. Check if code exists in Users table (Referral Code)
        $referrer = User::where('referral_code', $code)->first();

        if (!$referrer) {
            return [
                'valid'   => false,
                'message' => 'The entered referral code does not exist. Please enter a valid referral code.',
            ];
        }

        // 3. Customer cannot refer themselves
        if ($referrer->id === $currentUser->id) {
            return [
                'valid'   => false,
                'message' => 'You cannot use your own referral code.',
            ];
        }

        // 4. Referrer completed booking requirement check: referrer must have at least 1 completed booking
        $hasCompletedBooking = Booking::where('user_id', $referrer->id)
            ->get()
            ->contains(function ($b) {
                $status = $b->status instanceof BookingStatus ? $b->status->value : strtolower((string) $b->status);
                return $status === 'completed';
            });

        if (!$hasCompletedBooking) {
            return [
                'valid'   => false,
                'message' => 'This referral code is not eligible yet. The referrer must have at least 1 completed booking.',
            ];
        }

        // 5. Check if user has any booking where status == 'completed' AND referal_code != NULL
        $hasCompletedReferralBooking = Booking::where('user_id', $currentUser->id)
            ->whereNotNull('referal_code')
            ->where('referal_code', '!=', '')
            ->where('status', 'completed')
            ->exists();

        if ($hasCompletedReferralBooking) {
            return [
                'valid'   => false,
                'message' => 'You can not use any referal code second time.',
            ];
        }

        $discountAmount = (float) ($settings?->referral_reward > 0 ? $settings->referral_reward : 10.00);

        // Keep exact original format of the referral code
        $codeToReturn = $referrer->referral_code ?? $code;

        return [
            'valid'           => true,
            'message'         => 'Referral code applied successfully!',
            'type'            => 'referral',
            'code'            => $codeToReturn,
            'discount_amount' => $discountAmount,
            'referrer_id'     => $referrer->id,
        ];
    }

    /**
     * Apply referral code to a booking instance and update session.
     *
     * @param Booking $booking
     * @param string $code
     * @param User $user
     * @return array
     */
    public function applyCodeToBooking(Booking $booking, string $code, User $user): array
    {
        $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);

        $result = $this->validateCode($code, $user, $subtotal, $booking->id);

        if (!$result['valid']) {
            return [
                'success' => false,
                'message' => $result['message'],
            ];
        }

        $discountAmount = (float) $result['discount_amount'];
        $newTotal = max(0, $subtotal - $discountAmount);

        $booking->referal_code = $result['code'];
        $booking->promo_code = null;
        $booking->subtotal = $subtotal;
        $booking->discount_amount = $discountAmount;
        $booking->credit_used = 0; // Reset wallet credit when code is applied
        $booking->total_amount = $newTotal;
        $booking->save();

        session(['booking_wizard.offer' => [
            'type'            => 'referral',
            'code'            => $result['code'],
            'discount_amount' => $discountAmount,
        ]]);

        return [
            'success'         => true,
            'message'         => $result['message'],
            'type'            => 'referral',
            'code'            => $result['code'],
            'discount_amount' => $discountAmount,
            'subtotal'        => $subtotal,
            'total_amount'    => $newTotal,
        ];
    }

    /**
     * Remove applied referral code from booking instance and clear session.
     *
     * @param Booking|null $booking
     * @return array
     */
    public function removeCodeFromBooking(?Booking $booking): array
    {
        if ($booking) {
            $subtotal = (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount);
            $booking->referal_code = null;
            $booking->promo_code = null;
            $booking->discount_amount = 0;
            $booking->total_amount = $subtotal;
            $booking->save();
        }

        session()->forget('booking_wizard.offer');

        return [
            'success'      => true,
            'message'      => 'Referral code removed successfully.',
            'subtotal'     => $booking ? (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount) : 0,
            'total_amount' => $booking ? (float) ($booking->subtotal > 0 ? $booking->subtotal : $booking->total_amount) : 0,
        ];
    }
}
