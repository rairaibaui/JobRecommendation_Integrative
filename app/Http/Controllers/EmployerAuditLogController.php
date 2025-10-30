<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->user_type === 'employer', 403);

        $query = AuditLog::where('user_id', $user->id)->orderByDesc('created_at');

        if ($request->filled('event')) {
            $query->where('event', $request->string('event'));
        }
        if ($request->filled('q')) {
            $q = '%' . $request->string('q') . '%';
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', $q)
                  ->orWhere('message', 'like', $q);
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('employer.audit-logs', [
            'user' => $user,
            'logs' => $logs,
            'events' => AuditLog::where('user_id', $user->id)->select('event')->distinct()->pluck('event'),
        ]);
    }
}
