<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\ReferralInviteNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        // if (empty($referralCode)) {
        //     $referralCode = strtoupper(($user->first_name ?: 'REF') . $user->id);
        //     $user->update(['referral_code' => $referralCode]);
        // }

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

        // if ($totalRewards == 0) {
        //     $completedCount = $referralBookings->filter(function ($b) {
        //         $status = strtolower((string) ($b->status->value ?? $b->status));
        //         return in_array($status, ['completed', 'rewarded', 'paid']);
        //     })->count();
        //     $totalRewards = $completedCount * $configuredReward;
        // }

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
            'completedBookingCount' => $completedBookingCount,
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

    /**
     * Refer to docs/DeveloperNote.md (Line 3: "Customer Dashboard: Invite Via Email Feature")
     * for full technical specifications, rules, and developer documentation.
     */
    public function sendEmailInvitation(User $sender, string $recipientEmail, ?string $customMessage = null): array
    {
        // 1. Verify customer has at least 1 completed paid booking
        $hasCompletedPaidBooking = Booking::where('user_id', $sender->id)
            ->where('status', BookingStatus::COMPLETED->value)
            ->where('payment_status', 'paid')
            ->exists();

        if (!$hasCompletedPaidBooking) {
            return [
                'success' => false,
                'message' => 'Your referral link and invitation features are locked until you successfully complete at least one booking with payment done.',
            ];
        }

        // 2. Check if email already exists in users table
        if (User::where('email', trim($recipientEmail))->exists()) {
            return [
                'success' => false,
                'message' => 'This email address is already registered as an existing account.',
            ];
        }

        // 3. Save invitation record in referrals table
        try {
            $senderCode = $sender->referral_code ?: strtoupper(($sender->first_name ?: 'REF') . $sender->id);
            Referral::updateOrCreate(
                [
                    'referrer_user_id' => $sender->id,
                    'recipient_email'  => strtolower(trim($recipientEmail)),
                ],
                [
                    'referral_code'   => $senderCode,
                    'custom_message'  => $customMessage,
                    'status'          => 'invited',
                ]
            );
        } catch (Throwable $e) {
            Log::error('Failed to save referral invitation record: ' . $e->getMessage());
        }

        // 4. Dispatch professional referral email notification to the recipient
        try {
            Notification::route('mail', trim($recipientEmail))
                ->notify(new ReferralInviteNotification($sender, $customMessage));

            return [
                'success' => true,
                'message' => 'Referral invitation sent successfully to ' . trim($recipientEmail) . '!',
            ];
        } catch (Throwable $e) {
            Log::error('Failed to send referral email invitation: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Could not send invitation email at this time. Please try again later.',
            ];
        }
    }

    /**
     * Get dynamic dashboard data specifically for 'Your Referrals' feature from referrals table.
     *
     * @param User $user Logged in customer user
     * @return object
     */
    public function getYourReferralsDashboardData(User $user): object
    {
        $setting = Setting::first();
        $configuredReward = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

        $referralRecords = Referral::with(['referredUser', 'booking'])
            ->where('referrer_user_id', $user->id)
            ->latest()
            ->get();

        $totalInvited = $referralRecords->count();
        // $successfulReferrals = $referralRecords->whereIn('status', ['signed_up', 'rewarded'])->count();

        $completedBookingCount = WalletTransaction::where('user_id', $user->id)
            ->where('source', 'referral_bonus')
            ->count();
        
        $totalRewards = (float) WalletTransaction::where('user_id', $user->id)
            ->where('source', 'referral_bonus')
            ->sum('amount');

        if ($totalRewards == 0 && $referralRecords->where('status', 'rewarded')->count() > 0) {
            $totalRewards = (float) $referralRecords->where('status', 'rewarded')->sum('reward_amount');
        }

        $recentReferrals = $referralRecords->take(5)->map(function ($ref) use ($configuredReward) {
            $referredUser = $ref->referredUser;
            $name = $referredUser ? trim($referredUser->first_name . ' ' . ($referredUser->last_name ?? '')) : '';
            if (empty($name)) {
                $name = $ref->recipient_email ?: 'Invited Friend';
            }

            $emailDisplay = $ref->recipient_email ?: ($referredUser?->email ?: $name);
            $rawStatus = strtolower(trim((string) $ref->status));

            $statusLabel = match ($rawStatus) {
                'rewarded'               => 'Rewarded',
                'signed_up', 'signed up' => 'Signed Up',
                'cancelled'              => 'Cancelled',
                default                  => 'Invited',
            };

            $rewardAmt = (float) ($ref->reward_amount > 0 ? $ref->reward_amount : ($rawStatus === 'rewarded' ? $configuredReward : 0.00));

            return (object) [
                'id'             => $ref->id,
                'customer_name'  => $name,
                'customer_email' => $emailDisplay,
                'status'         => $statusLabel,
                'status_raw'     => $rawStatus,
                'reward_amount'  => $rewardAmt,
                'created_at'     => $ref->created_at ? $ref->created_at->format('M d, Y') : now()->format('M d, Y'),
            ];
        });

        return (object) [
            'total_invited'           => $totalInvited,
            // 'successful_referrals'    => $successfulReferrals,
            'total_rewards'           => $totalRewards,
            'total_rewards_formatted' => '$' . number_format($totalRewards, 2),
            'recent_referrals'        => $recentReferrals,
            'completedBookingCount' => $completedBookingCount
        ];
    }
}
