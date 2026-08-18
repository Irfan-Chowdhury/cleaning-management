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

        return view('pages.weekly-schedule.index', compact('schedule'));
    }
}
