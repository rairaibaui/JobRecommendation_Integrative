@extends('layouts.admin')

@section('content')
    <div style="padding:20px;">
        <h2>Permits Needing Review</h2>
        @if(session('success'))<div style="color:green;margin-bottom:12px;">{{ session('success') }}</div>@endif
        <table style="width:100%;border-collapse:collapse;margin-top:12px;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Employer Email</th>
                    <th style="padding:8px;">Document Type</th>
                    <th style="padding:8px;">Has Signature</th>
                    <th style="padding:8px;">Review Reason</th>
                    <th style="padding:8px;">Submitted</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permits as $p)
                    <tr style="border-bottom:1px solid #f3f3f3;">
                        <td style="padding:8px;">{{ $p->email ?? 'N/A' }}</td>
                        <td style="padding:8px;">
                            @php
                                $typeLabels = [
                                    'MAYORS_PERMIT' => "Mayor's Permit",
                                    'BARANGAY_CLEARANCE' => 'Barangay Clearance',
                                    'BARANGAY_LOCATIONAL_CLEARANCE' => 'Barangay Locational Clearance',
                                    'DTI' => 'DTI Certificate',
                                    'BUSINESS_PERMIT' => 'Business Permit',
                                    'UNKNOWN' => 'Not specified',
                                ];
                                $raw = $p->document_type ?? 'UNKNOWN';
                                $label = $typeLabels[strtoupper((string)$raw)] ?? 'Not specified';
                            @endphp
                            {{ $label }}
                        </td>
                        <td style="padding:8px;">{{ $p->has_signature ? 'Yes' : 'No' }}</td>
                        <td style="padding:8px;">{{ $p->review_reason ?? '-' }}</td>
                        <td style="padding:8px;">{{ $p->created_at->diffForHumans() }}</td>
                        <td style="padding:8px;">
                            <a href="{{ route('admin.permits.show', $p->id) }}" class="btn-action btn-view">View</a>
                            @php
                                // statuses considered final; show buttons but disable them
                                $finalStatuses = ['APPROVED','REJECTED','BLOCKED','removed'];
                                $isFinal = in_array($p->status, $finalStatuses);
                            @endphp

                            @if(!$isFinal)
                                {{-- Buttons open a confirmation modal to collect admin comment before submitting --}}
                                <button
                                    class="btn-action btn-approve"
                                    style="margin-left:8px;"
                                    data-approve-url="{{ route('admin.permits.approve', $p->id) }}"
                                    data-reject-url="{{ route('admin.permits.reject', $p->id) }}"
                                    data-permit-id="{{ $p->id }}"
                                    onclick="openAdminActionModal(this)">
                                    Approve
                                </button>

                                <button
                                    class="btn-action btn-reject"
                                    style="margin-left:6px;"
                                    data-approve-url="{{ route('admin.permits.approve', $p->id) }}"
                                    data-reject-url="{{ route('admin.permits.reject', $p->id) }}"
                                    data-permit-id="{{ $p->id }}"
                                    onclick="openAdminActionModal(this)">
                                    Reject
                                </button>
                            @else
                                {{-- Finalized: show disabled action buttons to indicate no further actions allowed --}}
                                <button class="btn-action btn-approve" disabled aria-disabled="true" tabindex="-1" title="No actions allowed for finalized status" style="margin-left:8px; opacity:0.6; cursor:not-allowed; pointer-events:none;">Approve</button>
                                <button class="btn-action btn-reject" disabled aria-disabled="true" tabindex="-1" title="No actions allowed for finalized status" style="margin-left:6px; opacity:0.6; cursor:not-allowed; pointer-events:none;">Reject</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:16px;">No permits pending review.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:12px;">
            {{ $permits->links() }}
        </div>
    </div>

    <!-- Admin Approve/Reject Modal -->
    <div id="adminActionModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:12px; padding:18px; width:95%; max-width:560px;">
            <h3 id="adminActionModalTitle">Confirm Action</h3>
            <p id="adminActionModalSubtitle">Please add an optional note for the employer. This will be recorded with the action.</p>

            <form id="adminActionForm" method="POST" style="margin-top:12px;">
                @csrf
                <div style="margin-bottom:12px;">
                    <label for="admin_comment">Admin comment (optional)</label>
                    <textarea id="admin_comment" name="admin_comment" rows="4" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;"></textarea>
                </div>

                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button type="button" onclick="closeAdminActionModal()" class="btn btn-secondary">Cancel</button>
                    <button type="button" id="modalApproveBtn" class="btn btn-primary" style="background:#10b981;" onclick="submitAdminAction('approve')">Approve</button>
                    <button type="button" id="modalRejectBtn" class="btn btn-primary" style="background:#ef4444;" onclick="submitAdminAction('reject')">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAdminActionModal(btn) {
            var modal = document.getElementById('adminActionModal');
            modal.style.display = 'flex';
            // store urls on modal dataset
            modal.dataset.approveUrl = btn.getAttribute('data-approve-url');
            modal.dataset.rejectUrl = btn.getAttribute('data-reject-url');
            modal.dataset.permitId = btn.getAttribute('data-permit-id');
            // set titles
            var title = document.getElementById('adminActionModalTitle');
            title.textContent = 'Confirm Admin Action';
            document.getElementById('admin_comment').value = '';
        }

        function closeAdminActionModal() {
            var modal = document.getElementById('adminActionModal');
            modal.style.display = 'none';
        }

        function submitAdminAction(action) {
            var modal = document.getElementById('adminActionModal');
            var url = action === 'approve' ? modal.dataset.approveUrl : modal.dataset.rejectUrl;
            var form = document.getElementById('adminActionForm');
            form.action = url;
            // Submit the form programmatically with Fetch to allow graceful UI response
            var formData = new FormData(form);
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            }).then(function(resp){
                if (resp.ok) {
                    // reload page to reflect changes
                    window.location.reload();
                } else {
                    resp.text().then(function(t){ alert('Action failed: ' + t); });
                }
            }).catch(function(err){ alert('Request failed: ' + err); });
        }
    </script>
@endsection
