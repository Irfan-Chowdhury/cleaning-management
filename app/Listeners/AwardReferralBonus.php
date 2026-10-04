<?php

namespace App\Listeners;

use App\Events\BookingCompletedAndPaid;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

class AwardReferralBonus
{
    public function handle(BookingCompletedAndPaid $event): void
    {
        $booking = $event->booking;

        if (empty($booking->referal_code)) {
            return;
        }

        $referrer = User::where('referral_code', strtoupper($booking->referal_code))->first();
        if (!$referrer || $referrer->id === $booking->user_id) {
            return;
        }

        $alreadyRewarded = WalletTransaction::where('source', 'referral_bonus')
            ->where('booking_id', $booking->id)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        try {
            $setting = Setting::first();
            $rewardAmount = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

            WalletTransaction::create([
                'user_id'     => $referrer->id,
                'booking_id'  => $booking->id,
                'type'        => 'credit',
                'amount'      => $rewardAmount,
                'source'      => 'referral_bonus',
                'description' => 'Referral bonus reward for completed Booking #BK-' . sprintf('%03d', $booking->id),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to award referral bonus: ' . $e->getMessage());
        }
    }
}
