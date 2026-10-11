<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;

class AdminDashboardService
{
    /**
     * Get metrics for Today Bookings card.
     *
     * @return array{today_count: int, approved_count: int}
     */
    public function getTodayBookingsMetrics(): array
    {
        $today = Carbon::today();

        $todayCount = Booking::whereDate('booking_date', $today)->count();

        $approvedCount = Booking::whereDate('booking_date', $today)
            ->where('status', BookingStatus::APPROVED)
            ->count();

        return [
            'today_count'    => $todayCount,
            'approved_count' => $approvedCount,
        ];
    }
}
