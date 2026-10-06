<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
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
            $booking->status_badge_class = match (strtolower($statusVal)) {
                'confirmed'  => 'status-confirmed',
                'approved'   => 'status-approved',
                'processing' => 'status-processing',
                default      => 'status-pending',
            };
            $booking->details_url = route('customer.bookings.show', $booking->id);
            $booking->is_approved = (strtolower($statusVal) === 'approved');
            $booking->step4_url = route('booking-service.review-confirm', ['booking' => $booking->id]);
        }

        return $booking;
    }
}
