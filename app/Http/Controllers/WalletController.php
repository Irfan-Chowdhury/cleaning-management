<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class WalletController extends Controller
{
    public function index()
    {
        // TODO: Replace dummy data with real query when wallet relationships are ready.
        // $wallets = User::withSum('creditTransactions as total_credit', 'amount')
        //     ->withSum('debitTransactions as total_debit', 'amount')
        //     ->get()
        //     ->map(function ($user) {
        //         $user->total_credit = $user->total_credit ?? 0;
        //         $user->total_debit  = $user->total_debit ?? 0;
        //         $user->remaining_balance = $user->total_credit - $user->total_debit;
        //         return $user;
        //     });

        $wallets = collect([
            (object)['id' => 1, 'name' => 'Alice Johnson', 'email' => 'alice@example.com',   'total_credit' => 1500.00, 'total_debit' => 320.50,  'remaining_balance' => 1179.50],
            (object)['id' => 2, 'name' => 'Bob Smith',     'email' => 'bob@example.com',     'total_credit' => 1200.00, 'total_debit' => 950.00,  'remaining_balance' => 250.00],
            (object)['id' => 3, 'name' => 'Carol White',   'email' => 'carol@example.com',   'total_credit' => 3200.00, 'total_debit' => 1100.75, 'remaining_balance' => 2099.25],
            (object)['id' => 4, 'name' => 'David Brown',   'email' => 'david@example.com',   'total_credit' => 900.00,  'total_debit' => 450.00,  'remaining_balance' => 450.00],
            (object)['id' => 5, 'name' => 'Eva Martinez',  'email' => 'eva@example.com',     'total_credit' => 2750.00, 'total_debit' => 600.00,  'remaining_balance' => 2150.00],
            (object)['id' => 6, 'name' => 'Frank Lee',     'email' => 'frank@example.com',   'total_credit' => 500.00,  'total_debit' => 80.00,   'remaining_balance' => 420.00],
        ]);

        return view('pages.admin.wallet.index', compact('wallets'));
    }

    public function show(int $id)
    {
        // Dummy user details
        $user = (object)[
            'id'                => $id,
            'name'              => 'Alice Johnson',
            'email'             => 'alice@example.com',
            'phone'             => '+1 (555) 234-5678',
            'gender'            => 'Female',
            'photo'             => 'https://ui-avatars.com/api/?name=Alice+Johnson&background=0D8ABC&color=fff&size=128',
            'total_credit'      => 1500.00,
            'total_debit'       => 320.50,
            'remaining_balance' => 1179.50,
        ];

        // Dummy transactions data for table
        $transactions = collect([
            (object)['id' => 1, 'credit' => 500.00, 'debit' => 0.00,   'source' => 'Bank Transfer - Deposit',    'created_at' => '2026-08-10 10:15:00'],
            (object)['id' => 2, 'credit' => 0.00,   'debit' => 120.50, 'source' => 'Home Deep Cleaning Booking', 'created_at' => '2026-08-12 14:30:00'],
            (object)['id' => 3, 'credit' => 1000.00,'debit' => 0.00,   'source' => 'Promotional Bonus',          'created_at' => '2026-08-15 09:00:00'],
            (object)['id' => 4, 'credit' => 0.00,   'debit' => 200.00, 'source' => 'Office Sanitation Service',  'created_at' => '2026-08-18 16:45:00'],
        ]);

        return view('pages.admin.wallet.show', compact('user', 'transactions'));
    }
}
