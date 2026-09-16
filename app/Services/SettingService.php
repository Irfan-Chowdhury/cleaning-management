<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SettingService
{
    public function latest(): ?Setting
    {
        return Setting::latest()->first();
    }

    public function timezoneOptions(): array
    {
        $timestamp = time();

        return collect(timezone_identifiers_list())
            ->map(function (string $zone) use ($timestamp): array {
                $timezone = new \DateTimeZone($zone);
                $offset = $timezone->getOffset((new \DateTimeImmutable())->setTimestamp($timestamp));
                $hours = intdiv(abs($offset), 3600);
                $minutes = intdiv(abs($offset) % 3600, 60);
                $sign = $offset >= 0 ? '+' : '-';

                return [
                    'zone' => $zone,
                    'diff_from_GMT' => sprintf('UTC/GMT %s%02d:%02d', $sign, $hours, $minutes),
                ];
            })
            ->all();
    }

    public function update(array $data): Setting
    {
        $setting = $this->latest() ?? new Setting();

        if (isset($data['company_logo']) && $data['company_logo'] instanceof UploadedFile) {
            $data['company_logo'] = $this->uploadLogo($data['company_logo'], $setting->company_logo);
        }

        $setting->fill(Arr::only($data, [
            'company_name',
            'company_logo',
            'phone',
            'email',
            'address',
            'timezone',
            'currency',
            'minimum_booking_amount',
            'max_wallet_usage',
            'maximum_advance_booking_days',
            'cancellation_notice_hours',
            'welcome_credit',
            'welcome_credit_enabled',
            'referral_reward',
            'referral_reward_enabled',
            'google_review_reward',
            'google_review_enabled',
            'promotion_max_uses',
            'promotion_max_uses_per_customer',
        ]));

        $setting->save();

        return $setting->refresh();
    }

    private function uploadLogo(UploadedFile $file, ?string $oldLogo): string
    {
        $destination = public_path('assets/images/company_logo');

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = 'company-logo-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        $this->deleteOldLogo($oldLogo);

        return 'public/assets/images/company_logo/' . $filename;
    }

    private function deleteOldLogo(?string $oldLogo): void
    {
        if (empty($oldLogo) || $oldLogo === 'public/assets/images/company_logo/brand_logo.png' || filter_var($oldLogo, FILTER_VALIDATE_URL)) {
            return;
        }

        $path = public_path(Str::after($oldLogo, 'public/'));

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
