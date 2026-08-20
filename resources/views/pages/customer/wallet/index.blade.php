@extends('layouts.app')

@section('title', 'My Wallet')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .wallet-summary-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
            transition: all 0.2s ease-in-out;
        }
        .wallet-summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.05);
        }
        .wallet-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .wallet-icon-credit {
            background-color: #e6f4ea;
            color: #1e7e34;
        }
        .wallet-icon-debit {
            background-color: #fce8e6;
            color: #c5221f;
        }
        .wallet-icon-balance {
            background-color: #e8f4fd;
            color: #0866e8;
        }
        .filter-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
        }
        .filter-controls-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .filter-left-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-item label {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #475467;
            white-space: nowrap;
        }
        .filter-item .form-control {
            height: 38px;
            font-size: 13px;
            border-color: #d0d5dd;
            border-radius: 8px;
        }
        .filter-item .form-control:focus {
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }
        .btn-filter-apply {
            background-color: #0866e8;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            height: 38px;
            padding: 0 18px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-filter-apply:hover {
            background-color: #006ce5;
            color: #ffffff;
        }
        .btn-filter-reset {
            background-color: #f2f4f7;
            color: #344054;
            font-weight: 600;
            font-size: 13px;
            height: 38px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-filter-reset:hover {
            background-color: #eaecf0;
            color: #1d2939;
        }
        .badge-type-credit {
            background-color: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #b7e1cd;
        }
        .badge-type-debit {
            background-color: #fce8e6;
            color: #c5221f;
            border: 1px solid #fad2cf;
        }
        .booking-id-tag {
            font-family: monospace;
            font-weight: 700;
            font-size: 12.5px;
            color: #0866e8;
            background: #f0f6fe;
            padding: 3px 8px;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <h1>My Wallet</h1>
                <p>Track your wallet balance, earned bonuses, and service credits.</p>
            </div>
        </div>

        <!-- Balance Summary Cards Section (Wireframe Line 408-419) -->
        <div class="row mb-4">
            <!-- Total Credit -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="wallet-summary-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Total Credit</span>
                        <h3 class="font-weight-bold mb-0 text-success">${{ number_format($totalCredit, 2) }}</h3>
                    </div>
                    <div class="wallet-icon-box wallet-icon-credit">
                        <i class="fas fa-arrow-down" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Total Debit -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="wallet-summary-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Total Debit</span>
                        <h3 class="font-weight-bold mb-0 text-danger">${{ number_format($totalDebit, 2) }}</h3>
                    </div>
                    <div class="wallet-icon-box wallet-icon-debit">
                        <i class="fas fa-arrow-up" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Available Balance -->
            <div class="col-md-4">
                <div class="wallet-summary-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Available Balance</span>
                        <h3 class="font-weight-bold mb-0 text-primary">${{ number_format($availableBalance, 2) }}</h3>
                    </div>
                    <div class="wallet-icon-box wallet-icon-balance">
                        <i class="fas fa-wallet" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card (Wireframe Line 424) -->
        <div class="filter-card">
            <div class="filter-controls-wrapper">
                <div class="filter-left-group">
                    <!-- Type Filter -->
                    <div class="filter-item">
                        <label for="filter-type"><i class="fas fa-filter text-muted mr-1"></i> Type</label>
                        <select id="filter-type" class="form-control">
                            <option value="all">All</option>
                            <option value="credit">Credit</option>
                            <option value="debit">Debit</option>
                        </select>
                    </div>

                    <!-- Source Filter -->
                    <div class="filter-item">
                        <label for="filter-source"><i class="fas fa-layer-group text-muted mr-1"></i> Source</label>
                        <select id="filter-source" class="form-control">
                            <option value="all">All</option>
                            <option value="welcome_bonus">Welcome Bonus</option>
                            <option value="referral_bonus">Referral Bonus</option>
                            <option value="review_bonus">Google Review Reward</option>
                            <option value="admin_adjustment">Adjustment</option>
                            <option value="booking_usage">Booking Credit Used</option>
                        </select>
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <button type="button" id="btn-apply-filter" class="btn btn-filter-apply">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <button type="button" id="btn-reset-filter" class="btn btn-filter-reset">
                        <i class="fas fa-redo-alt"></i> Reset
                    </button>
                </div>

                <!-- Custom Search Bar -->
                <div class="filter-item">
                    <label for="custom-search-input"><i class="fas fa-search text-muted mr-1"></i> Search</label>
                    <input type="text" id="custom-search-input" class="form-control" placeholder="Search transactions...">
                </div>
            </div>
        </div>

        <!-- Wallet Transactions Datatable Card (Wireframe Line 422-430) -->
        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Wallet Transactions</h2>
                    <p>{{ count($transactions) }} transaction history entries</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="wallet-transactions-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Description</th>
                            <th>Booking ID</th>
                            <th>Credit</th>
                            <th>Debit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $item)
                            @php
                                $typeLower = strtolower($item->type);
                                $sourceKey = strtolower($item->source);

                                // Mapping Source Key to Display Label (Wireframe Lines 433-442)
                                $sourceLabels = [
                                    'welcome_bonus'    => 'Welcome Bonus',
                                    'referral_bonus'   => 'Referral Bonus',
                                    'review_bonus'     => 'Google Review Reward',
                                    'admin_adjustment' => 'Adjustment',
                                    'booking_usage'    => 'Booking Credit Used',
                                ];

                                $displaySource = $sourceLabels[$sourceKey] ?? ucfirst(str_replace('_', ' ', $sourceKey));
                                $typeBadgeClass = $typeLower === 'credit' ? 'badge-type-credit' : 'badge-type-debit';
                            @endphp
                            <tr data-type="{{ $typeLower }}" data-source="{{ $sourceKey }}">
                                <td>
                                    <span class="text-dark font-weight-600">
                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>{{ \Carbon\Carbon::parse($item->date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $typeBadgeClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-weight-600 text-dark">{{ $displaySource }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ $item->description }}</span>
                                </td>
                                <td>
                                    @if ($item->booking_id)
                                        <span class="booking-id-tag">{{ $item->booking_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->credit > 0)
                                        <strong class="text-success">+${{ number_format($item->credit, 2) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->debit > 0)
                                        <strong class="text-danger">-${{ number_format($item->debit, 2) }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-wallet fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No wallet transactions found.
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
            // Register Custom DataTables Search Extension
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var selectedType = $('#filter-type').val().toLowerCase();
                    var selectedSource = $('#filter-source').val().toLowerCase();

                    var rowNode = settings.aoData[dataIndex].nTr;
                    var rowType = $(rowNode).attr('data-type') || '';
                    var rowSource = $(rowNode).attr('data-source') || '';

                    if (selectedType && selectedType !== 'all' && rowType !== selectedType) {
                        return false;
                    }

                    if (selectedSource && selectedSource !== 'all' && rowSource !== selectedSource) {
                        return false;
                    }

                    return true;
                }
            );

            // Initialize DataTable
            var table = $('#wallet-transactions-table').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                dom: "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    zeroRecords: 'No matching wallet transactions found.',
                    info: 'Showing _START_ to _END_ of _TOTAL_ transactions',
                    infoEmpty: 'Showing 0 to 0 of 0 transactions',
                    infoFiltered: '(filtered from _MAX_ total transactions)',
                }
            });

            // Connect Custom Search Input
            $('#custom-search-input').on('keyup change clear', function () {
                table.search($(this).val()).draw();
            });

            // Apply Filter Event
            $('#btn-apply-filter').on('click', function () {
                table.draw();
            });

            // Reset Filter Event
            $('#btn-reset-filter').on('click', function () {
                $('#filter-type').val('all');
                $('#filter-source').val('all');
                $('#custom-search-input').val('');
                table.search('').draw();
            });
        });
    </script>
@endpush
