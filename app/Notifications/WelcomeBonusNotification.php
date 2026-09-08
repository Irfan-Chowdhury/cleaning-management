<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeBonusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly float $bonusAmount = 0.00,
        public readonly string $currency = '$'
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $setting = Setting::first();
        $companyName = $setting?->company_name ?: config('app.name', 'Dust2Glow');
        $currencySymbol = $setting?->currency ?: $this->currency;
        $userName = trim($notifiable->first_name . ' ' . $notifiable->last_name);

        $mail = (new MailMessage)
            ->subject('Welcome to ' . $companyName . '! Account Verified 🎉')
            ->greeting('Welcome ' . ($userName ?: 'Valued Customer') . '!')
            ->line('Your email address has been successfully verified, and your account is now fully active.');

        if ($this->bonusAmount > 0) {
            $formattedAmount = $currencySymbol . number_format($this->bonusAmount, 2);
            $mail->line('🎉 **Congratulations!** As a special welcome reward, we have credited **' . $formattedAmount . '** to your wallet!')
                 ->line('You can apply your wallet balance towards your upcoming cleaning service bookings.');
        } else {
            $mail->line('You can now log in, explore our professional cleaning services, and manage your bookings effortlessly.');
        }

        return $mail
            ->action('Go to My Dashboard', url('/dashboard'))
            ->salutation(new \Illuminate\Support\HtmlString("Regards,<br>" . e($companyName)));
    }

    public function toDatabase($notifiable): array
    {
        $setting = Setting::first();
        $currencySymbol = $setting?->currency ?: $this->currency;

        if ($this->bonusAmount > 0) {
            $formattedAmount = $currencySymbol . number_format($this->bonusAmount, 2);
            $title = 'Welcome Bonus Credited! 🎉';
            $message = "Congratulations! A welcome bonus credit of {$formattedAmount} has been added to your wallet.";
            $link = route('customer.wallet.index');
            $icon = 'fas fa-wallet';
        } else {
            $title = 'Welcome to Dust2Glow! 🎉';
            $message = 'Your email address has been verified and your account is fully active.';
            $link = route('dashboard');
            $icon = 'fas fa-check-circle';
        }

        return [
            'type'    => 'welcome_bonus',
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'icon'    => $icon,
        ];
    }
}
