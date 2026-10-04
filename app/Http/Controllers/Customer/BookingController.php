<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    /* ========================================================================= */
    /* CUSTOMER SECTION PART                                                     */
    /* ========================================================================= */

    /**
     * Display a listing of the customer's bookings.
     */
    public function index()
    {
        $bookings = $this->bookingService->getCustomerBookings((int) Auth::id());
        return view('pages.customer.booking.index', compact('bookings'));
    }

    /**
     * Display dedicated booking details page for customer (/my-bookings/{id}).
     */
    public function show(int $id)
    {
        $booking = $this->bookingService->getCustomerBookingDetails($id, (int) Auth::id());
        return view('pages.customer.booking.show', compact('booking'));
    }

    /**
     * Cancel an approved booking by customer and notify admin.
     */
    public function cancel(Booking $booking): JsonResponse
    {
        $result = $this->bookingService->cancelCustomerBooking($booking, (int) Auth::id());

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], $result['code']);
        }

        return response()->json($result);
    }
}
