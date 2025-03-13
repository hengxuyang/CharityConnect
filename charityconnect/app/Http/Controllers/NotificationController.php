<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get notifications for the current user
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['request', 'donation', 'charity'])
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Check if the notification belongs to the user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Mark as read by deleting the notification
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        // Delete all notifications for the current user
        Notification::where('user_id', Auth::id())->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        // Check if the notification belongs to the user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('notifications.show', compact('notification'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification)
    {
        // Check if the notification belongs to the user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the notification
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
