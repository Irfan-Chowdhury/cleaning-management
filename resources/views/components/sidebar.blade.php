<aside class="dashboard-sidebar" aria-label="Dashboard navigation">
    <div class="sidebar-logo">
        <a href="#" class="sidebar-logo-link" aria-label="Cleaning Management">
            <img src="https://placehold.co/340x96/ffffff/0866e8?text=Clean+Manage" alt="Cleaning Management logo">
        </a>

        <button type="button" class="sidebar-close" aria-label="Close sidebar">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ url('/dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-home" aria-hidden="true"></i></span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('services.index') }}" class="sidebar-link {{ request()->is('services*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-broom" aria-hidden="true"></i></span>
            <span>Services</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="fas fa-users" aria-hidden="true"></i></span>
            <span>Customers</span>
        </a>

        <!-- Timesheets with collapse -->
        <a href="#timesheets-submenu" data-toggle="collapse" aria-expanded="false" class="sidebar-link d-flex align-items-center justify-content-between">
            <span class="d-flex align-items-center">
                <span class="sidebar-link-icon"><i class="far fa-clock" aria-hidden="true"></i></span>
                <span>Timesheets</span>
            </span>
            <i class="fas fa-chevron-down ml-auto" style="font-size: 10px;" aria-hidden="true"></i>
        </a>
        <div class="collapse" id="timesheets-submenu" style="padding-left: 20px;">
            <a href="#" class="sidebar-link" style="margin-left: 18px; min-height: 38px; padding: 6px 14px;">
                <span class="sidebar-link-icon" style="width: 20px; flex: 0 0 20px;"><i class="fas fa-calendar-check" aria-hidden="true"></i></span>
                <span>Working Shifts</span>
            </a>
            <a href="#" class="sidebar-link" style="margin-left: 18px; min-height: 38px; padding: 6px 14px;">
                <span class="sidebar-link-icon" style="width: 20px; flex: 0 0 20px;"><i class="fas fa-umbrella-beach" aria-hidden="true"></i></span>
                <span>Manage Holiday</span>
            </a>
        </div>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="far fa-calendar-alt" aria-hidden="true"></i></span>
            <span>Bookings</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="fas fa-user-friends" aria-hidden="true"></i></span>
            <span>Referrals</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="far fa-star" aria-hidden="true"></i></span>
            <span>Credits &amp; Rewards</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="fas fa-star" aria-hidden="true"></i></span>
            <span>Reviews</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="far fa-file-alt" aria-hidden="true"></i></span>
            <span>Payments &amp; Invoices</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="sidebar-link-icon"><i class="fas fa-cog" aria-hidden="true"></i></span>
            <span>Settings</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="sidebar-link sidebar-link-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <span class="sidebar-link-icon"><i class="fas fa-sign-out-alt" aria-hidden="true"></i></span>
            <span>Logout</span>
        </a>
    </nav>

    <div class="sidebar-support">
        <div class="sidebar-support-copy">
            <h6>Need help?</h6>
            <p>Our support team is available 7 days a week.</p>
        </div>

        <a href="#" class="btn support-btn">Contact Support</a>
        <i class="fas fa-headset support-icon" aria-hidden="true"></i>
    </div>
</aside>
