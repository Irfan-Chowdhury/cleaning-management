<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display Facebook-style notifications list for the logged-in user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $notificationsQuery = ($filter === 'unread')
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $notificationsQuery->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Mark a specific notification as read and redirect to its target link.
     */
    public function read(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['link'] ?? null;

        if (empty($targetUrl)) {
            $targetUrl = ((int) $user->role === 1)
                ? route('admin.dashboard')
                : route('dashboard');
        }

        return redirect()->to($targetUrl);
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        return back()->with('success', 'Notification removed successfully.');
    }
}
