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
                'phone' => '+1 555 014 8821',
                'email' => 'support@cleanmanagepro.test',
                'address' => '240 Spring Street, New York, NY 10013',
                'timezone' => 'America/New_York',
                'currency' => 'USD',
                'minimum_booking_amount' => 50.00,
                'maximum_booking_amount' => 1500.00,
                'maximum_advance_booking_days' => 30,
                'cancellation_notice_hours' => 24,
                'welcome_credit' => 20.00,
                'welcome_credit_enabled' => true,
                'referral_reward' => 25.00,
                'referral_reward_enabled' => true,
                'google_review_reward' => 15.00,
                'google_review_enabled' => true,
                'promotion_max_uses' => 500,
                'promotion_max_uses_per_customer' => 1,
            ]
        );
    }
}
