<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralBonusEarnedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
        public readonly float $rewardAmount = 0.00
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $setting = Setting::first();
        $currencySymbol = $setting?->currency ?: '$';
        $formattedAmount = $currencySymbol . number_format($this->rewardAmount, 2);

        return [
            'type'       => 'referral_bonus_earned',
            'title'      => 'Referral Bonus Earned! 🎉',
            'message'    => "Congratulations! You earned a {$formattedAmount} referral bonus credit for completed Booking #BK-" . sprintf('%03d', $this->booking->id) . '.',
            'link'       => route('customer.referrals.index'),
            'icon'       => 'fas fa-gift',
            'booking_id' => $this->booking->id,
        ];
    }
}
