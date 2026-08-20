<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeeklyScheduleController extends Controller
{
    /**
     * Display a listing of the weekly schedule.
     */
    public function index()
    {
        $schedule = collect([
            ['day' => 'Monday', 'total_slots' => 10, 'status' => 'active'],
            ['day' => 'Tuesday', 'total_slots' => 8, 'status' => 'active'],
            ['day' => 'Wednesday', 'total_slots' => 0, 'status' => 'inactive'],
            ['day' => 'Thursday', 'total_slots' => 10, 'status' => 'active'],
            ['day' => 'Friday', 'total_slots' => 10, 'status' => 'active'],
            ['day' => 'Saturday', 'total_slots' => 6, 'status' => 'active'],
            ['day' => 'Sunday', 'total_slots' => 0, 'status' => 'inactive'],
        ]);

        return view('pages.admin.weekly-schedule.index', compact('schedule'));
    }

    /**
     * Show the edit form for a specific day's schedule.
     */
    public function edit(string $day)
    {
        $day = ucfirst(strtolower($day));

        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        if (!in_array($day, $validDays)) {
            abort(404);
        }

        // Static slot data per day (UI prototype only)
        $staticSlots = [
            'Monday'    => ['07:00 AM', '09:00 AM', '02:00 PM'],
            'Tuesday'   => ['08:00 AM', '10:00 AM', '03:00 PM'],
            'Wednesday' => [],
            'Thursday'  => ['07:00 AM', '11:00 AM', '01:00 PM'],
            'Friday'    => ['08:00 AM', '10:00 AM', '04:00 PM'],
            'Saturday'  => ['09:00 AM', '12:00 PM'],
            'Sunday'    => [],
        ];

        $staticStatus = [
            'Monday' => true, 'Tuesday' => true, 'Wednesday' => false,
            'Thursday' => true, 'Friday' => true, 'Saturday' => true, 'Sunday' => false,
        ];

        $slots    = $staticSlots[$day] ?? [];
        $isActive = $staticStatus[$day] ?? false;

        return view('pages.admin.weekly-schedule.edit', compact('day', 'slots', 'isActive'));
    }
}