@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/admin-dashboard.css') }}?v={{ filemtime(public_path('assets/css/admin-dashboard.css')) }}">
@endpush

@section('content')
    {{-- <div class="alert alert-warning d-flex align-items-center mb-4 p-3 shadow-sm rounded-lg" style="background-color: #fff8e6; border: 1px solid #ffe0b2; border-left: 5px solid #ff9800; color: #8c5400;" role="alert">
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
    </div> --}}

    <div class="admin-dashboard-page">
        <div class="admin-dashboard-header">
            <div>
                <span class="admin-dashboard-kicker">Operations Overview</span>
                <h1>Admin Dashboard</h1>
                <p>Track bookings, customers, credits, referrals, service availability and payment health.</p>
            </div>
            <div class="admin-dashboard-actions">
                <a href="{{ route('booking-service.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-plus" aria-hidden="true"></i> New Booking
                </a>
                <a href="{{ route('bookings.index') }}" class="btn btn-primary">
                    <i class="fas fa-tasks" aria-hidden="true"></i> Manage Bookings
                </a>
            </div>
        </div>

        <section class="admin-metric-grid" aria-label="Admin dashboard metrics">
            <a href="{{ route('bookings.index') }}" class="admin-metric-card metric-blue">
                <span class="metric-icon"><i class="far fa-calendar-check" aria-hidden="true"></i></span>
                <span class="metric-label">Today Bookings</span>
                <strong>{{ $todayBookings['today_count'] ?? 0 }}</strong>
                <span class="metric-note">{{ $todayBookings['approved_count'] ?? 0 }} awaiting confirmation</span>
                <!-- <span class="metric-note">{{ $todayBookings['approved_count'] ?? 0 }} approved</span> -->
            </a>

            <a href="{{ route('customers.index') }}" class="admin-metric-card metric-green">
                <span class="metric-icon"><i class="fas fa-users" aria-hidden="true"></i></span>
                <span class="metric-label">Active Customers</span>
                <strong>{{ number_format($activeCustomers['total_count'] ?? 0) }}</strong>
                <span class="metric-note">{{ number_format($activeCustomers['new_this_month_count'] ?? 0) }} new this month</span>
            </a>

            <a href="{{ route('wallets.index') }}" class="admin-metric-card metric-amber">
                <span class="metric-icon"><i class="fas fa-wallet" aria-hidden="true"></i></span>
                <span class="metric-label">Total Revenue This {{ $totalRevenue['month_name'] ?? now()->format('F') }}</span>
                <strong>${{ number_format($totalRevenue['amount'] ?? 0, 2) }}</strong>
                <span class="metric-note">{{ number_format($totalRevenue['completed_count'] ?? 0) }} completed bookings with paid this month</span>
            </a>

            <a href="{{ route('referrals.index') }}" class="admin-metric-card metric-rose">
                <span class="metric-icon"><i class="fas fa-user-friends" aria-hidden="true"></i></span>
                <span class="metric-label">Referral Signup</span>
                <strong>{{ number_format($referralPipeline['signed_up_count'] ?? 0) }}</strong>
                <span class="metric-note">{{ number_format($referralPipeline['invited_count'] ?? 0) }} Invited</span>
            </a>
        </section>

        <section class="admin-dashboard-grid">
            <div class="admin-panel bookings-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Booking Queue</h2>
                        <p>Static snapshot for today and upcoming service requests.</p>
                    </div>
                    <a href="{{ route('bookings.index') }}">View all</a>
                </div>

                <div class="table-responsive">
                    <table class="table admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookingQueue as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('bookings.show', $booking['id']) }}" class="text-dark font-weight-bold">
                                            {{ $booking['reference'] }}
                                        </a>
                                    </td>
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{{ $booking['service_name'] }}</td>
                                    <td>{{ $booking['schedule'] }}</td>
                                    <td><span class="admin-status {{ $booking['status_badge_class'] }}">{{ $booking['status_label'] }}</span></td>
                                    <td><span class="admin-payment {{ $booking['payment_badge_class'] }}">{{ $booking['payment_label'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No bookings found in the queue.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-panel service-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Service Mix</h2>
                        <p>Most requested cleaning services.</p>
                    </div>
                    <a href="{{ route('services.index') }}">Manage</a>
                </div>

                <div class="service-mix-list">
                    @forelse ($serviceMix as $service)
                        <div class="service-mix-row">
                            <div>
                                <strong>{{ $service['name'] }}</strong>
                                <span>{{ $service['label'] }}</span>
                            </div>
                            <div class="service-progress"><span style="width: {{ $service['percentage'] }}%"></span></div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No active services found.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="admin-dashboard-grid admin-dashboard-grid-secondary">
            <div class="admin-panel operations-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Operations Overview</h2>
                        <p>Availability, credits, referrals and customer status.</p>
                    </div>
                </div>

                <div class="operations-grid">
                    <a href="{{ route('weekly-schedule.index') }}" class="operation-item">
                        <span><i class="far fa-clock" aria-hidden="true"></i></span>
                        <strong>{{ number_format($openSlotsThisWeek ?? 0) }}</strong>
                        <small>Open slots this week</small>
                    </a>
                    <a href="{{ route('holidays.index') }}" class="operation-item">
                        <span><i class="fas fa-umbrella-beach" aria-hidden="true"></i></span>
                        <strong>{{ number_format($totalHolidaysCurrentMonth ?? 0) }}</strong>
                        <small>Total Holidays of this Month</small>
                    </a>
                    <a href="{{ route('wallets.index') }}" class="operation-item">
                        <div class="d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-coins" aria-hidden="true"></i></span>
                            <i class="fas fa-exclamation-circle stat-tooltip-icon"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="Total sum of Welcome Bonuses, Referral Rewards, Google Review Rewards of this {{ now()->format('F') }}"
                               aria-hidden="true"></i>
                        </div>
                        <strong>${{ number_format($creditsIssuedThisMonth ?? 0, 2) }}</strong>
                        <small>Credits issued this month</small>
                    </a>
                    <a href="{{ route('wallets.index') }}" class="operation-item">
                        <span><i class="fas fa-gift" aria-hidden="true"></i></span>
                        <strong>${{ number_format($welcomeBonusThisMonth ?? 0, 2) }}</strong>
                        <small>Welcome Bonus this month</small>
                    </a>
                    <a href="{{ route('referrals.index') }}" class="operation-item">
                        <span><i class="fas fa-user-friends" aria-hidden="true"></i></span>
                        <strong>${{ number_format($referralBonusThisMonth ?? 0, 2) }}</strong>
                        <small>Referral Bonus this month</small>
                    </a>
                    <a href="{{ route('reviews.index') }}" class="operation-item">
                        <span><i class="fab fa-google" aria-hidden="true"></i></span>
                        <strong>${{ number_format($googleRewardsThisMonth ?? 0, 2) }}</strong>
                        <small>Google Rewards this month</small>
                    </a>
                </div>
            </div>

            <!-- <div class="admin-panel activity-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Recent Audit Logs</h2>
                        <p>Latest operational events.</p>
                    </div>
                </div>

                <div class="activity-list">
                    @forelse ($recentActivities as $activity)
                        <div class="activity-item">
                            <span class="activity-dot {{ $activity['dot_class'] }}"></span>
                            <div>
                                <strong>{{ $activity['title'] }}</strong>
                                <small>{{ $activity['description'] }} · {{ $activity['created_at_human'] }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small py-3 mb-0">No recent activity recorded.</p>
                    @endforelse
                </div>
            </div> -->
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
