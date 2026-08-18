<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Static dummy holidays for UI prototype.
     */
    private function mockHolidays(): array
    {
        return [
            ['id' => 1, 'title' => 'New Year\'s Day',     'start_date' => '2025-01-01', 'end_date' => '2025-01-01'],
            ['id' => 2, 'title' => 'Easter Break',        'start_date' => '2025-04-18', 'end_date' => '2025-04-21'],
            ['id' => 3, 'title' => 'Labour Day',          'start_date' => '2025-05-05', 'end_date' => '2025-05-05'],
            ['id' => 4, 'title' => 'Queen\'s Birthday',   'start_date' => '2025-06-09', 'end_date' => '2025-06-09'],
            ['id' => 5, 'title' => 'Christmas Holiday',   'start_date' => '2025-12-25', 'end_date' => '2025-12-26'],
        ];
    }

    /**
     * Display the holiday list.
     */
    public function index()
    {
        $holidays = collect($this->mockHolidays());
        return view('pages.holidays.index', compact('holidays'));
    }
}
