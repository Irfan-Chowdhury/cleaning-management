<?php

namespace Database\Seeders;

use App\Models\WeeklySchedule;
use App\Services\WeeklyScheduleService;
use Illuminate\Database\Seeder;

class WeeklyScheduleSeeder extends Seeder
{
    /**
     * // php artisan db:seed --class=WeeklyScheduleSeeder
     * Seed weekly schedule with five slots for every weekday.
     */
    public function run(): void
    {
        WeeklySchedule::query()->delete();

        $slots = [
            ['start_time' => '08:00', 'end_time' => '09:00'],
            ['start_time' => '10:00', 'end_time' => '11:00'],
            ['start_time' => '12:00', 'end_time' => '13:00'],
            ['start_time' => '14:00', 'end_time' => '15:00'],
            ['start_time' => '16:00', 'end_time' => '17:00'],
        ];

        foreach (WeeklyScheduleService::DAYS as $day) {
            $schedule = WeeklySchedule::updateOrCreate(
                ['day_of_week' => $day],
                ['is_active' => true]
            );

            foreach ($slots as $index => $slot) {
                $schedule->slots()->updateOrCreate(
                    ['start_time' => $slot['start_time']],
                    [
                        'end_time' => $slot['end_time'],
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
