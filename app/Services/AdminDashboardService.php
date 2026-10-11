<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Referral;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\WeeklySchedule;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
            'signed_up_count' => (int) ($referralCounts->signed_up_count ?? 0),
            'invited_count'   => (int) ($referralCounts->invited_count ?? 0),
        ];
    }

    /**
     * Fetch the latest bookings for the booking queue.
     *
     * @param int $limit
     * @return array
     */
    public function getBookingQueue(int $limit = 10): array
    {
        $bookings = Booking::with([
            'service:id,name',
            'user:id,first_name,last_name,email',
        ])
            ->latest('id')
            ->limit($limit)
            ->get();

        return $bookings->map(function (Booking $booking) {
            $statusVal = $booking->status instanceof BookingStatus
                ? $booking->status->value
                : (string) $booking->status;

            $statusLabel = $booking->status instanceof BookingStatus
                ? $booking->status->label()
                : ucfirst($statusVal);

            $statusBadgeClass = match (strtolower($statusVal)) {
                'pending'                            => 'status-pending',
                'confirmed', 'approved', 'completed' => 'status-confirmed',
                'processing'                         => 'status-progress',
                'cancelled'                          => 'status-cancelled',
                default                              => 'status-pending',
            };

            $payment = strtolower((string) $booking->payment_status);
            $paymentBadgeClass = match ($payment) {
                'paid'               => 'payment-paid',
                'refund', 'refunded' => 'payment-refund',
                default              => 'payment-due',
            };

            $scheduleDate = Carbon::parse($booking->booking_date);
            $timeStr = $booking->start_time ? Carbon::parse($booking->start_time)->format('h:i A') : '';
            if ($scheduleDate->isToday()) {
                $schedule = 'Today' . ($timeStr ? ', ' . $timeStr : '');
            } elseif ($scheduleDate->isTomorrow()) {
                $schedule = 'Tomorrow' . ($timeStr ? ', ' . $timeStr : '');
            } else {
                $schedule = $scheduleDate->format('M d') . ($timeStr ? ', ' . $timeStr : '');
            }

            $customerName = $booking->customer_name;
            if (empty($customerName) && $booking->user) {
                $customerName = trim($booking->user->first_name . ' ' . $booking->user->last_name);
            }

            return [
                'id'                  => $booking->id,
                'reference'           => sprintf('#BK-%04d', $booking->id),
                'customer_name'       => $customerName ?: 'Customer',
                'service_name'        => $booking->service?->name ?? 'Cleaning Service',
                'schedule'            => $schedule,
                'status_label'        => $statusLabel,
                'status_badge_class'  => $statusBadgeClass,
                'payment_label'       => ucfirst($payment ?: 'due'),
                'payment_badge_class' => $paymentBadgeClass,
            ];
        })->all();
    }

    /**
     * Fetch active services and their completed & paid bookings count.
     *
     * @return array
     */
    public function getServiceMix(): array
    {
        $services = Service::where('status', 'active')
            ->withCount(['bookings' => function ($query) {
                $query->where('status', BookingStatus::COMPLETED)
                    ->where(function ($q) {
                        $q->where('payment_status', 'paid')
                          ->orWhere('payment_status', 'Paid');
                    });
            }])
            ->orderByDesc('bookings_count')
            ->get();

        $totalCompleted = $services->sum('bookings_count');

        return $services->map(function (Service $service) use ($totalCompleted) {
            $count = (int) $service->bookings_count;
            $percentage = $totalCompleted > 0 ? (int) round(($count / $totalCompleted) * 100) : 0;

            // Give a subtle minimum width for items with count > 0 for visual bar presentation
            $barWidth = ($count > 0 && $percentage < 5) ? 5 : $percentage;

            return [
                'id'         => $service->id,
                'name'       => $service->name,
                'count'      => $count,
                'label'      => number_format($count) . ' completed ' . Str::plural('booking', $count),
                'percentage' => $barWidth,
            ];
        })->all();
    }

    /**
     * Calculate open slots for the current week using weekly_schedule, schedule_slots, and holidays.
     *
     * @return int
     */
    public function getOpenSlotsThisWeek(): int
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday
        $endOfWeek   = Carbon::now()->endOfWeek();   // Sunday

        // Fetch active holidays that overlap with the current week
        $holidays = Holiday::where('is_active', true)
            ->where('start_date', '<=', $endOfWeek->format('Y-m-d'))
            ->where('end_date', '>=', $startOfWeek->format('Y-m-d'))
            ->get(['start_date', 'end_date']);

        // Fetch active weekly schedule days with their schedule_slots count
        $weeklySchedules = WeeklySchedule::where('is_active', true)
            ->withCount('slots')
            ->get()
            ->keyBy(fn ($item) => strtolower($item->day_of_week));

        $openSlotsCount = 0;

        // Loop through Monday to Sunday of the current week
        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayName = strtolower($date->format('l'));

            // Check if this date falls within any active holiday
            $isHoliday = $holidays->contains(function ($holiday) use ($dateStr) {
                $start = Carbon::parse($holiday->start_date)->format('Y-m-d');
                $end   = Carbon::parse($holiday->end_date)->format('Y-m-d');
                return $dateStr >= $start && $dateStr <= $end;
            });

            if ($isHoliday) {
                continue;
            }

            // Add slots count if the day of week is active
            $schedule = $weeklySchedules->get($dayName);
            if ($schedule) {
                $openSlotsCount += (int) $schedule->slots_count;
            }
        }

        return $openSlotsCount;
    }

    /**
     * Calculate upcoming holidays for the current month.
     *
     * @return int
     */
    // public function getUpcomingHolidaysCount(): int
    // {
    //     $today      = Carbon::today();
    //     $endOfMonth = Carbon::now()->endOfMonth();

    //     return Holiday::where('is_active', true)
    //         ->whereDate('end_date', '>=', $today)
    //         ->whereDate('start_date', '<=', $endOfMonth)
    //         ->count();
    // }

    public function getTotalHolidaysCurrentMonthCount(): int
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $holidays = Holiday::where('is_active', true)
            ->whereDate('end_date', '>=', $startOfMonth)
            ->whereDate('start_date', '<=', $endOfMonth)
            ->get(['start_date', 'end_date']);

        $holidayDates = [];

        foreach ($holidays as $holiday) {
            $startDate = Carbon::parse($holiday->start_date)->max($startOfMonth);
            $endDate   = Carbon::parse($holiday->end_date)->min($endOfMonth);

            for (
                $date = $startDate->copy();
                $date->lte($endDate);
                $date->addDay()
            ) {
                $holidayDates[$date->toDateString()] = true;
            }
        }

        return count($holidayDates);
    }

    /**
     * Sum the amount where wallet_transactions.type = credit on the current month.
     *
     * @return float
     */
    public function getTotalCreditsIssuedCurrentMonth(): float
    {
        $now = Carbon::now();

        return (float) WalletTransaction::where('type', 'credit')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');
    }

    /**
     * Sum welcome bonus credits issued on the current month.
     *
     * @return float
     */
    public function getWelcomeBonusThisMonth(): float
    {
        $now = Carbon::now();

        return (float) WalletTransaction::where('type', 'credit')
            ->where('source', 'welcome_bonus')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');
    }

    /**
     * Sum referral bonus credits issued on the current month.
     *
     * @return float
     */
    public function getReferralBonusThisMonth(): float
    {
        $now = Carbon::now();

        return (float) WalletTransaction::where('type', 'credit')
            ->where('source', 'referral_bonus')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');
    }

    /**
     * Sum Google review reward credits issued on the current month.
     *
     * @return float
     */
    public function getGoogleRewardsThisMonth(): float
    {
        $now = Carbon::now();

        return (float) WalletTransaction::where('type', 'credit')
            ->where('source', 'review_bonus')
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');
    }

    /**
     * Fetch recent operational activities using the AuditLog table (Option A).
     *
     * @param int $limit
     * @return array
     */
    public function getRecentActivities(int $limit = 4): array
    {
        $logs = AuditLog::with(['user', 'auditable'])
            ->latest('id')
            ->limit($limit)
            ->get();

        if ($logs->isEmpty()) {
            return $this->getFallbackRecentActivities($limit);
        }

        return $logs->map(function (AuditLog $log) {
            $module = class_basename($log->auditable_type);
            $action = strtolower($log->action);
            $timeAgo = $log->created_at ? $log->created_at->diffForHumans() : 'Just now';
            $userName = $log->user ? trim($log->user->first_name . ' ' . $log->user->last_name) : 'System';

            $title = "Activity recorded";
            $description = "{$userName} performed an action";
            $dotClass = 'dot-blue';

            if ($module === 'Booking') {
                $bookingId = $log->auditable_id;
                $newValues = $log->new_values ?? [];
                $customer = $newValues['customer_name'] ?? ($log->auditable?->customer_name ?? $userName);

                if ($action === 'created') {
                    $title = sprintf('Booking #BK-%04d submitted', $bookingId);
                    $service = $log->auditable?->service?->name ?? 'Cleaning Service';
                    $description = "{$customer} requested {$service}";
                    $dotClass = 'dot-blue';
                } elseif ($action === 'updated') {
                    $status = $newValues['status'] ?? ($log->auditable?->status instanceof BookingStatus ? $log->auditable->status->value : null);
                    if ($status === 'cancelled') {
                        $title = sprintf('Booking #BK-%04d cancelled', $bookingId);
                        $description = "Booking was cancelled for {$customer}";
                        $dotClass = 'dot-rose';
                    } elseif ($status === 'completed') {
                        $title = sprintf('Booking #BK-%04d completed', $bookingId);
                        $description = "Cleaning completed successfully";
                        $dotClass = 'dot-green';
                    } else {
                        $title = sprintf('Booking #BK-%04d updated', $bookingId);
                        $description = "Status updated to " . ucfirst((string) ($status ?? 'in progress'));
                        $dotClass = 'dot-amber';
                    }
                } else {
                    $title = sprintf('Booking #BK-%04d %s', $bookingId, $action);
                    $description = "Action by {$userName}";
                    $dotClass = 'dot-rose';
                }
            } elseif ($module === 'WalletTransaction') {
                $newValues = $log->new_values ?? [];
                $amount = (float) ($newValues['amount'] ?? ($log->auditable?->amount ?? 0));
                $type = $newValues['type'] ?? ($log->auditable?->type ?? 'credit');
                $source = $newValues['source'] ?? ($log->auditable?->source ?? '');
                $sourceLabel = ucwords(str_replace('_', ' ', (string) $source));

                if ($type === 'credit') {
                    $title = 'Wallet credit added';
                    $description = '$' . number_format($amount, 2) . ' credit added' . ($sourceLabel ? " ({$sourceLabel})" : '');
                    $dotClass = 'dot-green';
                } else {
                    $title = 'Wallet credit used';
                    $description = '$' . number_format($amount, 2) . ' credit debited';
                    $dotClass = 'dot-amber';
                }
            } elseif ($module === 'Referral') {
                $status = $log->new_values['status'] ?? ($log->auditable?->status ?? '');
                if ($status === 'rewarded') {
                    $title = 'Referral reward completed';
                    $description = 'Referral reward queued and paid';
                    $dotClass = 'dot-green';
                } else {
                    $title = 'Referral waiting for approval';
                    $description = 'Referral reward queued after registration';
                    $dotClass = 'dot-amber';
                }
            } elseif ($module === 'User') {
                $role = (int) ($log->new_values['role'] ?? ($log->auditable?->role ?? 2));
                if ($action === 'created') {
                    $title = $role === 2 ? 'New customer registered' : 'User account created';
                    $description = "{$userName} registered in the system";
                    $dotClass = 'dot-green';
                } else {
                    $title = 'User profile updated';
                    $description = "Details modified for {$userName}";
                    $dotClass = 'dot-amber';
                }
            } else {
                $title = "{$module} {$action}";
                $description = "Updated by {$userName}";
                $dotClass = match ($action) {
                    'created' => 'dot-blue',
                    'deleted' => 'dot-rose',
                    default   => 'dot-amber',
                };
            }

            return [
                'title'            => $title,
                'description'      => $description,
                'dot_class'        => $dotClass,
                'created_at_human' => $timeAgo,
            ];
        })->all();
    }

    /**
     * Fallback recent activities when audit_logs table is empty.
     *
     * @param int $limit
     * @return array
     */
    protected function getFallbackRecentActivities(int $limit = 4): array
    {
        $bookings = Booking::with(['service', 'user'])
            ->latest('id')
            ->limit($limit)
            ->get();

        return $bookings->map(function (Booking $booking) {
            $customerName = $booking->customer_name ?: ($booking->user ? trim($booking->user->first_name . ' ' . $booking->user->last_name) : 'Customer');
            $serviceName = $booking->service?->name ?? 'Cleaning Service';
            $timeAgo = $booking->created_at ? $booking->created_at->diffForHumans() : 'Recently';

            return [
                'title'            => sprintf('Booking #BK-%04d submitted', $booking->id),
                'description'      => "{$customerName} requested {$serviceName}",
                'dot_class'        => 'dot-blue',
                'created_at_human' => $timeAgo,
            ];
        })->all();
    }
}
