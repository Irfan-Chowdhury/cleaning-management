<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display customer wallet dashboard and transaction history.
     */
    public function index()
    {
        $userId = Auth::id();
        $dbTransactions = WalletTransaction::where('user_id', $userId)->latest()->get();

        if ($dbTransactions->isNotEmpty()) {
            $totalCredit = (float) $dbTransactions->where('type', 'credit')->sum('amount');
            $totalDebit = (float) $dbTransactions->where('type', 'debit')->sum('amount');
            $availableBalance = $totalCredit - $totalDebit;

            $transactions = $dbTransactions->map(function ($tx) {
                return (object)[
                    'id'          => $tx->id,
                    'date'        => $tx->created_at ? $tx->created_at->format('Y-m-d') : now()->format('Y-m-d'),
                    'type'        => $tx->type,
                    'source'      => $tx->source,
                    'description' => $tx->description ?: ucfirst(str_replace('_', ' ', $tx->source)),
                    'booking_id'  => $tx->booking_id,
                    'credit'      => $tx->type === 'credit' ? (float) $tx->amount : 0.00,
                    'debit'       => $tx->type === 'debit' ? (float) $tx->amount : 0.00,
                ];
            });
        } else {
            $totalCredit = 0.00;
            $totalDebit = 0.00;
            $availableBalance = 0.00;
            $transactions = collect();
        }

        return view('pages.customer.wallet.index', compact('totalCredit', 'totalDebit', 'availableBalance', 'transactions'));
    }
}
