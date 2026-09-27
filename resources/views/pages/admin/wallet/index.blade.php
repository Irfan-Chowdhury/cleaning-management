@extends('layouts.app')

@section('title', 'Wallets')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/wallet.css') }}">
@endpush

@section('content')
    <div class="alert alert-warning d-flex align-items-center mb-4 p-3 shadow-sm rounded-lg" style="background-color: #fff8e6; border: 1px solid #ffe0b2; border-left: 5px solid #ff9800; color: #8c5400;" role="alert">
        <div class="mr-3" style="font-size: 24px;">
            <i class="fas fa-tools text-warning"></i>
        </div>
        <div>
            <h5 class="alert-heading font-weight-bold mb-1" style="font-size: 16px; color: #d97706;">
                Page Under Construction &bull; Coming Soon!
            </h5>
            <p class="mb-0 small" style="color: #92400e;">
                We are actively working on enhancing this dashboard with exciting features. Stay tuned!
            </p>
        </div>
    </div>
    <div class="wallets-page">
        <div class="wallets-header">
            <div>
                <h1>Wallets</h1>
                <p>View credit, debit, and remaining balance for each user.</p>
            </div>
        </div>

        <div class="wallets-table-card">
            <div class="wallets-table-card-header">
                <div>
                    <h2>Wallet List</h2>
                    <p>{{ $wallets->count() }} records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="wallets-table" class="table table-hover table-bordered nowrap wallets-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>User</th>
                            <th>Total Credit</th>
                            <th>Total Debit</th>
                            <th>Remaining Balance</th>
                            <th class="wallet-action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($wallets as $wallet)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $wallet->name }}</strong>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size: 12px;">
                                        <i class="fas fa-envelope mr-1" aria-hidden="true"></i>{{ $wallet->email }}
                                    </span>
                                </td>
                                <td>
                                    <span class="wallet-credit-badge">
                                        + {{ number_format($wallet->total_credit, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="wallet-debit-badge">
                                        - {{ number_format($wallet->total_debit, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="wallet-balance-badge {{ $wallet->remaining_balance < 0 ? 'negative' : '' }}">
                                        {{ $wallet->remaining_balance >= 0 ? '+' : '' }}{{ number_format($wallet->remaining_balance, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="wallet-actions">
                                        <a href="{{ route('wallets.show', $wallet->id) }}"
                                           class="btn btn-sm btn-outline-success wallet-action-btn"
                                           title="View Wallet">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-wallet fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No wallet records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
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
            $('#wallets-table').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [6] }
                ],
                language: {
                    search: 'Search wallets:',
                    lengthMenu: 'Show _MENU_ wallets',
                }
            });
        });
    </script>
@endpush
