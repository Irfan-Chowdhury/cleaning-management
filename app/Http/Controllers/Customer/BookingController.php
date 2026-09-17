<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

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
                    'time'                 => $b->start_time ? ($b->start_time . ($b->end_time ? ' - ' . $b->end_time : '')) : '09:00 AM - 10:00 AM',
                    'amount'               => (float) $b->total_amount,
                    'status'               => ucfirst($statusVal),
                    'status_raw'           => strtolower($statusVal),
                    'payment_status'       => $paymentStatus,
                    'payment_method'       => $paymentMethod === 'pending' ? 'Pending Payment' : $paymentMethod,
                    'paid_amount'          => $paymentStatusRaw === 'paid' ? (float) $b->total_amount : 0.00,
                    'wallet_used'          => (float) $b->credit_used,
                    'cancellation_eligible'=> ($statusVal === 'pending' || $statusVal === 'approved'),
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
                    'time'                 => '09:00 AM - 11:00 AM',
                    'amount'               => 180.00,
                    'status'               => 'Approved',
                    'status_raw'           => 'approved',
                    'payment_status'       => 'Paid',
                    'payment_method'       => 'Credit Card (Visa **** 4242)',
                    'paid_amount'          => 180.00,
                    'wallet_used'          => 0.00,
                    'cancellation_eligible'=> true,
                    'questionnaires'       => []
                ]
            ]);
        }

        return view('pages.customer.booking.index', compact('bookings'));
    }
}
