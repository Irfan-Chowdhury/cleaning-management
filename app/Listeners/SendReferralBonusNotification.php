<?php

namespace App\Listeners;

use App\Events\BookingCompletedAndPaid;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\ReferralBonusEarnedNotification;
use Illuminate\Support\Facades\Log;

class SendReferralBonusNotification
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

        $transaction = WalletTransaction::where('source', 'referral_bonus')
            ->where('booking_id', $booking->id)
            ->first();

        $setting = Setting::first();
        $rewardAmount = $transaction ? (float) $transaction->amount : (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);

        $alreadyNotified = $referrer->notifications()
            ->where('data->booking_id', $booking->id)
            ->where('type', ReferralBonusEarnedNotification::class)
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        try {
            $referrer->notify(new ReferralBonusEarnedNotification($booking, $rewardAmount));
        } catch (\Throwable $e) {
            Log::error('Failed to send referral bonus notification: ' . $e->getMessage());
        }
    }
}
