<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Seed demo promotional offers.
     */
    public function run(): void
    {
        $adminId = User::query()->where('role', 1)->value('id');

        $promotions = [
            [
                'name' => 'Welcome Cleaning Credit',
                'code' => 'WELCOME25',
                'description' => 'Introductory discount for first-time cleaning service customers.',
                'discount_type' => 'fixed',
                'discount_value' => 25.00,
                'status' => 'active',
                'start_at' => now()->subDays(5),
                'expires_at' => now()->addDays(45),
                'new_customers_only' => true,
                'existing_customers_only' => false,
            ],
            [
                'name' => 'Spring Deep Clean',
                'code' => 'SPRING15',
                'description' => 'Seasonal percentage discount for deep cleaning bookings.',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'status' => 'active',
                'start_at' => now()->subDays(2),
                'expires_at' => now()->addDays(30),
                'new_customers_only' => false,
                'existing_customers_only' => false,
            ],
            [
                'name' => 'Loyal Customer Reward',
                'code' => 'LOYAL10',
                'description' => 'Special offer for returning customers.',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'status' => 'active',
                'start_at' => now()->subDay(),
                'expires_at' => now()->addDays(60),
                'new_customers_only' => false,
                'existing_customers_only' => true,
            ],
            [
                'name' => 'Paused Move Out Offer',
                'code' => 'MOVEOUT50',
                'description' => 'Paused fixed discount prepared for move-out cleaning campaigns.',
                'discount_type' => 'fixed',
                'discount_value' => 50.00,
                'status' => 'paused',
                'start_at' => now()->addDays(7),
                'expires_at' => now()->addDays(90),
                'new_customers_only' => false,
                'existing_customers_only' => false,
            ],
            [
                'name' => 'Expired Holiday Refresh',
                'code' => 'HOLIDAY20',
                'description' => 'Previous holiday campaign retained for reporting history.',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'status' => 'expired',
                'start_at' => now()->subDays(90),
                'expires_at' => now()->subDays(10),
                'new_customers_only' => false,
                'existing_customers_only' => false,
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::updateOrCreate(
                ['code' => $promotion['code']],
                $promotion + ['created_by' => $adminId]
            );
        }
    }
}
