<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the customer's bookings.
     */
    public function index()
    {
        $bookings = collect([
            (object)[
                'id'                   => 1,
                'booking_id'           => 'BK-001',
                'service_name'         => 'Deep Home Cleaning',
                'date'                 => '2026-08-20',
                'time'                 => '09:00 AM - 11:00 AM',
                'amount'               => 180.00,
                'status'               => 'Confirmed',
                'payment_status'       => 'Paid',
                'payment_method'       => 'Credit Card (Visa **** 4242)',
                'paid_amount'          => 180.00,
                'wallet_used'          => 0.00,
                'cancellation_eligible'=> true,
                'questionnaires'       => [
                    [
                        'question' => 'How many bedrooms and bathrooms require cleaning?',
                        'answer'   => '3 Bedrooms, 2 Bathrooms'
                    ],
                    [
                        'question' => 'Are there any pets present in the residence?',
                        'answer'   => 'Yes (1 Golden Retriever, friendly)'
                    ],
                    [
                        'question' => 'Any special requests or focus areas?',
                        'answer'   => 'Please perform deep scrubbing on kitchen oven and master shower tiles.'
                    ]
                ]
            ],
            (object)[
                'id'                   => 2,
                'booking_id'           => 'BK-002',
                'service_name'         => 'Office Sanitation Service',
                'date'                 => '2026-08-21',
                'time'                 => '10:00 AM - 12:30 PM',
                'amount'               => 350.50,
                'status'               => 'Pending',
                'payment_status'       => 'Unpaid',
                'payment_method'       => 'Pending Payment Selection',
                'paid_amount'          => 0.00,
                'wallet_used'          => 0.00,
                'cancellation_eligible'=> true,
                'questionnaires'       => [
                    [
                        'question' => 'Total office floor area size?',
                        'answer'   => '2,500 sq. ft. (Open floor + 4 private cabins)'
                    ],
                    [
                        'question' => 'Sanitation type preference?',
                        'answer'   => 'Hospital-grade Surface & Air Fogging'
                    ],
                    [
                        'question' => 'Building entry instructions?',
                        'answer'   => 'Pick up keycard at main reception security desk.'
                    ]
                ]
            ],
            (object)[
                'id'                   => 3,
                'booking_id'           => 'BK-003',
                'service_name'         => 'Carpet Wash & Steam',
                'date'                 => '2026-08-22',
                'time'                 => '02:00 PM - 04:00 PM',
                'amount'               => 120.00,
                'status'               => 'Completed',
                'payment_status'       => 'Paid',
                'payment_method'       => 'Customer Wallet',
                'paid_amount'          => 120.00,
                'wallet_used'          => 120.00,
                'cancellation_eligible'=> false,
                'questionnaires'       => [
                    [
                        'question' => 'Number of carpeted rooms or areas?',
                        'answer'   => '2 Bedrooms + 1 Large Living Room Carpet'
                    ],
                    [
                        'question' => 'Tough stain treatment requested?',
                        'answer'   => 'Coffee stain treatment on living room rug'
                    ]
                ]
            ],
            (object)[
                'id'                   => 4,
                'booking_id'           => 'BK-004',
                'service_name'         => 'Window Washing',
                'date'                 => '2026-08-23',
                'time'                 => '04:30 PM - 06:00 PM',
                'amount'               => 95.00,
                'status'               => 'Cancelled',
                'payment_status'       => 'Refunded',
                'payment_method'       => 'Credit Card (Refunded)',
                'paid_amount'          => 0.00,
                'wallet_used'          => 0.00,
                'cancellation_eligible'=> false,
                'questionnaires'       => [
                    [
                        'question' => 'Building height and total exterior windows?',
                        'answer'   => '2-Story House, 14 Exterior Windows'
                    ],
                    [
                        'question' => 'Include window screen cleaning?',
                        'answer'   => 'Yes, clean all window mesh screens'
                    ]
                ]
            ],
            (object)[
                'id'                   => 5,
                'booking_id'           => 'BK-005',
                'service_name'         => 'Move-in / Move-out Cleaning',
                'date'                 => '2026-08-24',
                'time'                 => '08:00 AM - 12:00 PM',
                'amount'               => 240.00,
                'status'               => 'Confirmed',
                'payment_status'       => 'Paid',
                'payment_method'       => 'Credit Card + Wallet',
                'paid_amount'          => 200.00,
                'wallet_used'          => 40.00,
                'cancellation_eligible'=> true,
                'questionnaires'       => [
                    [
                        'question' => 'Property type and occupancy state?',
                        'answer'   => 'Unfurnished 2-Bedroom Condo (Move-Out)'
                    ],
                    [
                        'question' => 'Inside appliance deep cleaning required?',
                        'answer'   => 'Yes, include refrigerator and oven interiors'
                    ]
                ]
            ],
            (object)[
                'id'                   => 6,
                'booking_id'           => 'BK-006',
                'service_name'         => 'Kitchen Deep Clean',
                'date'                 => '2026-08-25',
                'time'                 => '01:00 PM - 03:00 PM',
                'amount'               => 150.00,
                'status'               => 'Pending',
                'payment_status'       => 'Unpaid',
                'payment_method'       => 'Cash on Service Completion',
                'paid_amount'          => 0.00,
                'wallet_used'          => 0.00,
                'cancellation_eligible'=> true,
                'questionnaires'       => [
                    [
                        'question' => 'Oven hood and range degreasing needed?',
                        'answer'   => 'Yes, heavy grease accumulation treatment'
                    ],
                    [
                        'question' => 'Cabinet interior wiping?',
                        'answer'   => 'Clean empty pantry shelves inside'
                    ]
                ]
            ],
        ]);

        return view('pages.customer.booking.index', compact('bookings'));
    }
}
