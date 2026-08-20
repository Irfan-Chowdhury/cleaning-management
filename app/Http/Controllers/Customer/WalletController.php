<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display customer wallet dashboard and transaction history.
     */
    public function index()
    {
        $totalCredit = 1700.00;
        $totalDebit = 500.00;
        $availableBalance = $totalCredit - $totalDebit;

        $transactions = collect([
            (object)[
                'id'          => 1,
                'date'        => '2026-08-01',
                'type'        => 'credit',
                'source'      => 'welcome_bonus',
                'description' => 'Welcome Registration Bonus Credit',
                'booking_id'  => null,
                'credit'      => 50.00,
                'debit'       => 0.00,
            ],
            (object)[
                'id'          => 2,
                'date'        => '2026-08-05',
                'type'        => 'credit',
                'source'      => 'referral_bonus',
                'description' => 'Referral Bonus for inviting new customer @john_doe',
                'booking_id'  => null,
                'credit'      => 150.00,
                'debit'       => 0.00,
            ],
            (object)[
                'id'          => 3,
                'date'        => '2026-08-10',
                'type'        => 'credit',
                'source'      => 'review_bonus',
                'description' => 'Reward for Google Review Feedback',
                'booking_id'  => null,
                'credit'      => 25.00,
                'debit'       => 0.00,
            ],
            (object)[
                'id'          => 4,
                'date'        => '2026-08-12',
                'type'        => 'credit',
                'source'      => 'admin_adjustment',
                'description' => 'Promotional Loyalty Top-up by Admin',
                'booking_id'  => null,
                'credit'      => 1475.00,
                'debit'       => 0.00,
            ],
            (object)[
                'id'          => 5,
                'date'        => '2026-08-15',
                'type'        => 'debit',
                'source'      => 'booking_usage',
                'description' => 'Wallet Payment for Deep Home Cleaning',
                'booking_id'  => 'BK-001',
                'credit'      => 0.00,
                'debit'       => 180.00,
            ],
            (object)[
                'id'          => 6,
                'date'        => '2026-08-18',
                'type'        => 'debit',
                'source'      => 'booking_usage',
                'description' => 'Wallet Payment for Carpet Wash & Steam',
                'booking_id'  => 'BK-003',
                'credit'      => 0.00,
                'debit'       => 120.00,
            ],
            (object)[
                'id'          => 7,
                'date'        => '2026-08-20',
                'type'        => 'debit',
                'source'      => 'booking_usage',
                'description' => 'Wallet Payment for Move-in / Move-out Cleaning',
                'booking_id'  => 'BK-005',
                'credit'      => 0.00,
                'debit'       => 200.00,
            ],
        ]);

        return view('pages.customer.wallet.index', compact('totalCredit', 'totalDebit', 'availableBalance', 'transactions'));
    }
}
