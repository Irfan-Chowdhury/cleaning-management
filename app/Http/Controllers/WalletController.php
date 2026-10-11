<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Display listing of all user wallets with financial summaries.
     *
     * @return View
     */
    public function index(): View
    {
        $wallets = $this->walletService->getAllWallets();

        return view('pages.admin.wallet.index', compact('wallets'));
    }

    /**
     * Display wallet details, customer info, summary and transaction history for a specific user.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $customer      = (object) $this->walletService->getCustomerDetails($id);
        $walletSummary = (object) $this->walletService->getWalletSummary($id);
        $transactions  = $this->walletService->getTransactionHistory($id);
        $user          = $customer;

        return view('pages.admin.wallet.show', compact('customer', 'user', 'walletSummary', 'transactions'));
    }
}
