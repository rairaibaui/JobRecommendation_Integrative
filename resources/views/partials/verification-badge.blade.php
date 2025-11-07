@php
  /**
   * Renders a compact verification badge for business permit validations.
   * - Shows "Verified" for approved status
   * - Shows "Verification Failed" for rejected status
   * - Hides badge for pending_review and no validation states
   */
  use App\Models\DocumentValidation;
  use Illuminate\Support\Facades\Auth;

  if (!isset($validation)) {
    $currentUserId = Auth::id();
    $validation = $currentUserId
      ? DocumentValidation::where('user_id', $currentUserId)
        ->where('document_type', 'business_permit')
        ->orderByDesc('created_at')
        ->first()
      : null;
  }
@endphp

@if($validation)
  @if($validation->validation_status === 'approved')
    <div title="Business Permit Verified - {{ $validation->confidence_score }}% confidence"
         style="align-self:center; display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; padding:6px 12px; border-radius:20px; margin:6px 0 8px; border:2px solid #c3e6cb; background:#d4edda; color:#155724;">
      <i class="fas fa-check-circle"></i> Verified
    </div>
  @elseif($validation->validation_status === 'rejected')
    <div title="{{ $validation->reason }}"
         style="align-self:center; display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; padding:6px 12px; border-radius:20px; margin:6px 0 8px; border:2px solid #f5c6cb; background:#f8d7da; color:#721c24;">
      <i class="fas fa-times-circle"></i> Verification Failed
    </div>
  @endif
@endif
