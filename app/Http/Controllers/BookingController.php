<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminBookingUpdateRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {
    }

    public function index()
    {
        $dbBookings = Booking::with(['user', 'service', 'payment'])->orderBy('id', 'desc')->get();

        if ($dbBookings->isNotEmpty()) {
            $bookings = $dbBookings->map(function ($b) {
                return (object) [
                    'id'              => $b->id,
                    'booking_id'       => 'BK-' . sprintf('%03d', $b->id),
                    'customer_name'   => $b->customer_name ?: ($b->user ? trim($b->user->first_name . ' ' . $b->user->last_name) : 'Guest Customer'),
                    'customer_email'  => $b->customer_email ?: ($b->user->email ?? 'N/A'),
                    'customer_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($b->customer_name ?: 'Customer') . '&background=0D8ABC&color=fff&size=128',
                    'service_name'    => $b->service?->name ?? 'Cleaning Service',
                    'date'            => $b->booking_date ?? $b->created_at->format('Y-m-d'),
                    'slot'            => $b->start_time ?? '09:00 AM',
                    'amount'          => (float) $b->total_amount,
                    'status'          => $b->status instanceof \App\Enums\BookingStatus ? $b->status->value : (string) $b->status,
                    'payment_status'  => strtolower($b->payment_status ?? $b->payment?->payment_status ?? 'pending'),
                    'payment_method'  => $b->payment_method ?? $b->payment?->payment_method ?? 'pending',
                ];
            });
        } else {
            $bookings = collect([
                (object)[
                    'id'              => 1,
                    'customer_name'   => 'Alice Johnson',
                    'customer_email'  => 'alice@example.com',
                    'customer_avatar' => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
                    'service_name'    => 'Deep Home Cleaning',
                    'date'            => '2026-08-20',
                    'slot'            => '07:00 AM',
                    'amount'          => 180.00,
                    'status'          => 'pending',
                    'payment_status'  => 'pending',
                    'payment_method'  => 'pending',
                ]
            ]);
        }

        return view('pages.admin.bookings.index', compact('bookings'));
    }

    public function show(int $id)
    {
        $bookingModel = Booking::with(['user', 'service', 'payment'])->find($id);

        if ($bookingModel) {
            $booking = (object)[
                'id'                   => $bookingModel->id,
                'booking_id'           => 'BK-' . sprintf('%03d', $bookingModel->id),
                'user_id'              => $bookingModel->user_id,
                'customer_name'        => $bookingModel->customer_name ?: ($bookingModel->user ? trim($bookingModel->user->first_name . ' ' . $bookingModel->user->last_name) : 'Guest Customer'),
                'customer_email'       => $bookingModel->customer_email ?: ($bookingModel->user->email ?? 'N/A'),
                'customer_phone'       => $bookingModel->customer_phone ?: ($bookingModel->user->phone ?? 'N/A'),
                'customer_gender'      => $bookingModel->user->gender ?? 'N/A',
                'customer_address'     => $bookingModel->customer_address ?: ($bookingModel->user->address ?? 'N/A'),
                'unit_suite_floor'     => $bookingModel->unit_suite_floor ?? 'N/A',
                'suburb'               => $bookingModel->suburb ?? 'N/A',
                'postcode'             => $bookingModel->postcode ?? 'N/A',
                'special_instructions' => $bookingModel->special_instructions ?? 'None',
                'service_notes'        => $bookingModel->service_notes ?? 'None',
                'customer_avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($bookingModel->customer_name ?: 'Customer') . '&background=0D8ABC&color=fff&size=128',
                'service_id'           => $bookingModel->service_id,
                'service_name'         => $bookingModel->service?->name ?? 'Cleaning Service',
                'frequency'            => ucfirst(str_replace('_', ' ', $bookingModel->frequency ?? 'one_time')),
                'date'                 => $bookingModel->booking_date ?? $bookingModel->created_at->format('Y-m-d'),
                'slot'                 => $bookingModel->start_time ? ($bookingModel->start_time . ($bookingModel->end_time ? ' - ' . $bookingModel->end_time : '')) : '09:00 AM',
                'subtotal'             => (float) $bookingModel->subtotal,
                'discount_amount'      => (float) $bookingModel->discount_amount,
                'credit_used'          => (float) $bookingModel->credit_used,
                'amount'               => (float) $bookingModel->total_amount,
                'referal_code'         => $bookingModel->referal_code,
                'promo_code'           => $bookingModel->promo_code,
                'status'               => $bookingModel->status instanceof \App\Enums\BookingStatus ? $bookingModel->status->value : (string) $bookingModel->status,
                'payment_status'       => strtolower($bookingModel->payment_status ?? $bookingModel->payment?->payment_status ?? 'pending'),
                'payment_method'       => $bookingModel->payment_method ?? $bookingModel->payment?->payment_method ?? 'pending',
                'answers'              => is_array($bookingModel->answers) ? $bookingModel->answers : [],
                'created_at'           => $bookingModel->created_at ? $bookingModel->created_at->format('M d, Y g:i A') : 'N/A',
                'model'                => $bookingModel,
            ];
        } else {
            $booking = (object)[
                'id'                   => $id,
                'booking_id'           => 'BK-' . sprintf('%03d', $id),
                'user_id'              => null,
                'customer_name'        => 'Alice Johnson',
                'customer_email'       => 'alice@example.com',
                'customer_phone'       => '+1 (555) 234-5678',
                'customer_gender'      => 'Female',
                'customer_address'     => '123 Main Street, Suite 400, Sydney NSW 2000',
                'unit_suite_floor'     => 'Unit 5, Floor 2',
                'suburb'               => 'Sydney',
                'postcode'             => '2000',
                'special_instructions' => 'Please focus on kitchen and bathrooms.',
                'service_notes'        => 'Eco-friendly supplies preferred.',
                'customer_avatar'      => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
                'service_id'           => 1,
                'service_name'         => 'Deep Home Cleaning',
                'frequency'            => 'One time',
                'date'                 => '2026-08-20',
                'slot'                 => '07:00 AM - 08:00 AM',
                'subtotal'             => 0.00,
                'discount_amount'      => 0.00,
                'credit_used'          => 0.00,
                'amount'               => 0.00,
                'referal_code'         => null,
                'promo_code'           => null,
                'status'               => 'pending',
                'payment_status'       => 'pending',
                'payment_method'       => 'pending',
                'answers'              => [],
                'created_at'           => 'Aug 20, 2026 07:00 AM',
                'model'                => null,
            ];
        }

        return view('pages.admin.bookings.show', compact('booking'));
    }

    public function edit(int $id)
    {
        $services = Service::where('status', 'active')->orderBy('name')->get();

        $bookingModel = Booking::with(['user', 'service', 'payment'])->find($id);

        if ($bookingModel) {
            $booking = (object)[
                'id'                   => $bookingModel->id,
                'user_id'              => $bookingModel->user_id,
                'customer_name'        => $bookingModel->customer_name ?: ($bookingModel->user ? trim($bookingModel->user->first_name . ' ' . $bookingModel->user->last_name) : 'N/A'),
                'customer_email'       => $bookingModel->customer_email ?: ($bookingModel->user->email ?? 'N/A'),
                'customer_phone'       => $bookingModel->customer_phone ?: ($bookingModel->user->phone ?? 'N/A'),
                'customer_address'     => $bookingModel->customer_address ?: ($bookingModel->user->address ?? 'N/A'),
                'unit_suite_floor'     => $bookingModel->unit_suite_floor ?? 'N/A',
                'suburb'               => $bookingModel->suburb ?? 'N/A',
                'postcode'             => $bookingModel->postcode ?? 'N/A',
                'special_instructions' => $bookingModel->special_instructions ?? 'None',
                'service_notes'        => $bookingModel->service_notes ?? 'None',
                'customer_gender'      => $bookingModel->user->gender ?? 'N/A',
                'customer_avatar'      => 'https://ui-avatars.com/api/?name=' . urlencode($bookingModel->customer_name ?: 'Customer') . '&background=0D8ABC&color=fff&size=128',
                'service_id'           => $bookingModel->service_id,
                'service_name'         => $bookingModel->service?->name ?? 'N/A',
                'date'                 => $bookingModel->booking_date ?? $bookingModel->created_at->format('Y-m-d'),
                'slot'                 => $bookingModel->start_time ?? '09:00 AM',
                'amount'               => (float) $bookingModel->total_amount,
                'status'               => $bookingModel->status instanceof \App\Enums\BookingStatus ? $bookingModel->status->value : (string) $bookingModel->status,
                'payment_status'       => strtolower($bookingModel->payment_status ?? $bookingModel->payment?->payment_status ?? 'pending'),
                'payment_method'       => $bookingModel->payment_method ?? $bookingModel->payment?->payment_method ?? 'pending',
                'answers'              => is_array($bookingModel->answers) ? $bookingModel->answers : [],
                'model'                => $bookingModel,
            ];
        } else {
            $booking = (object)[
                'id'                   => $id,
                'user_id'              => null,
                'customer_name'        => 'Alice Johnson',
                'customer_email'       => 'alice@example.com',
                'customer_phone'       => '+1 (555) 234-5678',
                'customer_address'     => '123 Main Street, Suite 400, Sydney NSW 2000',
                'unit_suite_floor'     => 'Unit 5, Floor 2',
                'suburb'               => 'Sydney',
                'postcode'             => '2000',
                'special_instructions' => 'Please focus on kitchen and bathrooms.',
                'service_notes'        => 'Eco-friendly supplies preferred.',
                'customer_gender'      => 'Female',
                'customer_avatar'      => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
                'service_id'           => 1,
                'service_name'         => 'Deep Home Cleaning',
                'date'                 => '2026-08-20',
                'slot'                 => '07:00 AM',
                'amount'               => 180.00,
                'status'               => 'pending',
                'payment_status'       => 'pending',
                'payment_method'       => 'pending',
                'model'                => null,
            ];
        }

        return view('pages.admin.bookings.edit', compact('booking', 'services'));
    }

    public function update(AdminBookingUpdateRequest $request, int $id)
    {
        $validated = $request->validated();
        $booking = Booking::find($id);

        if ($booking) {
            $this->bookingService->updateBookingByAdmin($booking, $validated, auth()->user());
        }

        return redirect()->route('bookings.index')->with('success', "Booking #{$id} updated successfully!");
    }
}
