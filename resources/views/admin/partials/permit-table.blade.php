<table class="table">
    <thead>
        <tr>
            <th>Employer</th>
            <th>Status</th>
            <th>AI Confidence</th>
            <th>Document Type</th>
            <th>Expiry Date</th>
            <th>Submitted</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($permits as $permit)
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ strtoupper(substr($permit->user->company_name ?? $permit->user->email, 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ $permit->user->company_name ?? 'N/A' }}</div>
                            <div class="user-email">{{ $permit->user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $status = $permit->validation_status ?? 'pending_review';
                        $statusClass = match($status) {
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            'pending_review' => 'status-needs-review',
                            default => 'status-pending'
                        };
                        $statusIcon = match($status) {
                            'approved' => 'fa-check-circle',
                            'rejected' => 'fa-times-circle',
                            'pending_review' => 'fa-clock',
                            default => 'fa-clock'
                        };
                        $aiAnalysis = is_string($permit->ai_analysis) ? json_decode($permit->ai_analysis, true) : $permit->ai_analysis;
                        $reason = $permit->reason ?? ($aiAnalysis['reason'] ?? null);
                        if ($status === 'pending_review') {
                            $tooltipText = $reason
                                ? 'Pending review: ' . 
                                    (strlen($reason) > 180 ? substr($reason,0,177) . '...' : $reason)
                                : 'This permit is waiting for administrator review. AI analysis is complete.';
                        } else {
                            $tooltipText = match($status) {
                                'approved' => 'This business permit has been approved. The employer can post jobs and hire applicants.',
                                'rejected' => 'This permit was rejected. The employer must upload a valid business permit to continue.',
                                default => 'Status unknown'
                            };
                        }
                    @endphp
                    <div class="tooltip">
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="tooltip-text">{{ $tooltipText }}</span>
                    </div>

                    @if($reason)
                        <div style="margin-top:6px; color:#64748b; font-size:13px; max-width:320px;">
                            <strong style="font-weight:600;">Reason:</strong>
                            <span>{{ \Illuminate\Support\Str::limit($reason, 140) }}</span>
                        </div>
                    @endif
                </td>
                <td>
                    @php
                        $confidence = $permit->confidence_score ?? 0;
                        $confidenceClass = $confidence >= 80 ? 'high' : ($confidence >= 50 ? 'medium' : 'low');
                    @endphp
                    <div class="ai-score">
                        <div class="score-bar">
                            <div class="score-fill {{ $confidenceClass }}" style="width: {{ $confidence }}%"></div>
                        </div>
                        <span class="score-text">{{ $confidence }}%</span>
                    </div>
                </td>
                <td>
                    @php
                        $typeLabels = [
                            'MAYORS_PERMIT' => "Mayor's Permit",
                            'BARANGAY_CLEARANCE' => 'Barangay Clearance',
                            'BARANGAY_LOCATIONAL_CLEARANCE' => 'Barangay Locational Clearance',
                            'DTI' => 'DTI Certificate',
                            'BUSINESS_PERMIT' => 'Business Permit',
                            'UNKNOWN' => 'Not specified',
                        ];

                        $aiAnalysis = is_string($permit->ai_analysis) ? json_decode($permit->ai_analysis, true) : $permit->ai_analysis;
                        // Prefer the saved document_type on the record, fallback to AI analysis
                        $rawType = $permit->document_type ?? ($aiAnalysis['document_type'] ?? 'UNKNOWN');
                        $key = strtoupper((string) $rawType);
                        $docLabel = $typeLabels[$key] ?? 'Not specified';
                    @endphp
                    <span style="font-size: 13px; color: #475569;">{{ $docLabel }}</span>
                </td>
                <td>
                    @php
                        $expiryDateRaw = $permit->permit_expiry_date;
                        $inferred = false;
                        // Prepare text sources to search for 'mandaluyong'
                        $aiText = '';
                        if (is_string($permit->ai_analysis)) {
                            $decoded = json_decode($permit->ai_analysis, true);
                        } else {
                            $decoded = is_array($permit->ai_analysis) ? $permit->ai_analysis : null;
                        }
                        if (is_array($decoded)) {
                            // flatten nested arrays and collect scalar values only to avoid Array to string conversion
                            $flatten = function($arr) use (&$flatten) {
                                $out = [];
                                foreach ($arr as $v) {
                                    if (is_array($v)) {
                                        $out = array_merge($out, $flatten($v));
                                    } elseif (is_scalar($v)) {
                                        $out[] = (string) $v;
                                    }
                                }
                                return $out;
                            };
                            $aiText = implode(' ', $flatten($decoded));
                        }
                        $combinedText = strtolower(trim(($aiText ?? '') . ' ' . ($permit->ocr_text ?? '') . ' ' . ($permit->raw_text ?? '') . ' ' . ($permit->reason ?? '')));
                        if (!$expiryDateRaw && strpos($combinedText, 'mandaluyong') !== false) {
                            $expiryDateRaw = \Carbon\Carbon::create(2025,12,31);
                            $inferred = true;
                        }

                        if ($expiryDateRaw) {
                            $expiryDate = is_string($expiryDateRaw) ? \Carbon\Carbon::parse($expiryDateRaw) : $expiryDateRaw;
                            $isExpiringSoon = $expiryDate->diffInDays(now()) <= 30 && $expiryDate->isFuture();
                            $isExpired = $expiryDate->isPast();
                        }
                    @endphp

                    @if(isset($expiryDate))
                        <span style="color: {{ $isExpired ? '#ef4444' : ($isExpiringSoon ? '#f59e0b' : '#64748b') }}; font-size: 13px;">
                            {{ $expiryDate->format('M d, Y') }}@if($inferred) <small style="color:#94a3b8;">(inferred)</small>@endif
                            @if($isExpired)
                                <i class="fas fa-exclamation-triangle" title="Expired"></i>
                            @elseif($isExpiringSoon)
                                <i class="fas fa-clock" title="Expiring Soon"></i>
                            @endif
                        </span>
                    @else
                        <span style="color: #94a3b8; font-size: 13px;">Not specified</span>
                    @endif
                </td>
                <td>
                    {{ $permit->created_at->diffForHumans() }}
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.verifications.file', $permit->id) }}" 
                           class="btn-action btn-view" 
                           target="_blank"
                           title="View Permit Document">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
                        @if($status !== 'approved')
                            <button class="btn-action btn-approve"
                                    title="Approve this permit"
                                    onclick="showApproveModal({{ $permit->id }}, '{{ $permit->user->company_name ?? '' }}')">
                                <i class="fas fa-check"></i>
                                Approve
                            </button>
                        @endif
                        @if($status !== 'rejected')
                            <button class="btn-action btn-reject"
                                    title="Reject this permit"
                                    onclick="showPermitRejectModal({{ $permit->id }}, '{{ $permit->user->company_name ?? '' }}')">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <h3>No Business Permits Found</h3>
                        <p>There are no business permits matching your current filters.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px; color: #1e293b;">Approve Business Permit</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Approving permit for <strong id="approveCompanyName"></strong>:</p>
        <form id="approveForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Permit Expiry Date (Optional)</label>
                <input 
                    type="date" 
                    name="permit_expiry_date"
                    min="{{ date('Y-m-d') }}"
                    style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit;"
                >
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #475569;">Admin Notes (Optional)</label>
                <textarea 
                    name="admin_notes"
                    style="width: 100%; min-height: 80px; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit;"
                    placeholder="Any additional notes or comments..."
                ></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background: #10b981;">
                    <i class="fas fa-check"></i>
                    Approve Permit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="permitRejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px; color: #1e293b;">Reject Business Permit</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Please provide a reason for rejecting <strong id="rejectCompanyName"></strong>'s business permit:</p>
        <form id="permitRejectForm" method="POST">
            @csrf
            <textarea 
                name="rejection_reason" 
                required
                style="width: 100%; min-height: 120px; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; margin-bottom: 20px;"
                placeholder="e.g., Document is unclear/blurry, permit has expired, not a valid business permit, etc."
            ></textarea>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closePermitRejectModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">
                    <i class="fas fa-times"></i>
                    Reject Permit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showApproveModal(permitId, companyName) {
        document.getElementById('approveCompanyName').textContent = companyName;
        document.getElementById('approveForm').action = '/admin/verifications/' + permitId + '/approve';
        document.getElementById('approveModal').style.display = 'flex';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
    }

    function showPermitRejectModal(permitId, companyName) {
        document.getElementById('rejectCompanyName').textContent = companyName;
        document.getElementById('permitRejectForm').action = '/admin/verifications/' + permitId + '/reject';
        document.getElementById('permitRejectModal').style.display = 'flex';
    }

    function closePermitRejectModal() {
        document.getElementById('permitRejectModal').style.display = 'none';
    }

    // Close modals when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeApproveModal();
    });

    document.getElementById('permitRejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePermitRejectModal();
    });
</script>
