<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->with(['foundReport.reporter', 'foundReport.lostItem'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($notifications);
    }

    public function getUnread()
    {
        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
        $notifications = Auth::user()->notifications()
            ->with(['foundReport.reporter', 'foundReport.lostItem'])
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['count' => $unreadCount, 'notifications' => $notifications]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if (!$notification || $notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json($notification);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);
        return response()->json(['message' => 'All marked as read']);
    }

    public function delete($id)
    {
        $notification = Notification::find($id);
        if (!$notification || $notification->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
