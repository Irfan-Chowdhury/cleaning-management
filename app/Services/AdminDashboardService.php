<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Referral;
use App\Models\User;
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

    /**
     * Get metrics for Active Customers card.
     *
     * @return array{total_count: int, new_this_month_count: int}
     */
    public function getActiveCustomersMetrics(): array
    {
        $now = Carbon::now();

        $totalCount = User::where('role', 2)
            ->where('is_active', true)
            ->count();

        $newThisMonthCount = User::where('role', 2)
            ->where('is_active', true)
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        return [
            'total_count'          => $totalCount,
            'new_this_month_count' => $newThisMonthCount,
        ];
    }

    /**
     * Get metrics for Total Revenue card.
     *
     * @return array{amount: float, completed_count: int, month_name: string}
     */
    public function getTotalRevenueMetrics(): array
    {
        $now = Carbon::now();

        $query = Booking::where('status', BookingStatus::COMPLETED)
            ->where(function ($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere('payment_status', 'Paid');
            })
            ->whereBetween('booking_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);

        $amount = (float) $query->sum('total_amount');
        $completedCount = $query->count();

        return [
            'amount'          => $amount,
            'completed_count' => $completedCount,
            'month_name'      => $now->format('F'),
        ];
    }
    
    public function getReferralMetrics(): array
    {
        $referralCounts = Referral::query()
            ->selectRaw("
                COUNT(CASE WHEN status IN ('signed_up', 'rewarded') THEN 1 END) AS signed_up_count,
                COUNT(CASE WHEN status = 'invited' THEN 1 END) AS invited_count
            ")
            ->first();

        return [
            'signed_up_count' => (int) $referralCounts->signed_up_count,
            'invited_count'   => (int) $referralCounts->invited_count,
        ];
    }
}
