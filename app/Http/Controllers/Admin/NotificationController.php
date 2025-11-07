<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display admin notifications page with filters, search, and pagination.
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('read', false);
            } elseif ($request->status === 'read') {
                $query->where('read', true);
            }
        }

        // Search by company name or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.company_name')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.email')) LIKE ?", ["%{$search}%"]);
            });
        }

        $perPage = $request->input('per_page', 25);
        $notifications = $query->paginate($perPage)->appends($request->except('page'));

        // Stats
        $totalCount = Notification::where('user_id', Auth::id())->count();
        $unreadCount = Notification::where('user_id', Auth::id())->where('read', false)->count();
        $warningCount = Notification::where('user_id', Auth::id())->where('type', 'warning')->count();
        $errorCount = Notification::where('user_id', Auth::id())->where('type', 'error')->count();

        return view('admin.notifications', compact(
            'notifications',
            'totalCount',
            'unreadCount',
            'warningCount',
            'errorCount'
        ));
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $notification->update([
            'read' => true,
            'read_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications for the current admin as read.
     */
    public function markAllRead(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Mark selected notifications as read (bulk action).
     */
    public function bulkMarkRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:notifications,id',
        ]);

        Notification::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return redirect()->back()->with('success', count($request->ids).' notification(s) marked as read');
    }

    /**
     * Delete a single notification.
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }

    /**
     * Delete selected notifications (bulk action).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:notifications,id',
        ]);

        Notification::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->back()->with('success', count($request->ids).' notification(s) deleted');
    }
}
