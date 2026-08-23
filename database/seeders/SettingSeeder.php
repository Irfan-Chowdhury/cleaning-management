<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // php artisan db:seed --class=SettingSeeder

        Setting::query()->updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Clean Manage Pro',
                // 'company_logo' => 'public/assets/images/company_logo/brand_logo.png',
                'welcome_credit' => 20.00,
                'referral_reward' => 25.00,
                'google_review_reward' => 15.00,
                'maximum_advance_booking_days' => 15,
                'cancellation_notice_hours' => 24,
            ]
        );
    }
}
