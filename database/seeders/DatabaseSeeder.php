<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            ServiceSeeder::class,
            ServiceQuestionSeeder::class,
            HolidaySeeder::class,
            WeeklyScheduleSeeder::class,
            PromotionSeeder::class,
            UserSeeder::class,
            // NotificationSeeder::class,
            QuotationSeeder::class,
        ]);
    }
}
