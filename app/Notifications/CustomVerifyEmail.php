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
        
        // Pass company logo if setting.company_logo is uploaded/set

        // $companyLogo = !empty($setting?->company_logo) ? $setting->company_logo_url : null;
        $companyLogo = null;
        if (!empty($setting?->company_logo)) {
            $rawLogo = $setting->company_logo;
            
            if (filter_var($rawLogo, FILTER_VALIDATE_URL)) {
                $companyLogo = $rawLogo;
            } else {
                $cleanPath = ltrim($rawLogo, '/');
                $possiblePaths = [
                    base_path($cleanPath),
                    public_path(str_replace('public/', '', $cleanPath)),
                    public_path($cleanPath),
                ];

                $filePath = null;
                foreach ($possiblePaths as $path) {
                    if (file_exists($path) && is_file($path)) {
                        $filePath = $path;
                        break;
                    }
                }

                if ($filePath) {
                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $mime = match ($ext) {
                        'png'  => 'image/png',
                        'jpg', 'jpeg' => 'image/jpeg',
                        'gif'  => 'image/gif',
                        'svg'  => 'image/svg+xml',
                        'webp' => 'image/webp',
                        default => 'image/' . $ext,
                    };
                    $fileData = file_get_contents($filePath);
                    $companyLogo = 'data:' . $mime . ';base64,' . base64_encode($fileData);
                } else {
                    $companyLogo = $setting->company_logo_url;
                }
            }
        }

        
        $userName = trim(($notifiable->first_name ?? '') . ' ' . ($notifiable->last_name ?? ''));
        $supportEmail = $setting?->email;
        $supportPhone = $setting?->phone;

        $welcomeCreditBonus = 0;
        if ($setting && $setting->welcome_credit_enabled && (float) $setting->welcome_credit > 0) {
            $welcomeCreditBonus = (float) $setting->welcome_credit;
        }
        $currencySymbol = $setting?->currency ?: '$';

        return (new MailMessage)
            ->subject('Verify Your Email Address - ' . $companyName)
            ->view('emails.verify-email', [
                'url'                => $verificationUrl,
                'userName'           => $userName,
                'companyName'        => $companyName,
                'companyLogo'        => $companyLogo,
                'supportEmail'       => $supportEmail,
                'supportPhone'       => $supportPhone,
                'welcomeCreditBonus' => $welcomeCreditBonus,
                'currencySymbol'     => $currencySymbol,
            ]);
    }
}
