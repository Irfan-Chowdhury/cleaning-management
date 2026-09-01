<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateWeeklyScheduleRequest;
use App\Services\WeeklyScheduleService;
use Illuminate\Http\JsonResponse;

class WeeklyScheduleController extends Controller
{
    public function __construct(private readonly WeeklyScheduleService $weeklyScheduleService)
    {
    }

    /**
     * Display a listing of the weekly schedule.
     */
    public function index()
    {
        $schedule = $this->weeklyScheduleService->allWithSlotCounts();

        return view('pages.admin.weekly-schedule.index', compact('schedule'));
    }

    /**
     * Show the edit form for a specific day's schedule.
     */
    public function edit(string $day)
    {
        if (! $this->weeklyScheduleService->isValidDay($day)) {
            abort(404);
        }

        $weeklySchedule = $this->weeklyScheduleService->findByDay($day);
        $day = $weeklySchedule->day_of_week;
        $slots = $weeklySchedule->slots;
        $isActive = $weeklySchedule->is_active;

        return view('pages.admin.weekly-schedule.edit', compact('weeklySchedule', 'day', 'slots', 'isActive'));
    }

    /**
     * Update a specific day's schedule.
     */
    public function update(UpdateWeeklyScheduleRequest $request, string $day): JsonResponse
    {
        if (! $this->weeklyScheduleService->isValidDay($day)) {
            abort(404);
        }

        $weeklySchedule = $this->weeklyScheduleService->findByDay($day);
        $this->weeklyScheduleService->update($weeklySchedule, $request->validated());

        return response()->json([
            'message' => $weeklySchedule->day_of_week . ' schedule updated successfully!',
        ]);
    }
}
