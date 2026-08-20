<header class="dashboard-header">
	    <div class="header-left">
	        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
	            <i class="fas fa-bars" aria-hidden="true"></i>
	        </button>

	        <div class="header-greeting">
	            @if (request()->is('dashboard'))
	                <h1>Good Evening, <span>{{ auth()->check() ? auth()->user()->name : 'MD. JAHEDUL DINER' }}</span> &#128075;</h1>
	                <p>Here's what's happening with your account today.</p>
	            @endif
	        </div>
	    </div>

    <div class="header-actions">
        <div class="dropdown notification-dropdown">
            <button type="button"
                    class="notification-btn"
                    id="notificationDropdown"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="Notifications">
                <i class="far fa-bell" aria-hidden="true"></i>
                <span class="notification-badge">3</span>
            </button>

            <div class="dropdown-menu dropdown-menu-right notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-menu-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Notifications</h6>
                    <a class="notification-read-link" href="#">Mark all as read</a>
                </div>

                <div class="notification-list">
                    <a class="dropdown-item notification-item unread d-flex align-items-start" href="#">
                        <span class="notification-icon">
                            <i class="fas fa-calendar-check" aria-hidden="true"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-title">New booking confirmed</span>
                            <span class="notification-message">A home cleaning service has been scheduled for tomorrow morning.</span>
                            <span class="notification-time">1 month ago</span>
                        </span>
                    </a>

                    <a class="dropdown-item notification-item unread d-flex align-items-start" href="#">
                        <span class="notification-icon">
                            <i class="fas fa-exclamation" aria-hidden="true"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-title">Cleaner arrival updated</span>
                            <span class="notification-message">Team B changed the estimated arrival time for booking #CL-2048.</span>
                            <span class="notification-time">1 month ago</span>
                        </span>
                    </a>

                    <a class="dropdown-item notification-item d-flex align-items-start" href="#">
                        <span class="notification-icon">
                            <i class="fas fa-wallet" aria-hidden="true"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-title">Payment received</span>
                            <span class="notification-message">BDT 2,500 was added to the customer wallet successfully.</span>
                            <span class="notification-time">1 month ago</span>
                        </span>
                    </a>

                    <a class="dropdown-item notification-item d-flex align-items-start" href="#">
                        <span class="notification-icon">
                            <i class="fas fa-user-plus" aria-hidden="true"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-title">New customer registered</span>
                            <span class="notification-message">Sarah Ahmed created an account and requested service details.</span>
                            <span class="notification-time">2 months ago</span>
                        </span>
                    </a>

                    <a class="dropdown-item notification-item d-flex align-items-start" href="#">
                        <span class="notification-icon">
                            <i class="fas fa-star" aria-hidden="true"></i>
                        </span>
                        <span class="notification-content">
                            <span class="notification-title">New service review</span>
                            <span class="notification-message">A customer left a 5-star rating for deep cleaning service.</span>
                            <span class="notification-time">2 months ago</span>
                        </span>
                    </a>
                </div>

                <div class="notification-menu-footer">
                    <a href="#">View All Notifications</a>
                </div>
            </div>
        </div>

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
