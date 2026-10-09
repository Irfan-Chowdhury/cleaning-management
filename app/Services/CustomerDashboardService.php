<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class CustomerDashboardService
{
    /**
     * Fetch and prepare the next upcoming cleaning booking for the logged-in customer's dashboard.
     *
     * Purpose:
     * - Queries the nearest upcoming active booking (pending, approved, confirmed, or processing).
     * - Compares both booking_date and start_time against current date/time to skip passed sessions today.
     * - Pre-formats all presentation properties (dates, time slots, address, images, badge classes, URLs)
     *   on the returned model object so the Blade template remains pure and free of business logic.
     *
     * @param int $userId ID of the logged-in customer
     * @return Booking|null Formatted booking object or null if no upcoming cleaning exists
     */
    public function getNextCleaning(int $userId): ?Booking
    {
        $now = Carbon::now();
        $todayDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // Active non-finalized booking statuses
        $activeStatuses = [
            BookingStatus::PENDING,
            BookingStatus::APPROVED,
            BookingStatus::CONFIRMED,
            BookingStatus::PROCESSING,
        ];

        // -------------------------------------------------------------------------
        // 1. Database Query Block:
        // - Eager loads 'service' and 'images' relationships to prevent N+1 queries.
        // - Filters by user ID and active statuses.
        // - Checks date and time:
        //   a) booking_date > todayDate (future date), OR
        //   b) booking_date == todayDate AND start_time > currentTime (session later today).
        // - Orders by booking_date ASC and start_time ASC so the earliest upcoming session comes first.
        // -------------------------------------------------------------------------
        $booking = Booking::with(['service', 'images'])
            ->where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->where(function ($query) use ($todayDate, $currentTime) {
                $query->where('booking_date', '>', $todayDate)
                      ->orWhere(function ($qToday) use ($todayDate, $currentTime) {
                          $qToday->where('booking_date', '=', $todayDate)
                                 ->where('start_time', '>', $currentTime);
                      });
            })
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        // -------------------------------------------------------------------------
        // 2. Presentation Data Preparation Block:
        // - Pre-calculates and formats all UI attributes (image URL, formatted date/time,
        //   full address string, badge styles, and route URLs) on the returned model object.
        // - Guarantees zero logic or formatting code in the Blade view.
        // -------------------------------------------------------------------------
        if ($booking) {
            // Extract raw status value and resolve Enum instance safely
            $statusVal = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            $statusEnum = BookingStatus::tryFrom(strtolower($statusVal)) ?? BookingStatus::PENDING;

            // Resolve 1st image URL: Use primary uploaded photo if present, otherwise fallback to default SVG placeholder
            $firstImage = $booking->images?->first();
            $imgPath = $firstImage?->image_path;
            if (!empty($imgPath)) {
                $imageUrl = str_starts_with($imgPath, 'public/') ? asset($imgPath) : asset('public/' . $imgPath);
            } else {
                $imageUrl = asset('public/assets/images/default-cleaning-placeholder.svg');
            }

            // Construct full readable address string (Unit/Suite/Floor, Address, Suburb, Postcode)
            $fullAddress = trim(
                ($booking->unit_suite_floor ? $booking->unit_suite_floor . ', ' : '') .
                ($booking->customer_address ?? '') .
                ($booking->suburb ? ', ' . $booking->suburb : '') .
                ($booking->postcode ? ' ' . $booking->postcode : '')
            );

            // Attach pre-formatted presentation attributes directly onto the booking object
            $booking->booking_id_formatted = 'BK-' . sprintf('%02d', $booking->id);
            $booking->image_url = $imageUrl;
            $booking->service_name = $booking->service?->name ?? 'Cleaning Service';
            $booking->booking_date_formatted = $booking->booking_date
                ? Carbon::parse($booking->booking_date)->format('D, d M Y')
                : 'Date to be scheduled';
            $booking->time_slot_formatted = $booking->start_time
                ? (Carbon::parse($booking->start_time)->format('g:i A') . ($booking->end_time ? ' - ' . Carbon::parse($booking->end_time)->format('g:i A') : ''))
                : 'Time to be scheduled';
            $booking->full_address = !empty($fullAddress) ? $fullAddress : 'Address not specified';
            $booking->status_raw = strtolower($statusVal);
            $booking->status_label = $statusEnum->label();
            $booking->status_badge_class = $statusEnum->badgeClass() . ($statusEnum === BookingStatus::PENDING ? ' text-dark' : '');
            $booking->details_url = route('customer.bookings.show', $booking->id);
            $booking->is_approved = (strtolower($statusVal) === 'approved');
            $booking->step4_url = route('booking-service.review-confirm', ['booking' => $booking->id]);
        }

        return $booking;
    }

    /**
     * Calculate and format customer dashboard stat card metrics.
     *
     * @param int $userId ID of the logged-in customer
     * @return object Formatted metrics object with pre-computed counts, values, tooltips, and URLs
     */
    public function getDashboardStats(int $userId): object
    {
        $now = Carbon::now();
        $todayDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');
        // dd($currentTime);

        $activeStatuses = [
            BookingStatus::PENDING,
            BookingStatus::APPROVED,
            BookingStatus::CONFIRMED,
            BookingStatus::PROCESSING,
        ];

        // 1. Upcoming active bookings count (pending, approved, confirmed, processing)
        $upcomingCount = Booking::where('user_id', $userId)
            ->whereIn('status', $activeStatuses)
            ->where(function ($query) use ($todayDate, $currentTime) {
                $query->where('booking_date', '>', $todayDate)
                      ->orWhere(function ($qToday) use ($todayDate, $currentTime) {
                          $qToday->where('booking_date', '=', $todayDate)
                                 ->where('start_time', '>', $currentTime);
                      });
            })
            ->count();

        // 2. Total Completed Bookings count (status = 'completed' AND payment_status = 'paid')
        $completedCount = Booking::where('user_id', $userId)
            ->where('status', BookingStatus::COMPLETED->value)
            ->where('payment_status', 'paid')
            ->count();

        // 3. Remaining Credits (Available Wallet Balance: Credits minus Debits)
        $walletTxs = WalletTransaction::where('user_id', $userId)->get();
        $credits = (float) $walletTxs->where('type', 'credit')->sum('amount');
        $debits  = (float) $walletTxs->where('type', 'debit')->sum('amount');
        $remainingCredits = max(0.00, $credits - $debits);

        // 4. Total Spent (status = 'completed' AND payment_status = 'paid')
        $totalSpent = (float) Booking::where('user_id', $userId)
            ->where('status', BookingStatus::COMPLETED->value)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return (object) [
            'upcoming_count'              => $upcomingCount,
            'upcoming_tooltip'            => 'Total count based on pending, approved, confirmed, and processing bookings.',
            'upcoming_url'                => route('customer.bookings.index'),

            'completed_count'             => $completedCount,
            'completed_tooltip'           => 'Total count based on completed bookings with paid payment status.',
            'completed_url'               => route('customer.bookings.index'),

            'remaining_credits'           => $remainingCredits,
            'remaining_credits_formatted' => '$' . number_format($remainingCredits, 2),
            'remaining_credits_tooltip'   => 'Available wallet credit balance for future bookings.',
            'remaining_credits_url'       => route('customer.wallet.index'),

            'total_spent'                 => $totalSpent,
            'total_spent_formatted'       => '$' . number_format($totalSpent, 2),
            'total_spent_tooltip'         => 'Total amount spent on completed bookings with paid payment status.',
            'total_spent_url'             => route('customer.bookings.index'),
        ];
    }

    /**
     * Get the latest recent bookings for the logged-in customer's dashboard.
     *
     * @param int $userId ID of the logged-in customer
     * @param int $limit Number of recent records to return
     * @return \Illuminate\Support\Collection Formatted collection of recent bookings
     */
    public function getRecentBookings(int $userId, int $limit = 3): \Illuminate\Support\Collection
    {
        $bookings = Booking::with(['service', 'images'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();

        return $bookings->map(function ($booking) {
            $statusVal = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            $statusEnum = BookingStatus::tryFrom(strtolower($statusVal)) ?? BookingStatus::PENDING;

            // Resolve 1st image URL
            $firstImage = $booking->images?->first();
            $imgPath = $firstImage?->image_path;
            if (!empty($imgPath)) {
                $imageUrl = str_starts_with($imgPath, 'public/') ? asset($imgPath) : asset('public/' . $imgPath);
            } else {
                $imageUrl = asset('public/assets/images/default-cleaning-placeholder.svg');
            }

            $statusClass = match (strtolower($statusVal)) {
                'completed'  => 'status-completed',
                'confirmed'  => 'status-confirmed',
                'approved'   => 'status-approved',
                'processing' => 'status-processing',
                'cancelled'  => 'status-cancelled',
                default      => 'status-pending',
            };

            $statusIcon = match (strtolower($statusVal)) {
                'completed', 'confirmed', 'approved' => 'fas fa-check-circle',
                'processing' => 'fas fa-spinner',
                'cancelled'  => 'fas fa-times-circle',
                default      => 'fas fa-clock',
            };

            return (object) [
                'id'                     => $booking->id,
                'booking_id_formatted' => 'BK-' . sprintf('%02d', $booking->id),
                'service_name'           => $booking->service?->name ?? 'Cleaning Service',
                'booking_date_formatted' => $booking->booking_date
                    ? Carbon::parse($booking->booking_date)->format('j M Y')
                    : 'Date pending',
                'time_slot_formatted'    => $booking->start_time
                    ? Carbon::parse($booking->start_time)->format('g:i A')
                    : '09:00 AM',
                'total_amount_formatted' => '$' . number_format((float) $booking->total_amount, 2),
                'status_label'           => $statusEnum->label(),
                'status_class'           => $statusClass,
                'status_icon'            => $statusIcon,
                'image_url'              => $imageUrl,
                'details_url'            => route('customer.bookings.show', $booking->id),
            ];
        });
    }

    /**
     * Get top 5 active cleaning services for Quick Book Again section.
     * Ranks most booked services (completed) first, then pads with active services (id ASC) to ensure 5 items.
     *
     * @param int $userId ID of the logged-in customer
     * @return \Illuminate\Support\Collection Formatted list of 5 quick book services
     */
    public function getQuickBookServices(int $userId): \Illuminate\Support\Collection
    {
        // 1. Query top completed service IDs for this customer
        $topServiceRecords = Booking::where('user_id', $userId)
            ->where('status', BookingStatus::COMPLETED->value)
            ->whereNotNull('service_id')
            ->selectRaw('service_id, COUNT(id) as total_completed')
            ->groupBy('service_id')
            ->orderByDesc('total_completed')
            ->take(7)
            ->get();

        $orderedServiceIds = $topServiceRecords->pluck('service_id')->toArray();

        // 2. Filter orderedServiceIds to include only currently active services
        if (!empty($orderedServiceIds)) {
            $activeTopIds = Service::whereIn('id', $orderedServiceIds)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();

            // Retain original completed count ranking order
            $orderedServiceIds = array_values(array_intersect($orderedServiceIds, $activeTopIds));
        }

        // 3. If under 5, pad with remaining active services ordered by id ASC
        $needed = 7 - count($orderedServiceIds);
        if ($needed > 0) {
            $fallbackIds = Service::where('status', 'active')
                ->when(!empty($orderedServiceIds), function ($query) use ($orderedServiceIds) {
                    $query->whereNotIn('id', $orderedServiceIds);
                })
                ->orderBy('id', 'asc')
                ->take($needed)
                ->pluck('id')
                ->toArray();

            $orderedServiceIds = array_merge($orderedServiceIds, $fallbackIds);
        }

        if (empty($orderedServiceIds)) {
            return collect();
        }

        // 4. Fetch service models and preserve exact ranking order
        $serviceMap = Service::whereIn('id', $orderedServiceIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $result = collect();
        $colorClasses = ['service-blue', 'service-green', 'service-purple', 'service-orange', 'service-cyan'];
        $iconClasses  = ['fas fa-home', 'fas fa-broom', 'fas fa-key', 'fas fa-building', 'far fa-window-maximize'];

        $index = 0;
        foreach ($orderedServiceIds as $serviceId) {
            $service = $serviceMap->get($serviceId);
            if ($service) {
                $result->push((object) [
                    'id'          => $service->id,
                    'name'        => $service->name,
                    'color_class' => $colorClasses[$index % count($colorClasses)],
                    'icon_class'  => $iconClasses[$index % count($iconClasses)],
                    'booking_url' => route('booking-service.create', ['service_id' => $service->id]),
                ]);
                $index++;
            }
        }

        return $result;
    }

    /**
     * Get referral program link and status data for customer dashboard.
     *
     * @param int $userId ID of the logged-in customer
     * @return object Referral program status object
     */
    public function getReferralProgramData(int $userId): object
    {
        $user = User::find($userId);

        $hasCompletedPaidBooking = Booking::where('user_id', $userId)
            ->where('status', BookingStatus::COMPLETED->value)
            ->where('payment_status', 'paid')
            ->exists();

        $referralCode = $user?->referral_code;
        // if (empty($referralCode) && $user) {
        //     $referralCode = strtoupper(($user->first_name ?: 'REF') . $user->id);
        //     $user->update(['referral_code' => $referralCode]);
        // }

        $referralLink = $hasCompletedPaidBooking ? url('/register?ref=' . $referralCode) : '';

        return (object) [
            'has_completed_paid_booking' => $hasCompletedPaidBooking,
            'referral_link'              => $referralLink,
        ];
    }
}
