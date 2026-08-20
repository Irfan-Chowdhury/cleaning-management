@extends('layouts.app')

@section('title', 'Referrals')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/css/customer.css') }}">
@endpush

@section('content')
    <div class="customers-page">
        <!-- Header -->
        <div class="customers-header">
            <div>
                <h1>Referrals</h1>
                <p>Track customer referrals, rewards, and associated bookings.</p>
            </div>
        </div>

        <!-- Referrals DataTable Card -->
        <div class="customers-table-card">
            <div class="customers-table-card-header">
                <div>
                    <h2>Referral List</h2>
                    <p>{{ $referrals->count() }} records found</p>
                </div>
            </div>

            <div class="table-responsive">
                <table id="referrals-table" class="table table-hover table-bordered nowrap customers-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Referrer Name</th>
                            <th>Referred Name</th>
                            <th>Referral Code</th>
                            <th>Reward Amount</th>
                            <th>Created At</th>
                            <th>Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($referrals as $referral)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="customer-avatar-cell">
                                        <img src="{{ $referral->referrer_avatar }}" alt="{{ $referral->referrer_name }}" class="customer-avatar">
                                        <div class="customer-info-meta">
                                            <span class="customer-name">{{ $referral->referrer_name }}</span>
                                            <span class="customer-email">{{ $referral->referrer_email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="customer-avatar-cell">
                                        <img src="{{ $referral->referred_avatar }}" alt="{{ $referral->referred_name }}" class="customer-avatar">
                                        <div class="customer-info-meta">
                                            <span class="customer-name">{{ $referral->referred_name }}</span>
                                            <span class="customer-email">{{ $referral->referred_email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light border px-2 py-1" style="font-size: 12px; font-weight: 600; color: #0866e8; letter-spacing: 0.5px;">
                                        <i class="fas fa-ticket-alt mr-1"></i>{{ $referral->referral_code }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">${{ number_format($referral->reward_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="text-dark">
                                        <i class="far fa-calendar-alt mr-1 text-primary"></i>{{ \Carbon\Carbon::parse($referral->created_at)->format('M d, Y h:i A') }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $referral->booking_service }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-user-friends fa-2x mb-2 d-block" aria-hidden="true"></i>
                                    No referrals found.
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
            $('#referrals-table').DataTable({
                responsive: true,
                order: [[5, 'desc']],
                language: {
                    search: 'Search referrals:',
                    lengthMenu: 'Show _MENU_ entries',
                }
            });
        });
    </script>
@endpush
