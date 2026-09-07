<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $setting = Setting::first();
        $companyName = $setting?->company_name ?: config('app.name', 'Dust2Glow');
        $userName = trim($notifiable->first_name . ' ' . $notifiable->last_name);

        return (new MailMessage)
            ->subject('Verify Your Email Address - ' . $companyName)
            ->greeting('Hello ' . ($userName ?: 'Valued Customer') . '!')
            ->line('Thank you for registering with ' . $companyName . '. Please click the button below to verify your email address and activate your account.')
            ->action('Verify Email Address', $verificationUrl)
            ->salutation(new \Illuminate\Support\HtmlString("Regards,<br>" . e($companyName)));
    }
}
