@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('public/assets/css/dashboard.css') }}">
@endpush

@section('content')
    <div class="customer-dashboard">
        <section class="dashboard-section dashboard-section-top">
            <div class="dashboard-card next-cleaning-card">
                <div class="dashboard-card-header">
                    <h2 class="section-title">Next Cleaning</h2>
                    <span class="status-pill status-confirmed">Confirmed</span>
                </div>

                <div class="next-cleaning-body">
                    <img src="https://picsum.photos/seed/cleaning1/400/260" alt="Regular home cleaning service" class="next-cleaning-image">

                    <div class="next-cleaning-details">
                        <h3>Regular Home Cleaning</h3>

                        <ul class="cleaning-meta">
                            <li><i class="far fa-calendar-alt" aria-hidden="true"></i> Fri, 15 Aug 2025</li>
                            <li><i class="far fa-clock" aria-hidden="true"></i> 09:00 AM - 01:00 PM</li>
                            <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> 25 King St, Sydney NSW 2000</li>
                            <li><i class="fas fa-user-check" aria-hidden="true"></i> Cleaner: Sarah Johnson</li>
                        </ul>

                        <div class="cleaner-rating">
                            <i class="fas fa-star" aria-hidden="true"></i>
                            <span>4.9</span>
                        </div>
                    </div>
                </div>

                <div class="next-cleaning-actions">
                    <a href="#" class="btn btn-primary btn-sm dashboard-action-btn">Manage Booking</a>
                    <a href="#" class="btn btn-outline-primary btn-sm dashboard-action-btn">Reschedule</a>
                    <a href="#" class="btn btn-outline-danger btn-sm dashboard-action-btn">Cancel Booking</a>
                </div>
            </div>

            <div class="dashboard-stats-grid">
                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-blue"><i class="far fa-calendar-alt" aria-hidden="true"></i></div>
                    <div>
                        <h2 class="section-title">Upcoming Bookings</h2>
                        <div class="stat-value">2</div>
                        <p>Bookings</p>
                        <a href="#">View all bookings</a>
                    </div>
                </div>

                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-green"><i class="fas fa-shopping-bag" aria-hidden="true"></i></div>
                    <div>
                        <h2 class="section-title">Lifetime Bookings</h2>
                        <div class="stat-value">18</div>
                        <p>Total Bookings</p>
                        <a href="#">View history</a>
                    </div>
                </div>

                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-yellow"><i class="far fa-star" aria-hidden="true"></i></div>
                    <div>
                        <h2 class="section-title">Earned Credits</h2>
                        <div class="stat-value">$45.00</div>
                        <p>Available Credits</p>
                        <a href="#">View details</a>
                    </div>
                </div>

                <div class="dashboard-card stat-card">
                    <div class="stat-icon stat-icon-purple"><i class="fas fa-wallet" aria-hidden="true"></i></div>
                    <div>
                        <h2 class="section-title">Total Spent</h2>
                        <div class="stat-value">$1,260.00</div>
                        <p>Total Spent</p>
                        <a href="#">View invoices</a>
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

                        <div class="share-via">Share via</div>
                        <div class="social-buttons">
                            <a href="#" aria-label="Email"><i class="far fa-envelope" aria-hidden="true"></i></a>
                            <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                            <a href="#" aria-label="More"><i class="fas fa-ellipsis-h" aria-hidden="true"></i></a>
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
@endpush
