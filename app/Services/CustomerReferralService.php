<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

class CustomerReferralService
{
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

        $referralLink = url('/register?ref=' . $referralCode);
        $setting = Setting::first();
        $configuredReward = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 00.00);

        // Fetch bookings where this user's referral code was used
        $referralBookings = Booking::with('user')
            ->where('referal_code', $referralCode)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        // Dynamic Metric Calculations
        $totalReferrals = $referralBookings->pluck('user_id')->unique()->count();
        if ($totalReferrals === 0) {
            $totalReferrals = $referralBookings->count();
        }

        $pendingReferrals = $referralBookings->filter(function ($b) {
            $status = strtolower((string) ($b->status->value ?? $b->status));
            $paymentStatus = strtolower((string) ($b->payment_status ?? 'pending'));

            if (in_array($status, ['completed'])) {
                return $paymentStatus !== 'paid';
            }

            return in_array($status, ['pending', 'confirmed', 'processing', 'approved']);
        })->count();

        // Wallet rewards credited for referral bonuses
        $totalRewards = (float) WalletTransaction::where('user_id', $user->id)
            ->where('source', 'referral_bonus')
            ->sum('amount');

        $completedBookingCount = WalletTransaction::where('user_id', $user->id)
            ->where('source', 'referral_bonus')
            ->count();

        // Map referral items for view
        $bookingReferralsList = $referralBookings->map(function ($booking) use ($configuredReward) {
            $referredUser = $booking->user;
            $name = trim(($referredUser?->first_name ?? '') . ' ' . ($referredUser?->last_name ?? ''));
            if (empty($name)) {
                $name = $booking->customer_name ?: 'Referred Customer';
            }

            $avatar = $referredUser?->photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=0866e8&color=fff";
            $joinedDate = $referredUser?->created_at ? $referredUser->created_at->format('Y-m-d') : $booking->created_at->format('Y-m-d');
            $statusRaw = strtolower((string) ($booking->status->value ?? $booking->status));
            $paymentStatusRaw = strtolower((string) ($booking->payment_status ?? 'pending'));

            $status = 'Pending';
            $rewardAmount = 0.00;

            if ($statusRaw === 'completed' && $paymentStatusRaw === 'paid') {
                $status = 'Rewarded';
                $rewardAmount = (float) (
                    $booking->discount_amount > 0 ? $booking->discount_amount : $configuredReward
                );
            }

            $email = $referredUser?->email ?: $booking->customer_email ?: 'N/A';

            return (object) [
                'id'              => $booking->id,
                'customer_name'   => $name,
                'customer_email'  => $email,
                'customer_avatar' => $avatar,
                'joined_date'     => $joinedDate,
                'status'          => $status,
                'booking_id'      => '#BK-' . $booking->id,
                'reward_amount'   => $rewardAmount,
            ];
        });

        // Merge invited or signed_up records from referrals table using dedicated method
        $referrals = $this->mergeReferralsTableData($user, $bookingReferralsList);

        $totalReferrals = $referrals->count();
        $pendingReferrals = $referrals->filter(function ($item) {
            $st = strtolower((string) $item->status);
            return in_array($st, ['pending', 'invited', 'signed_up','signed up']);
        })->count();

        $hasCompletedPaidBooking = Booking::where('user_id', $user->id)
            ->get()
            ->contains(function ($b) {
                $status = $b->status instanceof BookingStatus ? $b->status->value : strtolower((string) $b->status);
                $paymentStatus = strtolower((string) $b->payment_status);
                return $status === 'completed' && $paymentStatus === 'paid';
            });

        return [
            'referralCode'            => $referralCode,
            'referralLink'            => $referralLink,
            'totalReferrals'          => $totalReferrals,
            'pendingReferrals'        => $pendingReferrals,
            'completedBookingCount'   => $completedBookingCount,
            'totalRewards'            => $totalRewards,
            'referrals'               => $referrals,
            'hasCompletedPaidBooking' => $hasCompletedPaidBooking,
        ];
    }

    /**
     * Helper method to merge referrals table records (status: invited, signed_up) into customer referral history.
     */
    protected function mergeReferralsTableData(User $user, Collection $existingReferrals): Collection
    {
        $referralRecords = Referral::with(['referredUser'])
            ->where('referrer_user_id', $user->id)
            ->whereIn('status', ['invited', 'signed_up'])
            ->latest()
            ->get();

        if ($referralRecords->isEmpty()) {
            return $existingReferrals;
        }

        $existingEmails = $existingReferrals->pluck('customer_email')->filter()->map(fn($e) => strtolower(trim($e)))->toArray();

        $tableItems = $referralRecords->reject(function ($ref) use ($existingEmails) {
            $emailToCompare = strtolower(trim((string) ($ref->recipient_email ?: $ref->referredUser?->email)));
            return !empty($emailToCompare) && in_array($emailToCompare, $existingEmails);
        })->map(function ($ref) {
            $referredUser = $ref->referredUser;

            // Explicitly fetch recipient_email for invited and signed_up statuses
            $email = !empty($ref->recipient_email) ? trim($ref->recipient_email) : ($referredUser?->email ?: '');

            $name = $referredUser ? trim($referredUser->first_name . ' ' . ($referredUser->last_name ?? '')) : '';
            if (empty($name)) {
                $name = $email ?: 'Invited Friend';
            }

            $avatar = $referredUser?->photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=0866e8&color=fff";
            $joinedDate = $ref->created_at ? $ref->created_at->format('Y-m-d') : now()->format('Y-m-d');
            $statusRaw = strtolower(trim((string) $ref->status));
            $status = match ($statusRaw) {
                'signed_up', 'signed up' => 'Signed Up',
                default                  => 'Invited',
            };

            return (object) [
                'id'              => 'ref_' . $ref->id,
                'customer_name'   => $name,
                'customer_email'  => $email,
                'customer_avatar' => $avatar,
                'joined_date'     => $joinedDate,
                'status'          => $status,
                'booking_id'      => null, // Blank booking column for invited / signed_up
                'reward_amount'   => 0.00, // Amount is 0
            ];
        });

        return $existingReferrals->concat($tableItems);
    }
}
