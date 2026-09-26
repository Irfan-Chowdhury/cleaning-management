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

    /**
     * Get dynamic customer referral dashboard metrics and referral history.
     *
     * @param User $user
     * @return array
     */
    public function getCustomerReferralData(User $user): array
    {
        // 1. Ensure referral code exists for customer
        $referralCode = $user->referral_code;
        if (empty($referralCode)) {
            $referralCode = strtoupper(($user->first_name ?: 'REF') . $user->id);
            $user->update(['referral_code' => $referralCode]);
        }

        $referralLink = url('/register?ref=' . $referralCode);
        $setting = Setting::first();
        $configuredReward = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

        // Fetch bookings where this user's referral code was used
        $referralBookings = Booking::with('user')
            ->where('referal_code', $referralCode)
            ->latest()
            ->get();

        // Dynamic Metric Calculations
        $totalReferrals = $referralBookings->pluck('user_id')->unique()->count();
        if ($totalReferrals === 0) {
            $totalReferrals = $referralBookings->count();
        }

        $pendingReferrals = $referralBookings->filter(function ($b) {
            $status = strtolower((string) ($b->status->value ?? $b->status));
            return in_array($status, ['pending', 'confirmed', 'processing', 'approved']);
        })->count();

        // Wallet rewards credited for referral bonuses
        $totalRewards = (float) \App\Models\WalletTransaction::where('user_id', $user->id)
            ->where('source', 'referral_bonus')
            ->sum('amount');

        if ($totalRewards == 0) {
            $completedCount = $referralBookings->filter(function ($b) {
                $status = strtolower((string) ($b->status->value ?? $b->status));
                return in_array($status, ['completed', 'rewarded', 'paid']);
            })->count();
            $totalRewards = $completedCount * $configuredReward;
        }

        // Map referral items for view
        $referrals = $referralBookings->map(function ($booking) use ($configuredReward) {
            $referredUser = $booking->user;
            $name = trim(($referredUser?->first_name ?? '') . ' ' . ($referredUser?->last_name ?? ''));
            if (empty($name)) {
                $name = $booking->customer_name ?: 'Referred Customer';
            }

            $avatar = $referredUser?->photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=0866e8&color=fff";
            $joinedDate = $referredUser?->created_at ? $referredUser->created_at->format('Y-m-d') : $booking->created_at->format('Y-m-d');
            $statusRaw = strtolower((string) ($booking->status->value ?? $booking->status));

            if (in_array($statusRaw, ['completed', 'rewarded'])) {
                $status = 'Rewarded';
                $rewardAmount = (float) ($booking->discount_amount > 0 ? $booking->discount_amount : $configuredReward);
            } elseif (in_array($statusRaw, ['confirmed', 'approved'])) {
                $status = 'Approved';
                $rewardAmount = 0.00;
            } elseif (in_array($statusRaw, ['cancelled', 'rejected'])) {
                $status = 'Rejected';
                $rewardAmount = 0.00;
            } else {
                $status = 'Pending';
                $rewardAmount = 0.00;
            }

            return (object) [
                'id'              => $booking->id,
                'customer_name'   => $name,
                'customer_avatar' => $avatar,
                'joined_date'     => $joinedDate,
                'status'          => $status,
                'booking_id'      => '#CL-' . $booking->id,
                'reward_amount'   => $rewardAmount,
            ];
        });

        return [
            'referralCode'     => $referralCode,
            'referralLink'     => $referralLink,
            'totalReferrals'   => $totalReferrals,
            'pendingReferrals' => $pendingReferrals,
            'totalRewards'     => $totalRewards,
            'referrals'        => $referrals,
        ];
    }

    /**
     * Get admin referral history list.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAdminReferralData()
    {
        $setting = Setting::first();
        $configuredReward = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

        $referralBookings = Booking::with(['user', 'service'])
            ->whereNotNull('referal_code')
            ->where('referal_code', '!=', '')
            ->latest()
            ->get();

        if ($referralBookings->isEmpty()) {
            return collect([
                (object)[
                    'id'              => 1,
                    'referrer_name'   => 'John Doe',
                    'referrer_email'  => 'john@example.com',
                    'referrer_avatar' => 'https://ui-avatars.com/api/?name=John+Doe&background=0D8ABC&color=fff&size=128',
                    'referred_name'   => 'Mary Jane',
                    'referred_email'  => 'mary@example.com',
                    'referred_avatar' => 'https://ui-avatars.com/api/?name=Mary+Jane&background=9c36b5&color=fff&size=128',
                    'referral_code'   => 'REF-JOHN25',
                    'reward_amount'   => 25.00,
                    'booking_service' => 'Deep Home Cleaning',
                    'created_at'      => '2026-08-15 10:30:00',
                ],
            ]);
        }

        return $referralBookings->map(function ($booking) use ($configuredReward) {
            $referredUser = $booking->user;
            $referrer = User::where('referral_code', $booking->referal_code)->first();

            $referrerName = $referrer ? trim($referrer->first_name . ' ' . $referrer->last_name) : 'Referrer User';
            $referredName = $referredUser ? trim($referredUser->first_name . ' ' . $referredUser->last_name) : ($booking->customer_name ?: 'Referred Customer');

            return (object) [
                'id'              => $booking->id,
                'referrer_name'   => $referrerName,
                'referrer_email'  => $referrer?->email ?: 'N/A',
                'referrer_avatar' => $referrer?->photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($referrerName),
                'referred_name'   => $referredName,
                'referred_email'  => $referredUser?->email ?: $booking->customer_email,
                'referred_avatar' => $referredUser?->photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($referredName),
                'referral_code'   => $booking->referal_code,
                'reward_amount'   => (float) ($booking->discount_amount > 0 ? $booking->discount_amount : $configuredReward),
                'booking_service' => $booking->service?->name ?: 'Cleaning Service',
                'created_at'      => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : now()->toDateTimeString(),
            ];
        });
    }
}
