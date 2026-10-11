<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

class WalletService
{
    /**
     * Get summary of all user wallets with credit, debit, and remaining balance.
     *
     * @return Collection
     */
    public function getAllWallets(): Collection
    {
        return User::query()
            ->where(function ($query) {
                $query->where('role', 2)
                    ->orWhereHas('walletTransactions');
            })
            ->withSum(['walletTransactions as total_credit' => function ($q) {
                $q->where('type', 'credit');
            }], 'amount')
            ->withSum(['walletTransactions as total_debit' => function ($q) {
                $q->where('type', 'debit');
            }], 'amount')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function (User $user) {
                $credit = (float) ($user->total_credit ?? 0);
                $debit  = (float) ($user->total_debit ?? 0);
                $remaining = $credit - $debit;

                $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                if (empty($fullName)) {
                    $fullName = 'User #' . $user->id;
                }

                return (object) [
                    'id'                => $user->id,
                    'name'              => $fullName,
                    'first_name'        => $user->first_name,
                    'last_name'         => $user->last_name,
                    'email'             => $user->email,
                    'phone'             => $user->phone,
                    'total_credit'      => $credit,
                    'total_debit'       => $debit,
                    'remaining_balance' => $remaining,
                ];
            });
    }

    /**
     * Get customer profile details for the wallet view.
     *
     * @param int $userId
     * @return array
     */
    public function getCustomerDetails(int $userId): array
    {
        $user = User::findOrFail($userId);

        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($fullName)) {
            $fullName = 'User #' . $user->id;
        }

        return [
            'id'         => $user->id,
            'name'       => $fullName,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'phone'      => $user->phone ?: 'N/A',
            'gender'     => $user->gender ? ucfirst($user->gender) : 'N/A',
            'address'    => $user->address ?: 'N/A',
            'photo'      => $user->photo_url,
            'is_active'  => (bool) $user->is_active,
            'created_at' => $user->created_at,
        ];
    }

    /**
     * Get wallet financial summary (credit, debit, remaining balance) for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getWalletSummary(int $userId): array
    {
        $transactions = WalletTransaction::where('user_id', $userId)->get();

        $totalCredit = (float) $transactions->where('type', 'credit')->sum('amount');
        $totalDebit  = (float) $transactions->where('type', 'debit')->sum('amount');
        $remaining   = $totalCredit - $totalDebit;

        return [
            'total_credit'      => $totalCredit,
            'total_debit'       => $totalDebit,
            'remaining_balance' => $remaining,
        ];
    }

    /**
     * Get complete transaction history for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getTransactionHistory(int $userId): Collection
    {
        return WalletTransaction::where('user_id', $userId)
            ->latest('created_at')
            ->latest('id')
            ->get()
            ->map(function (WalletTransaction $tx) {
                return (object) [
                    'id'          => $tx->id,
                    'booking_id'  => $tx->booking_id,
                    'type'        => $tx->type,
                    'credit'      => $tx->type === 'credit' ? (float) $tx->amount : 0.00,
                    'debit'       => $tx->type === 'debit' ? (float) $tx->amount : 0.00,
                    'source'      => $this->formatTransactionSource($tx->source),
                    'raw_source'  => $tx->source,
                    'description' => $tx->description,
                    'created_at'  => $tx->created_at,
                ];
            });
    }

    /**
     * Format raw transaction source string to a human-readable label.
     *
     * @param string|null $source
     * @return string
     */
    protected function formatTransactionSource(?string $source): string
    {
        if (empty($source)) {
            return 'General Transaction';
        }

        $labels = [
            'welcome_bonus'    => 'Welcome Bonus',
            'referral_bonus'   => 'Referral Bonus',
            'review_bonus'     => 'Google Review Bonus',
            'booking_usage'    => 'Booking Payment / Usage',
            'admin_adjustment' => 'Admin Adjustment',
            'bank_transfer'    => 'Bank Transfer - Deposit',
            'refund'           => 'Refund',
        ];

        return $labels[$source] ?? ucwords(str_replace(['_', '-'], ' ', $source));
    }
}
