<header class="dashboard-header">
	    <div class="header-left">
	        <button type="button" class="sidebar-toggle" aria-label="Toggle sidebar">
	            <i class="fas fa-bars" aria-hidden="true"></i>
	        </button>

	        <div class="header-greeting">
	            @if (request()->is('dashboard'))
	                <h1>Good Evening, <span>{{ auth()->check() ? auth()->user()->first_name : 'Guest' }}</span> &#128075;</h1>
	                <p>Here's what's happening with your account today.</p>
	            @endif
	        </div>
	    </div>

    <div class="header-actions">
        <button type="button"
                class="fullscreen-btn"
                id="fullscreenToggle"
                title="Fullscreen"
                aria-label="Fullscreen">
            <i class="fas fa-expand" aria-hidden="true"></i>
        </button>

        @php
            $authUser = auth()->user();
            $unreadNotificationsCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
            $headerNotifications = $authUser ? $authUser->notifications()->take(6)->get() : collect();
        @endphp

        <div class="dropdown notification-dropdown">
            <button type="button"
                    class="notification-btn"
                    id="notificationDropdown"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="Notifications">
                <i class="far fa-bell" aria-hidden="true"></i>
                @if ($unreadNotificationsCount > 0)
                    <span class="notification-badge">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-right notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-menu-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Notifications</h6>
                    @if ($unreadNotificationsCount > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 notification-read-link" style="font-size: 13px; text-decoration: none;">Mark all as read</button>
                        </form>
                    @endif
                </div>

                <div class="notification-list">
                    @forelse ($headerNotifications as $notification)
                        @php
                            $nData = $notification->data;
                            $nTitle = $nData['title'] ?? 'Notification';
                            $nMessage = $nData['message'] ?? '';
                            $nIcon = $nData['icon'] ?? 'fas fa-bell';
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        <a class="dropdown-item notification-item {{ $isUnread ? 'unread' : '' }} d-flex align-items-start" href="{{ route('notifications.read', $notification->id) }}">
                            <span class="notification-icon">
                                <i class="{{ $nIcon }}" aria-hidden="true"></i>
                            </span>
                            <span class="notification-content">
                                <span class="notification-title">{{ $nTitle }}</span>
                                <span class="notification-message">{{ $nMessage }}</span>
                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="p-3 text-center text-muted" style="font-size: 14px;">
                            <i class="far fa-bell-slash d-block mb-2" style="font-size: 24px; opacity: 0.5;"></i>
                            No notifications yet.
                        </div>
                    @endforelse
                </div>

                <div class="notification-menu-footer">
                    <a href="{{ route('notifications.index') }}">See All Notifications</a>
                </div>
            </div>
        </div>

        <div class="dropdown user-profile">
            <a href="#" class="user-profile-toggle" id="userProfileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ auth()->check() ? auth()->user()->photo_url : 'https://i.pravatar.cc/84?img=12' }}" alt="User avatar" class="user-avatar">
                <span class="user-name">{{ auth()->check() ? trim(auth()->user()->first_name . ' ' . (auth()->user()->last_name ?? '')) : 'Guest' }}</span>
                <i class="fas fa-chevron-down user-chevron" aria-hidden="true"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userProfileDropdown">
                <a class="dropdown-item" href="{{ route('customer.profile.index') }}">
                    <i class="fas fa-user-circle mr-2 text-primary"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <form id="header-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>
