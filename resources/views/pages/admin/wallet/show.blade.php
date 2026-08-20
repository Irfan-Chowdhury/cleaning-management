@extends('layouts.app')

@section('title', 'Wallet Details - ' . $user->name)

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
            <!-- 1st Section: User Details (Label Left, Value Right) -->
            <div class="col-lg-6 mb-3 mb-lg-0">
                <div class="wallet-user-card h-100">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <img src="{{ $user->photo }}" alt="{{ $user->name }}" class="wallet-user-avatar rounded-circle mr-3">
                        <div>
                            <h4 class="mb-0 font-weight-bold text-dark">{{ $user->name }}</h4>
                            <span class="badge badge-light border text-muted px-2 py-1 mt-1" style="font-size: 11px;">User Profile</span>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless user-info-table mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2" style="width: 30%;">
                                    <i class="fas fa-envelope mr-1 text-primary"></i> Email
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-phone mr-1 text-primary"></i> Phone
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $user->phone }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold align-middle py-2">
                                    <i class="fas fa-venus-mars mr-1 text-primary"></i> Gender
                                </td>
                                <td class="font-weight-semibold text-dark align-middle py-2">{{ $user->gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2nd Section: Financial Summary Table (Credit, Debit, Remaining on Left Column, Value on Right Column) -->
            <div class="col-lg-6">
                <div class="wallet-user-card h-100">
                    <div class="wallet-summary-header mb-3 pb-2 border-bottom">
                        <h5 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-wallet mr-2 text-primary"></i> Wallet Summary
                        </h5>
                    </div>

                    @php 
                        $totalCredit = $transactions->sum('credit');
                        $totalDebit = $transactions->sum('debit');
                        $remaining = $totalCredit - $totalDebit;
                    @endphp

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
                                        + ${{ number_format($totalCredit, 2) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">
                                    <i class="fas fa-arrow-up mr-2 text-danger"></i> Debit
                                </td>
                                <td class="text-right align-middle">
                                    <span class="wallet-debit-badge">
                                        - ${{ number_format($totalDebit, 2) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-dark align-middle">
                                    <i class="fas fa-coins mr-2 text-info"></i> Remaining
                                </td>
                                <td class="text-right align-middle">
                                    <span class="wallet-balance-badge {{ $remaining < 0 ? 'negative' : '' }}">
                                        {{ $remaining >= 0 ? '+' : '' }}${{ number_format($remaining, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

                       

        <!-- Transactions Table Card -->
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
                            <th class="wallet-action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>
                                    <span class="wallet-credit-badge">
                                        + ${{ number_format($transaction->credit, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="wallet-debit-badge">
                                        - ${{ number_format($transaction->debit, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $transaction->source }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 13px;">
                                        <i class="far fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="wallet-actions">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary wallet-action-btn" title="Edit Transaction">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger wallet-action-btn" title="Delete Transaction">
                                            <i class="fas fa-trash-alt" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-receipt fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Credit: <span class="wallet-credit-badge">+ ${{ number_format($transactions->sum('credit'), 2) }}</span></th>
                            <th>Total Debit: <span class="wallet-debit-badge">- ${{ number_format($transactions->sum('debit'), 2) }}</span></th>
                            <th colspan="3"></th>
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
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [4] }
                ],
                language: {
                    search: 'Search transactions:',
                    lengthMenu: 'Show _MENU_ entries',
                }
            });
        });
    </script>
@endpush
