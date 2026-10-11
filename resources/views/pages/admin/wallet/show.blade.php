@extends('layouts.app')

@section('title', 'Wallet Details - ' . ($customer->name ?? $user->name ?? 'User'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/wallet.css') }}">
@endpush

@section('content')
    <div class="wallets-page">
        <!-- Page Header -->
        <div class="wallets-header">
            <div>
                <a href="{{ route('wallets.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Wallets
                </a>
                <h1>Wallet Details</h1>
                <p>View user profile information and transaction history.</p>
            </div>
        </div>

        <!-- Profile & Wallet Summary Cards -->
        <div class="row mb-4">
            <!-- 1st Section: Customer Details -->
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="wallet-user-card h-100">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <img src="{{ $customer->photo ?? $user->photo }}" alt="{{ $customer->name ?? $user->name }}" class="wallet-user-avatar rounded-circle mr-3">
                        <div>
                            <h4 class="mb-0 font-weight-bold text-dark">{{ $customer->name ?? $user->name }}</h4>
                            <span class="badge badge-light border text-muted px-2 py-1 mt-1" style="font-size: 11px;">User Profile</span>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless user-info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2" style="width: 30%;">
                                    <i class="fas fa-envelope mr-1 text-primary"></i> Email
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $customer->email ?? $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-phone mr-1 text-primary"></i> Phone
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $customer->phone ?? $user->phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-venus-mars mr-1 text-primary"></i> Gender
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $customer->gender ?? $user->gender }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-map-marker-alt mr-1 text-primary"></i> Address
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $customer->address ?? $user->address }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2nd Section: Financial Summary Table -->
            <div class="col-lg-6">
                <div class="wallet-user-card h-100">
                    <div class="wallet-summary-header mb-3 pb-2 border-bottom">
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-wallet mr-2 text-primary"></i> Wallet Summary
                        </h5>
                    </div>

                    <table class="table table-bordered wallet-summary-table mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="font-weight-bold text-dark">Type</th>
                                <th class="font-weight-bold text-dark text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">
                                    <i class="fas fa-arrow-down mr-2 text-success"></i> Credit
                                </td>
                                <td class="text-right align-middle">
                                    <span class="wallet-credit-badge">
                                        + ${{ number_format($walletSummary->total_credit ?? 0, 2) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">
                                    <i class="fas fa-arrow-up mr-2 text-danger"></i> Debit
                                </td>
                                <td class="text-right align-middle">
                                    <span class="wallet-debit-badge">
                                        - ${{ number_format($walletSummary->total_debit ?? 0, 2) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">
                                    <i class="fas fa-coins mr-2 text-info"></i> Remaining
                                </td>
                                <td class="text-right align-middle">
                                    @php
                                        $remaining = $walletSummary->remaining_balance ?? 0;
                                    @endphp
                                    <span class="wallet-balance-badge {{ $remaining < 0 ? 'negative' : '' }}">
                                        {{ $remaining >= 0 ? '+' : '-' }} ${{ number_format(abs($remaining), 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3rd Section: Transactions Table Card -->
        <div class="wallets-table-card">
            <div class="wallets-table-card-header">
                <div>
                    <h2>Transaction History</h2>
                    <p>{{ $transactions->count() }} transactions recorded</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="wallet-transactions-table" class="table table-hover table-bordered nowrap wallets-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Source</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    @if ($transaction->credit > 0)
                                        <span class="wallet-credit-badge">
                                            + ${{ number_format($transaction->credit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction->debit > 0)
                                        <span class="wallet-debit-badge">
                                            - ${{ number_format($transaction->debit, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $transaction->source }}</strong>
                                    @if (!empty($transaction->description))
                                        <div class="text-muted small mt-1">{{ $transaction->description }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 13px;">
                                        <i class="far fa-clock mr-1"></i>{{ $transaction->created_at ? \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') : 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Credit: <span class="wallet-credit-badge">+ ${{ number_format($walletSummary->total_credit ?? 0, 2) }}</span></th>
                            <th>Total Debit: <span class="wallet-debit-badge">- ${{ number_format($walletSummary->total_debit ?? 0, 2) }}</span></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#wallet-transactions-table').DataTable({
                responsive: true,
                searching: false,
                order: [[3, 'desc']],
                language: {
                    lengthMenu: 'Show _MENU_ entries',
                    emptyTable: 'No transactions found.',
                    zeroRecords: 'No transactions found.',
                }
            });
        });
    </script>
@endpush
