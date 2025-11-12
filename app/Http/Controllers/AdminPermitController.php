<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmployerDocument;
use Illuminate\Support\Facades\Log;

class AdminPermitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $permits = EmployerDocument::where('status', 'PENDING')->orderBy('created_at', 'desc')->paginate(30);
        return view('admin.permits.index', ['permits' => $permits]);
    }

    public function show($id)
    {
        $permit = EmployerDocument::findOrFail($id);
        return view('admin.permits.show', ['permit' => $permit]);
    }

    public function approve(Request $request, $id)
    {
        $permit = EmployerDocument::findOrFail($id);
        // Prevent approving an already-finalized permit
        $finalStatuses = ['APPROVED','REJECTED','BLOCKED','removed'];
        if (in_array($permit->status, $finalStatuses)) {
            return redirect()->route('admin.permits.index')->with('error', 'This permit has already been finalized and cannot be approved again.');
        }
        $permit->status = 'APPROVED';
        $permit->reviewed_by_admin = true;
        $permit->reviewed_at = now();
        if ($request->filled('admin_comment')) {
            $permit->review_reason = trim($permit->review_reason . "\nAdmin: " . $request->input('admin_comment'));
        }
        $permit->save();

        // Optionally notify employer via existing notifications system if present
        try {
            if (class_exists('\App\Models\Notification')) {
                \App\Models\Notification::create([
                    'user_id' => null,
                    'title' => 'Business permit approved',
                    'body' => 'Your submitted business permit has been approved by an administrator.',
                    'data' => json_encode(['employer_document_id' => $permit->id])
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to create notification for permit approval', ['id' => $permit->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.permits.index')->with('success', 'Permit approved.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['admin_comment' => 'nullable|string|max:1000']);
        $permit = EmployerDocument::findOrFail($id);
        $finalStatuses = ['APPROVED','REJECTED','BLOCKED','removed'];
        if (in_array($permit->status, $finalStatuses)) {
            return redirect()->route('admin.permits.index')->with('error', 'This permit has already been finalized and cannot be rejected.');
        }
        $permit->status = 'REJECTED';
        $permit->reviewed_by_admin = true;
        $permit->reviewed_at = now();
        $comment = $request->input('admin_comment');
        if ($comment) {
            $permit->review_reason = trim(($permit->review_reason ? $permit->review_reason . "\n" : '') . 'Admin: ' . $comment);
        }
        $permit->save();

        return redirect()->route('admin.permits.index')->with('success', 'Permit rejected.');
    }
}
