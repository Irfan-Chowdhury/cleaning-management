<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
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
                'payment_status'  => 'paid',
            ],
            (object)[
                'id'              => 2,
                'customer_name'   => 'Bob Smith',
                'customer_email'  => 'bob@example.com',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Bob+Smith&background=2b8a3e&color=fff&size=128',
                'service_name'    => 'Office Sanitation Service',
                'date'            => '2026-08-21',
                'slot'            => '09:30 AM',
                'amount'          => 350.50,
                'payment_status'  => 'pending',
            ],
            (object)[
                'id'              => 3,
                'customer_name'   => 'Carol White',
                'customer_email'  => 'carol@example.com',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Carol+White&background=e03131&color=fff&size=128',
                'service_name'    => 'Carpet Wash & Steam',
                'date'            => '2026-08-22',
                'slot'            => '02:00 PM',
                'amount'          => 120.00,
                'payment_status'  => 'paid',
            ],
            (object)[
                'id'              => 4,
                'customer_name'   => 'David Brown',
                'customer_email'  => 'david@example.com',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=David+Brown&background=f59f00&color=fff&size=128',
                'service_name'    => 'Window Washing',
                'date'            => '2026-08-23',
                'slot'            => '04:30 PM',
                'amount'          => 95.00,
                'payment_status'  => 'failed',
            ],
            (object)[
                'id'              => 5,
                'customer_name'   => 'Eva Martinez',
                'customer_email'  => 'eva@example.com',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Eva+Martinez&background=9c36b5&color=fff&size=128',
                'service_name'    => 'Move-in / Move-out Cleaning',
                'date'            => '2026-08-24',
                'slot'            => '07:00 AM',
                'amount'          => 240.00,
                'payment_status'  => 'paid',
            ],
            (object)[
                'id'              => 6,
                'customer_name'   => 'Frank Lee',
                'customer_email'  => 'frank@example.com',
                'customer_avatar' => 'https://ui-avatars.com/api/?name=Frank+Lee&background=1098ad&color=fff&size=128',
                'service_name'    => 'Kitchen Maintenance',
                'date'            => '2026-08-25',
                'slot'            => '11:00 AM',
                'amount'          => 150.00,
                'payment_status'  => 'refunded',
            ],
        ]);

        return view('pages.admin.bookings.index', compact('bookings'));
    }

    public function show(int $id)
    {
        $booking = (object)[
            'id'               => $id,
            'customer_name'    => 'Alice Johnson',
            'customer_email'   => 'alice@example.com',
            'customer_phone'   => '+1 (555) 234-5678',
            'customer_gender'  => 'Female',
            'customer_address' => '123 Main Street, Suite 400, New York, NY 10001',
            'customer_avatar'  => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
            'service_name'     => 'Deep Home Cleaning',
            'date'             => '2026-08-20',
            'slot'             => '07:00 AM',
            'amount'           => 180.00,
            'payment_status'   => 'paid',
        ];

        return view('pages.admin.bookings.show', compact('booking'));
    }

    public function edit(int $id)
    {
        try {
            $services = \App\Models\Service::where('status', 'active')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $services = collect([
                (object)['id' => 1, 'name' => 'Deep Home Cleaning'],
                (object)['id' => 2, 'name' => 'Office Sanitation Service'],
                (object)['id' => 3, 'name' => 'Carpet Wash & Steam'],
                (object)['id' => 4, 'name' => 'Window Washing'],
                (object)['id' => 5, 'name' => 'Move-in / Move-out Cleaning'],
                (object)['id' => 6, 'name' => 'Kitchen Maintenance'],
            ]);
        }

        $booking = (object)[
            'id'              => $id,
            'customer_name'   => 'Alice Johnson',
            'customer_email'  => 'alice@example.com',
            'customer_phone'  => '+1 (555) 234-5678',
            'customer_gender' => 'Female',
            'customer_avatar' => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
            'service_id'     => 1,
            'service_name'   => 'Deep Home Cleaning',
            'date'           => '2026-08-20',
            'slot'           => '07:00 AM',
            'amount'         => 180.00,
            'payment_status' => 'paid',
        ];

        return view('pages.admin.bookings.edit', compact('booking', 'services'));
    }

    public function update(Request $request, int $id)
    {
        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully!');
    }
}
