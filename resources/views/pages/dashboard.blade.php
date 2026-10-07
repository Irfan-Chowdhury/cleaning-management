@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/dashboard.css') }}">
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

    <div class="customer-dashboard">
        <section class="dashboard-section dashboard-section-top">
            @if ($nextCleaning)
                <div class="dashboard-card next-cleaning-card">
                    <div class="dashboard-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <h2 class="section-title mb-0">Next Cleaning</h2>
                            <span class="ml-2 font-weight-bold" style="font-family: monospace; font-size: 13px; color: #2563eb; background: #eff6ff; padding: 3px 10px; border-radius: 6px; border: 1px solid #dbeafe;">
                                {{ $nextCleaning->booking_id_formatted }}
                            </span>
                        </div>
                        <span class="badge {{ $nextCleaning->status_badge_class }}" style="padding: 6px 12px; font-weight: 700; border-radius: 999px;">
                            {{ $nextCleaning->status_label }}
                        </span>
                    </div>

                    <div class="next-cleaning-body">
                        <img src="{{ $nextCleaning->image_url }}" alt="{{ $nextCleaning->service_name }}" class="next-cleaning-image">

                        <div class="next-cleaning-details">
                            <h3>{{ $nextCleaning->service_name }}</h3>

                            <ul class="cleaning-meta">
                                <li><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $nextCleaning->booking_date_formatted }}</li>
                                <li><i class="far fa-clock" aria-hidden="true"></i> {{ $nextCleaning->time_slot_formatted }}</li>
                                <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> {{ $nextCleaning->full_address }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="next-cleaning-actions">
                        <a href="{{ $nextCleaning->details_url }}" class="btn btn-primary btn-sm dashboard-action-btn">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </a>
                        @if ($nextCleaning->is_approved)
                            <a href="{{ $nextCleaning->step4_url }}" class="btn btn-success btn-sm dashboard-action-btn">
                                <i class="fas fa-calendar-check mr-1"></i> Proceed to Step 4
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="dashboard-card next-cleaning-card p-4 text-center">
                    <div class="py-3">
                        <i class="far fa-calendar-check text-primary mb-3" style="font-size: 38px;"></i>
                        <h3 class="h6 font-weight-bold text-dark mb-1">No Upcoming Cleanings</h3>
                        <p class="text-muted small mb-3">You don't have any active cleaning sessions scheduled right now.</p>
                        <a href="{{ route('booking-service.create') }}" class="btn btn-primary btn-sm px-3" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> Book a Cleaning
                        </a>
                    </div>
                </div>
            @endif

            <div class="dashboard-stats-grid">
                <!-- Card 1: Upcoming Cleanings -->
                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-blue"><i class="far fa-calendar-alt" aria-hidden="true"></i></div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="section-title mb-0">Upcoming Cleanings</h2>
                            <i class="fas fa-exclamation-circle stat-tooltip-icon" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="{{ $stats->upcoming_tooltip }}" 
                               aria-hidden="true"></i>
                        </div>
                        <div class="stat-value">{{ $stats->upcoming_count }}</div>
                        <p>Bookings</p>
                        <a href="{{ $stats->upcoming_url }}">View all bookings</a>
                    </div>
                </div>

                <!-- Card 2: Total Completed Bookings -->
                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-green"><i class="fas fa-check-double" aria-hidden="true"></i></div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="section-title mb-0">Total Completed Bookings</h2>
                            <i class="fas fa-exclamation-circle stat-tooltip-icon" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="{{ $stats->completed_tooltip }}" 
                               aria-hidden="true"></i>
                        </div>
                        <div class="stat-value">{{ $stats->completed_count }}</div>
                        <p>Completed Bookings</p>
                        <a href="{{ $stats->completed_url }}">View history</a>
                    </div>
                </div>

                <!-- Card 3: Remaining Credits -->
                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-yellow"><i class="far fa-star" aria-hidden="true"></i></div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="section-title mb-0">Remaining Credits</h2>
                            <i class="fas fa-exclamation-circle stat-tooltip-icon" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="{{ $stats->remaining_credits_tooltip }}" 
                               aria-hidden="true"></i>
                        </div>
                        <div class="stat-value">{{ $stats->remaining_credits_formatted }}</div>
                        <p>Available Credits</p>
                        <a href="{{ $stats->remaining_credits_url }}">View details</a>
                    </div>
                </div>

                <!-- Card 4: Total Spent -->
                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-purple"><i class="fas fa-wallet" aria-hidden="true"></i></div>
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <h2 class="section-title mb-0">Total Spent</h2>
                            <i class="fas fa-exclamation-circle stat-tooltip-icon" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="{{ $stats->total_spent_tooltip }}" 
                               aria-hidden="true"></i>
                        </div>
                        <div class="stat-value">{{ $stats->total_spent_formatted }}</div>
                        <p>Total Spent</p>
                        <a href="{{ $stats->total_spent_url }}">View invoices</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-section dashboard-section-middle">
            <div class="dashboard-card recent-bookings-card">
                <div class="dashboard-card-header">
                    <h2 class="section-title">Recent Bookings</h2>
                    <a href="#" class="card-link">View all</a>
                </div>

                <div class="booking-row">
                    <img src="https://picsum.photos/seed/cleaning2/120/90" alt="House cleaning" class="booking-thumb">
                    <div class="booking-info">
                        <h3>House Cleaning</h3>
                        <p>1 Aug 2025 &bull; 09:00 AM</p>
                        <span class="booking-price">$180.00</span>
                    </div>
                    <div class="booking-status status-completed"><i class="fas fa-check-circle" aria-hidden="true"></i> Completed</div>
                    <div class="booking-actions">
                        <a href="#" class="btn btn-outline-primary btn-sm booking-btn">Invoice</a>
                        <a href="#" class="btn btn-primary btn-sm booking-btn">Book Again</a>
                    </div>
                </div>

                <div class="booking-row">
                    <img src="https://picsum.photos/seed/cleaning3/120/90" alt="Deep cleaning" class="booking-thumb">
                    <div class="booking-info">
                        <h3>Deep Cleaning</h3>
                        <p>15 Jul 2025 &bull; 09:00 AM</p>
                        <span class="booking-price">$260.00</span>
                    </div>
                    <div class="booking-status status-completed"><i class="fas fa-check-circle" aria-hidden="true"></i> Completed</div>
                    <div class="booking-actions">
                        <a href="#" class="btn btn-outline-primary btn-sm booking-btn">Invoice</a>
                        <a href="#" class="btn btn-primary btn-sm booking-btn">Book Again</a>
                    </div>
                </div>

                <div class="booking-row">
                    <img src="https://picsum.photos/seed/cleaning4/120/90" alt="End of lease cleaning" class="booking-thumb">
                    <div class="booking-info">
                        <h3>End of Lease Cleaning</h3>
                        <p>2 Jul 2025 &bull; 09:00 AM</p>
                        <span class="booking-price">$320.00</span>
                    </div>
                    <div class="booking-status status-cancelled"><i class="fas fa-times-circle" aria-hidden="true"></i> Cancelled</div>
                    <div class="booking-actions">
                        <a href="#" class="btn btn-outline-primary btn-sm booking-btn">Invoice</a>
                        <a href="#" class="btn btn-primary btn-sm booking-btn">Book Again</a>
                    </div>
                </div>
            </div>

            <div class="dashboard-card quick-book-card">
                <h2 class="section-title">Quick Book Again</h2>

                <a href="#" class="service-row service-blue">
                    <span class="service-icon"><i class="fas fa-home" aria-hidden="true"></i></span>
                    <span>Regular Home Cleaning</span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="#" class="service-row service-green">
                    <span class="service-icon"><i class="fas fa-broom" aria-hidden="true"></i></span>
                    <span>Deep Cleaning</span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="#" class="service-row service-purple">
                    <span class="service-icon"><i class="fas fa-key" aria-hidden="true"></i></span>
                    <span>End of Lease Cleaning</span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="#" class="service-row service-orange">
                    <span class="service-icon"><i class="fas fa-building" aria-hidden="true"></i></span>
                    <span>Office Cleaning</span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
                <a href="#" class="service-row service-cyan">
                    <span class="service-icon"><i class="far fa-window-maximize" aria-hidden="true"></i></span>
                    <span>Window Cleaning</span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="dashboard-card rewards-card">
                <div class="dashboard-card-header">
                    <h2 class="section-title">Credits &amp; Rewards</h2>
                    <a href="#" class="card-link">View details</a>
                </div>

                <div class="credit-summary">
                    <div>
                        <span>Available Credits</span>
                        <strong class="credit-green">$45.00</strong>
                    </div>
                    <div class="credit-separator"></div>
                    <div>
                        <span>Pending Credits</span>
                        <strong>$26.00</strong>
                    </div>
                </div>

                <div class="member-row">
                    <span class="member-icon"><i class="far fa-star" aria-hidden="true"></i></span>
                    <div>
                        <h3>Silver Member</h3>
                        <p>You're $80 away from Gold</p>
                    </div>
                </div>

                <div class="reward-progress">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="49" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="reward-footer">
                    <span>15 Cleanings Completed</span>
                    <strong><i class="fas fa-gift" aria-hidden="true"></i> Next Reward: 10% OFF</strong>
                </div>
            </div>
        </section>

        <section class="dashboard-section dashboard-section-bottom">
            <div class="dashboard-card referral-card">
                <h2 class="section-title">Referral Program</h2>

                <div class="referral-grid">
                    <div class="referral-subcard">
                        <h3>Share Your Referral Link</h3>
                        <p>Invite your friends and earn $25 credit when they book!</p>

                        <div class="referral-link-box">
                            <span id="referralLink">callthecleaners.com/ref/Jahedul</span>
                            <button type="button" class="btn btn-primary btn-sm copy-referral-btn">Copy</button>
                        </div>
                    </div>

                    <div class="referral-subcard">
                        <h3>Invite Via Email</h3>
                        <p>Send invitation to your friends via email</p>

                        <input type="email" class="form-control referral-input" placeholder="Enter friend's email">
                        <textarea class="form-control referral-input referral-message" rows="3" placeholder="Add a personal message (optional)"></textarea>
                        <button type="button" class="btn btn-primary btn-block send-invite-btn"><i class="fas fa-paper-plane" aria-hidden="true"></i> Send Invitation</button>
                    </div>
                </div>
            </div>

            <div class="dashboard-card referrals-card">
                <div class="dashboard-card-header">
                    <h2 class="section-title">Your Referrals</h2>
                    <a href="#" class="card-link">View all</a>
                </div>

                <div class="referral-metrics">
                    <div><strong>12</strong><span>Invited</span></div>
                    <div><strong>5</strong><span>Successful</span></div>
                    <div><strong>$125</strong><span>Earned Credits</span></div>
                </div>

                <div class="referral-table-wrap">
                    <table class="referral-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="far fa-user-circle" aria-hidden="true"></i> salimau@gmail.com</td>
                                <td><span class="referral-badge badge-invited">Invited</span></td>
                            </tr>
                            <tr>
                                <td><i class="far fa-user-circle" aria-hidden="true"></i> ritu.sarkar@hotmail.com</td>
                                <td><span class="referral-badge badge-success">Signed Up</span> <strong class="credit-plus">+ $25</strong></td>
                            </tr>
                            <tr>
                                <td><i class="far fa-user-circle" aria-hidden="true"></i> nahid.khan@gmail.com</td>
                                <td><span class="referral-badge badge-success">Completed</span> <strong class="credit-plus">+ $25</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <a href="#" class="view-referrals-link">View all referrals</a>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/assets/js/dashboard.js') }}"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endpush
