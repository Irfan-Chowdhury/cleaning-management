<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of the customer's bookings.
     */
    public function index()
    {
        $userId = Auth::id();
        $dbBookings = Booking::with(['service', 'payment'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        if ($dbBookings->isNotEmpty()) {
            $bookings = $dbBookings->map(function ($b) {
                $statusVal = $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : (string) $b->status;
                $paymentStatusRaw = strtolower($b->payment?->payment_status ?? $b->payment_status ?? 'pending');
                $paymentStatus = ucfirst($paymentStatusRaw);
                $paymentMethod = $b->payment?->payment_method ?? $b->payment_method ?? 'Pending Payment';

                $questionnaires = [];
                if (!empty($b->answers) && is_array($b->answers)) {
                    $questionnaires = $b->answers;
                }
                if (!empty($b->service_notes)) {
                    $questionnaires[] = [
                        'question' => 'Service Notes',
                        'answer'   => $b->service_notes,
                    ];
                }
                if (!empty($b->special_instructions)) {
                    $questionnaires[] = [
                        'question' => 'Special Instructions',
                        'answer'   => $b->special_instructions,
                    ];
                }

                return (object)[
                    'id'                   => $b->id,
                    'booking_id'           => 'BK-' . sprintf('%03d', $b->id),
                    'service_name'         => $b->service?->name ?? 'Cleaning Service',
                    'date'                 => $b->booking_date ?? $b->created_at->format('Y-m-d'),
                    'time'                 => $b->start_time ? \Carbon\Carbon::parse($b->start_time)->format('g:i A') : '09:00 AM',
                    'amount'               => (float) $b->total_amount,
                    'status'               => ucfirst($statusVal),
                    'status_raw'           => strtolower($statusVal),
                    'payment_status'       => $paymentStatus,
                    'payment_method'       => $paymentMethod === 'pending' ? 'Pending Payment' : $paymentMethod,
                    'paid_amount'          => $paymentStatusRaw === 'paid' ? (float) $b->total_amount : 0.00,
                    'wallet_used'          => (float) $b->credit_used,
                    'credit_used'          => (float) $b->credit_used,
                    'discount_amount'      => (float) $b->discount_amount,
                    'referal_code'         => $b->referal_code,
                    'promo_code'           => $b->promo_code,
                    'cancellation_eligible'=> ($statusVal === 'approved'),
                    'questionnaires'       => $questionnaires,
                ];
            });
        } else {
            $bookings = collect([
                (object)[
                    'id'                   => 1,
                    'booking_id'           => 'BK-001',
                    'service_name'         => 'Deep Home Cleaning',
                    'date'                 => '2026-08-20',
                    'time'                 => '09:00 AM',
                    'amount'               => 180.00,
                    'status'               => 'Approved',
                    'status_raw'           => 'approved',
                    'payment_status'       => 'Paid',
                    'payment_method'       => 'Credit Card (Visa **** 4242)',
                    'paid_amount'          => 180.00,
                    'wallet_used'          => 0.00,
                    'credit_used'          => 0.00,
                    'discount_amount'      => 0.00,
                    'referal_code'         => null,
                    'promo_code'           => null,
                    'cancellation_eligible'=> true,
                    'questionnaires'       => []
                ]
            ]);
        }

        return view('pages.customer.booking.index', compact('bookings'));
    }

    /**
     * Cancel an approved booking by customer and notify admin.
     */
    public function cancel(Booking $booking): JsonResponse
    {
        if ((int) $booking->user_id !== (int) Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $statusVal = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;

        if (strtolower($statusVal) !== 'approved') {
            return response()->json([
                'error' => 'Only approved bookings can be cancelled schedule.'
            ], 422);
        }

        $booking->status = BookingStatus::CANCELLED;
        $booking->save();

        // Send app notification to all Admins
        try {
            $admins = User::where('role', 1)->get();
            foreach ($admins as $admin) {
                $admin->notify(new BookingCancelledNotification($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin booking cancellation notification: ' . $e->getMessage());
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Booking schedule has been cancelled successfully.',
            'status'     => 'Cancelled',
            'status_raw' => 'cancelled',
        ]);
    }
}
