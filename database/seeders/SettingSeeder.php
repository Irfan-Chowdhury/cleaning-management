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
                'company_name' => 'Dust2Glow Cleaning Services',
                'company_logo' => 'assets/images/company_logo/company-logo-20260908180343-RtCkTnHr.webp',
                'phone' => '+1 555 014 8821',
                'email' => 'support@cleanmanagepro.test',
                'address' => '240 Spring Street, New York, NY 10013',
                'timezone' => 'Asia/Dhaka',
                'currency' => 'USD',
                'minimum_booking_amount' => 100.00,
                'max_wallet_usage' => 50.00,
                'minimum_advance_booking_days' => 3,
                'maximum_advance_booking_days' => 30,
                'cancellation_notice_hours' => 48,
                'welcome_credit' => 20.00,
                'welcome_credit_enabled' => true,
                'referral_reward' => 25.00,
                'referral_reward_enabled' => true,
                'google_review_reward' => 15.00,
                'google_review_enabled' => true,
                // 'email_configuration_enabled' => true,
                'promotion_max_uses' => 500,
                'promotion_max_uses_per_customer' => 1,
            ]
        );
    }
}
