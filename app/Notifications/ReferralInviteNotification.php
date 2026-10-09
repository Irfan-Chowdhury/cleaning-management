<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ReferralInviteNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $sender,
        public readonly ?string $customMessage = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $setting = Setting::first();
        $companyName = $setting?->company_name ?: config('app.name', 'Dust2Glow');
        $rewardAmount = (float) ($setting?->referral_reward > 0 ? $setting->referral_reward : 25.00);
        $formattedReward = '$' . number_format($rewardAmount, 2);

        $senderName = trim($this->sender->first_name . ' ' . ($this->sender->last_name ?? ''));
        if (empty($senderName)) {
            $senderName = 'A friend';
        }

        $referralCode = $this->sender->referral_code ?: strtoupper(($this->sender->first_name ?: 'REF') . $this->sender->id);
        $registrationUrl = url('/register?ref=' . $referralCode);

        return (new MailMessage)
            ->subject($senderName . ' invited you to join ' . $companyName . '!')
            ->view('emails.referral_invite', [
                'senderName'      => $senderName,
                'companyName'     => $companyName,
                'rewardAmount'    => $rewardAmount,
                'formattedReward' => $formattedReward,
                'customMessage'   => $this->customMessage,
                'registrationUrl' => $registrationUrl,
                'referralCode'    => $referralCode,
            ]);
    }
}
