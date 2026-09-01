<?php

namespace App\Services;

use App\Models\WeeklySchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WeeklyScheduleService
{
    public const DAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function allWithSlotCounts(): Collection
    {
        return WeeklySchedule::query()
            ->withCount('slots')
            ->orderByRaw("
                CASE day_of_week
                    WHEN 'Monday' THEN 1
                    WHEN 'Tuesday' THEN 2
                    WHEN 'Wednesday' THEN 3
                    WHEN 'Thursday' THEN 4
                    WHEN 'Friday' THEN 5
                    WHEN 'Saturday' THEN 6
                    WHEN 'Sunday' THEN 7
                    ELSE 8
                END
            ")
            ->get();
    }

    public function findByDay(string $day): WeeklySchedule
    {
        return WeeklySchedule::query()
            ->with('slots')
            ->where('day_of_week', $this->normalizeDay($day))
            ->firstOrFail();
    }

    public function isValidDay(string $day): bool
    {
        return in_array($this->normalizeDay($day), self::DAYS, true);
    }

    public function normalizeDay(string $day): string
    {
        return ucfirst(strtolower($day));
    }

    public function update(WeeklySchedule $weeklySchedule, array $data): WeeklySchedule
    {
        return DB::transaction(function () use ($weeklySchedule, $data) {
            $weeklySchedule->update([
                'is_active' => (bool) ($data['is_active'] ?? false),
            ]);

            $weeklySchedule->slots()->delete();

            foreach (array_values($data['slots'] ?? []) as $index => $slot) {
                $weeklySchedule->slots()->create([
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'] ?? null,
                    'sort_order' => $index + 1,
                ]);
            }

            return $weeklySchedule->fresh('slots');
        });
    }
}
