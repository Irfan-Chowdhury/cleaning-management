<header class="dashboard-header">
    <div class="header-left">
        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>

        <div class="header-greeting">
            <h1>Good Evening, <span>{{ auth()->check() ? auth()->user()->name : 'MD. JAHEDUL DINER' }}</span> &#128075;</h1>
            <p>Here's what's happening with your account today.</p>
        </div>
    </div>

    <div class="header-actions">
        <button type="button" class="notification-btn" aria-label="Notifications">
            <i class="far fa-bell" aria-hidden="true"></i>
            <span class="notification-badge">3</span>
        </button>

        <div class="dropdown user-profile">
            <a href="#" class="user-profile-toggle" id="userProfileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="https://i.pravatar.cc/84?img=12" alt="User avatar" class="user-avatar">
                <span class="user-name">{{ auth()->check() ? auth()->user()->name : 'MD. JAHEDUL' }}</span>
                <i class="fas fa-chevron-down user-chevron" aria-hidden="true"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userProfileDropdown">
                <a class="dropdown-item" href="#">Profile</a>
                <a class="dropdown-item" href="#">Account Settings</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#">Logout</a>
            </div>
        </div>
    </div>
</header>
