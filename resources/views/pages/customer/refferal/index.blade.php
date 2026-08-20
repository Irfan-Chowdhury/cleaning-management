@extends('layouts.app')

@section('title', 'My Referral Program')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
    <style>
        .referral-program-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 18px rgba(19, 33, 60, 0.03);
        }
        .referral-input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .referral-input-group .form-control {
            height: 46px;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
        }
        .referral-input-group .form-control:focus {
            background-color: #ffffff;
            border-color: #0866e8;
            box-shadow: 0 0 0 0.2rem rgba(8, 102, 232, 0.14);
        }
        .referral-metric-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(19, 33, 60, 0.025);
            transition: all 0.2s ease-in-out;
        }
        .referral-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(19, 33, 60, 0.05);
        }
        .referral-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .referral-icon-total {
            background-color: #e8f4fd;
            color: #0866e8;
        }
        .referral-icon-pending {
            background-color: #fff8e6;
            color: #b7791f;
        }
        .referral-icon-reward {
            background-color: #e6f4ea;
            color: #1e7e34;
        }
        .badge-status-rewarded {
            background-color: #e6f4ea;
            color: #1e7e34;
            border: 1px solid #b7e1cd;
        }
        .badge-status-approved {
            background-color: #e8f4fd;
            color: #1a73e8;
            border: 1px solid #c2e0ff;
        }
        .badge-status-pending {
            background-color: #fff8e6;
            color: #b7791f;
            border: 1px solid #fce8b2;
        }
        .badge-status-registered {
            background-color: #f1f5f9;
            color: #475467;
            border: 1px solid #cbd5e1;
        }
        .badge-status-rejected {
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
        .btn-copy {
            height: 46px;
            padding: 0 20px;
            font-weight: 600;
            font-size: 13.5px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="customers-page">
        <!-- Page Header -->
        <div class="customers-header mb-4">
            <div>
                <h1>My Referral Program</h1>
                <p>Invite friends and family to Clean &amp; Manage. Earn rewards on every completed booking!</p>
            </div>
        </div>

        <!-- Referral Link & Referral Code Cards Section (Wireframe Line 454-459) -->
        <div class="referral-program-card">
            <div class="row">
                <!-- Referral Code Box -->
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <label for="referral-code-input" class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                        <i class="fas fa-ticket-alt text-primary mr-1"></i> Your Referral Code
                    </label>
                    <div class="input-group referral-input-group">
                        <input type="text" id="referral-code-input" class="form-control" value="{{ $referralCode }}" readonly>
                        <div class="input-group-append">
                            <button type="button" id="btn-copy-code" class="btn btn-primary btn-copy" title="Copy Referral Code">
                                <i class="far fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Referral Link Box -->
                <div class="col-lg-7">
                    <label for="referral-link-input" class="font-weight-bold text-dark mb-2" style="font-size: 14px;">
                        <i class="fas fa-link text-primary mr-1"></i> Your Referral Link
                    </label>
                    <div class="input-group referral-input-group">
                        <input type="text" id="referral-link-input" class="form-control" value="{{ $referralLink }}" readonly>
                        <div class="input-group-append">
                            <button type="button" id="btn-copy-link" class="btn btn-primary btn-copy" title="Copy Referral Link">
                                <i class="far fa-copy"></i> Copy
                            </button>
                            <button type="button" id="btn-share-link" class="btn btn-outline-primary btn-copy ml-2" title="Share Link">
                                <i class="fas fa-share-alt"></i> Share
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Metric Cards (Wireframe Line 460-462) -->
        <div class="row mb-4">
            <!-- Total Referrals -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="referral-metric-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Total Referrals</span>
                        <h3 class="font-weight-bold mb-0 text-dark">{{ $totalReferrals }}</h3>
                    </div>
                    <div class="referral-icon-box referral-icon-total">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Referrals -->
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="referral-metric-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Pending Referrals</span>
                        <h3 class="font-weight-bold mb-0 text-warning">{{ $pendingReferrals }}</h3>
                    </div>
                    <div class="referral-icon-box referral-icon-pending">
                        <i class="fas fa-user-clock" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Total Rewards -->
            <div class="col-md-4">
                <div class="referral-metric-card d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase font-weight-bold d-block mb-1" style="font-size: 12px; letter-spacing: 0.5px;">Total Rewards</span>
                        <h3 class="font-weight-bold mb-0 text-success">${{ number_format($totalRewards, 2) }}</h3>
                    </div>
                    <div class="referral-icon-box referral-icon-reward">
                        <i class="fas fa-gift" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Referral History Datatable Card (Wireframe Line 464-471) -->
        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Referral History</h2>
                    <p>{{ count($referrals) }} invited friends and reward statuses</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="referrals-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Referred Customer</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th>Booking</th>
                            <th>Reward Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($referrals as $item)
                            @php
                                $statusLower = strtolower($item->status);
                                if ($statusLower === 'rewarded') {
                                    $statusBadgeClass = 'badge-status-rewarded';
                                } elseif ($statusLower === 'approved') {
                                    $statusBadgeClass = 'badge-status-approved';
                                } elseif ($statusLower === 'pending') {
                                    $statusBadgeClass = 'badge-status-pending';
                                } elseif ($statusLower === 'rejected') {
                                    $statusBadgeClass = 'badge-status-rejected';
                                } else {
                                    $statusBadgeClass = 'badge-status-registered';
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="customer-avatar-cell">
                                        <img src="{{ $item->customer_avatar }}" alt="{{ $item->customer_name }}" class="customer-avatar">
                                        <span class="customer-name font-weight-600">{{ $item->customer_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark font-weight-600">
                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>{{ \Carbon\Carbon::parse($item->joined_date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadgeClass }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->booking_id)
                                        <span class="booking-id-tag">{{ $item->booking_id }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->reward_amount > 0)
                                        <strong class="text-success">+${{ number_format($item->reward_amount, 2) }}</strong>
                                    @else
                                        <span class="text-muted">$0.00</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-user-friends fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No referrals recorded yet.
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
            // Initialize DataTable
            $('#referrals-table').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    search: 'Search referrals:',
                    lengthMenu: 'Show _MENU_ entries',
                }
            });

            // Copy Helper
            function copyToClipboard(elementId, successMessage) {
                var input = document.getElementById(elementId);
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(function () {
                    alert(successMessage);
                }).catch(function () {
                    document.execCommand('copy');
                    alert(successMessage);
                });
            }

            // Copy Code Event
            $('#btn-copy-code').on('click', function () {
                copyToClipboard('referral-code-input', 'Referral code copied to clipboard!');
            });

            // Copy Link Event
            $('#btn-copy-link').on('click', function () {
                copyToClipboard('referral-link-input', 'Referral link copied to clipboard!');
            });

            // Share Link Event
            $('#btn-share-link').on('click', function () {
                var link = $('#referral-link-input').val();
                if (navigator.share) {
                    navigator.share({
                        title: 'Join Clean & Manage',
                        text: 'Use my referral link to get cleaning service discounts!',
                        url: link
                    }).catch(function (err) {
                        console.log('Share canceled', err);
                    });
                } else {
                    copyToClipboard('referral-link-input', 'Referral link copied to clipboard for sharing!');
                }
            });
        });
    </script>
@endpush
