@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/admin-dashboard.css') }}">
@endpush

@section('content')
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
                <strong>24</strong>
                <span class="metric-note">8 awaiting confirmation</span>
            </a>

            <a href="{{ route('customers.index') }}" class="admin-metric-card metric-green">
                <span class="metric-icon"><i class="fas fa-users" aria-hidden="true"></i></span>
                <span class="metric-label">Active Customers</span>
                <strong>1,248</strong>
                <span class="metric-note">36 new this month</span>
            </a>

            <a href="{{ route('wallets.index') }}" class="admin-metric-card metric-amber">
                <span class="metric-icon"><i class="fas fa-wallet" aria-hidden="true"></i></span>
                <span class="metric-label">Wallet Credits</span>
                <strong>$18,420</strong>
                <span class="metric-note">Referral and review rewards</span>
            </a>

            <a href="{{ route('referrals.index') }}" class="admin-metric-card metric-rose">
                <span class="metric-icon"><i class="fas fa-user-friends" aria-hidden="true"></i></span>
                <span class="metric-label">Referral Pipeline</span>
                <strong>73</strong>
                <span class="metric-note">19 pending approval</span>
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
                            <tr>
                                <td><strong>#CL-2048</strong></td>
                                <td>Sarah Ahmed</td>
                                <td>Deep Cleaning</td>
                                <td>Today, 10:30 AM</td>
                                <td><span class="admin-status status-pending">Pending</span></td>
                                <td><span class="admin-payment payment-paid">Paid</span></td>
                            </tr>
                            <tr>
                                <td><strong>#CL-2049</strong></td>
                                <td>Rahim Khan</td>
                                <td>Residential Cleaning</td>
                                <td>Today, 02:00 PM</td>
                                <td><span class="admin-status status-confirmed">Confirmed</span></td>
                                <td><span class="admin-payment payment-due">Due</span></td>
                            </tr>
                            <tr>
                                <td><strong>#CL-2050</strong></td>
                                <td>Nusrat Jahan</td>
                                <td>Office Cleaning</td>
                                <td>Tomorrow, 09:00 AM</td>
                                <td><span class="admin-status status-progress">In Progress</span></td>
                                <td><span class="admin-payment payment-paid">Paid</span></td>
                            </tr>
                            <tr>
                                <td><strong>#CL-2051</strong></td>
                                <td>Tanvir Hasan</td>
                                <td>Move-out Cleaning</td>
                                <td>Aug 24, 11:00 AM</td>
                                <td><span class="admin-status status-cancelled">Cancelled</span></td>
                                <td><span class="admin-payment payment-refund">Refund</span></td>
                            </tr>
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
                    <div class="service-mix-row">
                        <div>
                            <strong>Residential Cleaning</strong>
                            <span>156 bookings</span>
                        </div>
                        <div class="service-progress"><span style="width: 76%"></span></div>
                    </div>
                    <div class="service-mix-row">
                        <div>
                            <strong>Deep Cleaning</strong>
                            <span>98 bookings</span>
                        </div>
                        <div class="service-progress"><span style="width: 58%"></span></div>
                    </div>
                    <div class="service-mix-row">
                        <div>
                            <strong>Office Cleaning</strong>
                            <span>74 bookings</span>
                        </div>
                        <div class="service-progress"><span style="width: 46%"></span></div>
                    </div>
                    <div class="service-mix-row">
                        <div>
                            <strong>Window Cleaning</strong>
                            <span>41 bookings</span>
                        </div>
                        <div class="service-progress"><span style="width: 29%"></span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="admin-dashboard-grid admin-dashboard-grid-secondary">
            <div class="admin-panel operations-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Operations Health</h2>
                        <p>Availability, credits, referrals and customer status.</p>
                    </div>
                </div>

                <div class="operations-grid">
                    <a href="{{ route('weekly-schedule.index') }}" class="operation-item">
                        <span><i class="far fa-clock" aria-hidden="true"></i></span>
                        <strong>42</strong>
                        <small>Open slots this week</small>
                    </a>
                    <a href="{{ route('holidays.index') }}" class="operation-item">
                        <span><i class="fas fa-umbrella-beach" aria-hidden="true"></i></span>
                        <strong>3</strong>
                        <small>Upcoming holidays</small>
                    </a>
                    <a href="{{ route('wallets.index') }}" class="operation-item">
                        <span><i class="fas fa-coins" aria-hidden="true"></i></span>
                        <strong>$2,140</strong>
                        <small>Credits issued this month</small>
                    </a>
                    <a href="{{ route('referrals.index') }}" class="operation-item">
                        <span><i class="fas fa-gift" aria-hidden="true"></i></span>
                        <strong>$875</strong>
                        <small>Referral rewards pending</small>
                    </a>
                </div>
            </div>

            <div class="admin-panel activity-panel">
                <div class="admin-panel-header">
                    <div>
                        <h2>Recent Activity</h2>
                        <p>Latest operational events.</p>
                    </div>
                </div>

                <div class="activity-list">
                    <div class="activity-item">
                        <span class="activity-dot dot-blue"></span>
                        <div>
                            <strong>Booking #CL-2048 submitted</strong>
                            <small>Sarah Ahmed requested Deep Cleaning · 8 min ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot dot-green"></span>
                        <div>
                            <strong>Wallet credit added</strong>
                            <small>$25 welcome credit added for new customer · 24 min ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot dot-amber"></span>
                        <div>
                            <strong>Referral waiting for approval</strong>
                            <small>Referral reward queued after completed booking · 1 hr ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <span class="activity-dot dot-rose"></span>
                        <div>
                            <strong>Payment review required</strong>
                            <small>Manual payment verification pending for #CL-2052 · 2 hrs ago</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
