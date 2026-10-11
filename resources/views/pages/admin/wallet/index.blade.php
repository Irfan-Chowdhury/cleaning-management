@extends('layouts.app')

@section('title', 'Wallets')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/wallet.css') }}">
@endpush

@section('content')
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
                            <th>#SL</th>
                            <th>Name</th>
                            <th>User</th>
                            <th>Total Credit</th>
                            <th>Total Debit</th>
                            <th>Remaining Balance</th>
                            <th class="wallet-action-column">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wallets as $wallet)
                            <tr>
                                <td data-search="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                                <td data-search="{{ $wallet->name }} {{ $wallet->first_name ?? '' }} {{ $wallet->last_name ?? '' }}">
                                    <strong>{{ $wallet->name }}</strong>
                                </td>
                                <td data-search="{{ $wallet->email }} {{ $wallet->phone ?? '' }}">
                                    <span class="text-muted" style="font-size: 12px;">
                                        <i class="fas fa-envelope mr-1" aria-hidden="true"></i>{{ $wallet->email }}
                                    </span>
                                    @if (!empty($wallet->phone))
                                        <br>
                                        <span class="text-muted" style="font-size: 11px;">
                                            <i class="fas fa-phone mr-1" aria-hidden="true"></i>{{ $wallet->phone }}
                                        </span>
                                    @endif
                                </td>
                                <td data-order="{{ $wallet->total_credit }}" data-search="{{ $wallet->total_credit }} {{ number_format($wallet->total_credit, 2) }} {{ number_format($wallet->total_credit, 0) }}">
                                    <span class="wallet-credit-badge">
                                        + {{ number_format($wallet->total_credit, 2) }}
                                    </span>
                                </td>
                                <td data-order="{{ $wallet->total_debit }}" data-search="{{ $wallet->total_debit }} {{ number_format($wallet->total_debit, 2) }} {{ number_format($wallet->total_debit, 0) }}">
                                    <span class="wallet-debit-badge">
                                        - {{ number_format($wallet->total_debit, 2) }}
                                    </span>
                                </td>
                                <td data-order="{{ $wallet->remaining_balance }}" data-search="{{ $wallet->remaining_balance }} {{ number_format($wallet->remaining_balance, 2) }} {{ number_format(abs($wallet->remaining_balance), 2) }}">
                                    <span class="wallet-balance-badge {{ $wallet->remaining_balance < 0 ? 'negative' : '' }}">
                                        {{ $wallet->remaining_balance >= 0 ? '+' : '-' }} {{ number_format(abs($wallet->remaining_balance), 2) }}
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
                        @endforeach
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
                    { targets: [0, 1, 2, 3, 4, 5], searchable: true },
                    { targets: [6], orderable: false, searchable: false }
                ],
                language: {
                    search: 'Search wallets:',
                    lengthMenu: 'Show _MENU_ wallets',
                    emptyTable: 'No wallet records found.',
                    zeroRecords: 'No matching wallets found.',
                }
            });
        });
    </script>
@endpush
