@extends('layouts.app')

@section('title', 'Notifications')

@push('styles')
    <style>
        .notifications-container {
            max-width: 760px;
            margin: 0 auto;
        }

        .notifications-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(19, 33, 60, 0.03);
            overflow: hidden;
        }

        .notifications-header {
            padding: 22px 24px;
            border-bottom: 1px solid #eef2f7;
            background: #ffffff;
        }

        .notifications-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #13213c;
        }

        .notifications-filter-nav {
            padding: 12px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f7;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #5c6a82;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-right: 8px;
            background: #eef2f7;
        }

        .filter-pill:hover {
            background: #e2e8f0;
            color: #13213c;
            text-decoration: none;
        }

        .filter-pill.active {
            background: #0866e8;
            color: #ffffff;
        }

        .filter-pill .badge {
            margin-left: 6px;
            font-weight: 700;
        }

        .fb-notification-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .fb-notification-item {
            display: flex;
            align-items: flex-start;
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
            position: relative;
            text-decoration: none;
            color: inherit;
        }

        .fb-notification-item:hover {
            background: #f8fafc;
            text-decoration: none;
            color: inherit;
        }

        .fb-notification-item.unread {
            background: #f0f6ff;
        }

        .fb-notification-item.unread:hover {
            background: #e6f0fe;
        }

        .fb-notification-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            margin-right: 16px;
            background: #eef2f7;
            color: #0866e8;
        }

        .fb-notification-item.unread .fb-notification-icon {
            background: #0866e8;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(8, 102, 232, 0.25);
        }

        .fb-notification-content {
            flex-grow: 1;
            padding-right: 12px;
        }

        .fb-notification-title {
            font-size: 15px;
            font-weight: 600;
            color: #13213c;
            margin-bottom: 3px;
            display: block;
        }

        .fb-notification-message {
            font-size: 14px;
            color: #475467;
            margin-bottom: 6px;
            line-height: 1.45;
            display: block;
        }

        .fb-notification-time {
            font-size: 12px;
            font-weight: 500;
            color: #8a94a6;
            display: inline-block;
        }

        .unread-dot {
            width: 10px;
            height: 10px;
            background: #0866e8;
            border-radius: 50%;
            display: inline-block;
            margin-left: 8px;
        }

        .fb-notification-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #8a94a6;
            background: transparent;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #e2e8f0;
            color: #13213c;
        }

        .action-btn.delete-btn:hover {
            background: #fee2e2;
            color: #ef4444;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="notifications-container">
        <div class="notifications-card">
            <div class="notifications-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0">Notifications</h4>
                    @if ($unreadCount > 0)
                        <span class="badge badge-primary badge-pill ml-2" style="font-size: 12px; padding: 5px 10px;">{{ $unreadCount }} Unread</span>
                    @endif
                </div>

                @if ($unreadCount > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm font-weight-bold" style="border-radius: 20px;">
                            <i class="fas fa-check-double mr-1"></i> Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <div class="notifications-filter-nav d-flex align-items-center">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="filter-pill {{ $filter !== 'unread' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="filter-pill {{ $filter === 'unread' ? 'active' : '' }}">
                    Unread
                    @if ($unreadCount > 0)
                        <span class="badge badge-light text-primary">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>

            <div class="fb-notification-list">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $title = $data['title'] ?? 'Notification';
                        $message = $data['message'] ?? '';
                        $icon = $data['icon'] ?? 'fas fa-bell';
                        $targetLink = route('notifications.read', $notification->id);
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <div class="fb-notification-item {{ $isUnread ? 'unread' : '' }}">
                        <a href="{{ $targetLink }}" class="d-flex align-items-start text-decoration-none text-body flex-grow-1">
                            <span class="fb-notification-icon">
                                <i class="{{ $icon }}"></i>
                            </span>
                            <span class="fb-notification-content">
                                <span class="fb-notification-title">
                                    {{ $title }}
                                    @if ($isUnread)
                                        <span class="unread-dot"></span>
                                    @endif
                                </span>
                                <span class="fb-notification-message">{{ $message }}</span>
                                <span class="fb-notification-time"><i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}</span>
                            </span>
                        </a>

                        <div class="fb-notification-actions">
                            <a href="{{ $targetLink }}" class="action-btn" title="Open details">
                                <i class="fas fa-chevron-right"></i>
                            </a>

                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="action-btn delete-btn js-notification-delete" data-title="{{ e($title) }}" title="Delete notification">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="far fa-bell-slash text-muted mb-3" style="font-size: 48px; opacity: 0.4;"></i>
                        <h6 class="text-muted font-weight-bold">No notifications found</h6>
                        <p class="text-muted small mb-0">When you receive new updates, they will appear here.</p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->hasPages())
                <div class="p-3 border-top d-flex justify-content-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $(document).on('click', '.js-notification-delete', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $form = $btn.closest('form');
            var title = $btn.data('title') || 'this notification';

            if (typeof Swal === 'undefined') {
                if (confirm('Delete ' + title + '?')) {
                    $form.submit();
                }
                return;
            }

            Swal.fire({
                title: 'Delete Notification?',
                html: 'Are you sure you want to delete <strong>' + title + '</strong>?'
                    + '<br><small class="text-muted">This action cannot be undone.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    $form.submit();
                }
            });
        });
    });
</script>
@endpush
