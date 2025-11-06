<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $search = $request->get('search');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $query = AuditLog::with('user');

        // Filter by action
        if ($action) {
            $query->where('action', $action);
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Get logs with pagination
        $logs = $query->latest()->paginate(50);

        // Statistics
        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'this_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => AuditLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Action breakdown
        $actionBreakdown = AuditLog::select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->get();

        // Activity timeline (last 7 days)
        $activityData = AuditLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Most active users
        $activeUsers = AuditLog::select('user_id', DB::raw('COUNT(*) as action_count'))
            ->whereNotNull('user_id')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('user_id')
            ->orderByDesc('action_count')
            ->limit(5)
            ->with('user')
            ->get();

        return view('admin.audit.index', compact('logs', 'stats', 'actionBreakdown', 'activityData', 'activeUsers'));
    }
}
