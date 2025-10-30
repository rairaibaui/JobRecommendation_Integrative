<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Return recent notifications as JSON
    public function list(Request $request)
    {
        $user = Auth::user();
        $limit = (int)($request->query('limit', 10));
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)->where('read', false)->count();

        return response()->json([
            'success' => true,
            'unread' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    // Return unread count only
    public function count()
    {
        $user = Auth::user();
        $unreadCount = Notification::where('user_id', $user->id)->where('read', false)->count();
        return response()->json(['success' => true, 'unread' => $unreadCount]);
    }

    // Mark a single notification as read
    public function markRead($id)
    {
        $user = Auth::user();
        $notif = Notification::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $notif->read = true;
        $notif->read_at = now();
        $notif->save();
        return response()->json(['success' => true]);
    }

    // Mark all as read
    public function markAllRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->where('read', false)->update([
            'read' => true,
            'read_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }
}
