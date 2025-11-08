<table class="table">
    <thead>
        <tr>
            <th>Job Seeker</th>
            <th>Status</th>
            <th>AI Score</th>
            <th>Flags</th>
            <th>Uploaded</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($resumes as $resume)
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">
                            @if(!empty($resume->profile_picture) && file_exists(storage_path('app/public/' . $resume->profile_picture)))
                                <img src="{{ asset('storage/' . $resume->profile_picture) }}" alt="{{ $resume->first_name }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;" />
                            @else
                                {{ strtoupper(substr($resume->first_name ?? $resume->email, 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ $resume->first_name }} {{ $resume->last_name }}</div>
                            <div class="user-email">{{ $resume->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $status = $resume->resume_verification_status ?? 'pending';
                        $statusClass = match($status) {
                            'verified' => 'status-verified',
                            'needs_review' => 'status-needs-review',
                            'pending' => 'status-pending',
                            default => 'status-pending'
                        };
                        $statusIcon = match($status) {
                            'verified' => 'fa-check-circle',
                            'needs_review' => 'fa-exclamation-triangle',
                            'pending' => 'fa-clock',
                            default => 'fa-clock'
                        };
                        $tooltipText = match($status) {
                            'verified' => 'This resume has been verified by AI or manually approved. The job seeker can apply for jobs.',
                            'needs_review' => 'This resume requires manual review by an administrator due to missing sections or low AI confidence.',
                            'pending' => 'This resume is waiting for AI verification or initial review.',
                            default => 'Status unknown'
                        };
                    @endphp
                    <div class="tooltip">
                        <span class="status-badge {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="tooltip-text">{{ $tooltipText }}</span>
                    </div>
                </td>
                <td>
                    @php
                        $score = $resume->verification_score ?? 0;
                        $scoreClass = $score >= 70 ? 'high' : ($score >= 50 ? 'medium' : 'low');
                    @endphp
                    <div class="ai-score">
                        <div class="score-bar">
                            <div class="score-fill {{ $scoreClass }}" style="width: {{ $score }}%"></div>
                        </div>
                        <span class="score-text">{{ $score }}%</span>
                    </div>
                </td>
                <td>
                    @php
                        $flags = json_decode($resume->verification_flags, true) ?? [];
                    @endphp
                    @if(count($flags) > 0)
                        <div class="flags-list">
                            @foreach(array_slice($flags, 0, 2) as $flag)
                                <span class="flag-item">{{ str_replace('_', ' ', $flag) }}</span>
                            @endforeach
                            @if(count($flags) > 2)
                                <span class="flag-item">+{{ count($flags) - 2 }} more</span>
                            @endif
                        </div>
                    @else
                        <span style="color: #94a3b8; font-size: 13px;">No flags</span>
                    @endif
                </td>
                <td>
                    {{ $resume->updated_at ? $resume->updated_at->diffForHumans() : 'N/A' }}
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="{{ route('admin.resumes.view', $resume->id) }}" 
                           class="btn-action btn-view" 
                           target="_blank"
                           title="View Resume PDF">
                            <i class="fas fa-eye"></i>
                            View
                        </a>
                        @php
                            $isRejected = ($status === 'rejected');
                            $isVerified = ($status === 'verified');
                        @endphp
                        @if($status !== 'verified')
                            <form method="POST" action="{{ route('admin.resumes.approve', $resume->id) }}" style="display: inline;">
                                @csrf
                                <button type="button" 
                                        class="btn-action btn-approve" 
                                        title="{{ $isRejected ? 'Resume rejected. Waiting for new upload before review.' : 'Approve this resume' }}"
                                        @if($isRejected)
                                            disabled
                                        @else
                                            onclick="(typeof openApproveModal === 'function') ? openApproveModal({{ $resume->id }}, '{{ addslashes(trim($resume->first_name . ' ' . $resume->last_name)) }}') : this.closest('form').submit();"
                                        @endif>
                                    <i class="fas fa-check"></i>
                                    Approve
                                </button>
                            </form>
                        @endif
                        @if($status !== 'needs_review')
                            @php
                                $disableReject = $isRejected || $isVerified;
                                if ($isRejected) {
                                    $rejectTitle = 'Resume rejected. Waiting for new upload before review.';
                                } elseif ($isVerified) {
                                    $rejectTitle = 'Resume already verified. Reject disabled.';
                                } else {
                                    $rejectTitle = 'Reject this resume';
                                }
                                $rejectOnclick = $disableReject ? 'return false' : "showRejectModal({$resume->id}, '{$resume->first_name} {$resume->last_name}')";
                            @endphp
                            <button class="btn-action btn-reject"
                                    @if($disableReject) disabled @endif
                                    title="{{ $rejectTitle }}"
                                    onclick="{{ $rejectOnclick }}">
                                <i class="fas fa-times"></i>
                                Reject
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-file-pdf"></i>
                        <h3>No Resumes Found</h3>
                        <p>There are no resumes matching your current filters.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 15px; padding: 30px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px; color: #1e293b;">Reject Resume</h2>
        <p style="margin-bottom: 20px; color: #64748b;">Please provide a reason for rejecting <strong id="rejectUserName"></strong>'s resume:</p>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea 
                name="rejection_reason" 
                required
                style="width: 100%; min-height: 120px; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-family: inherit; margin-bottom: 20px;"
                placeholder="e.g., Resume is missing work experience section, education details are incomplete, etc."
            ></textarea>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" style="background: #ef4444;">
                    <i class="fas fa-times"></i>
                    Reject Resume
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Additional visual styling for disabled action buttons
    (function(){
        var style = document.createElement('style');
        style.innerHTML = `
            .btn-action[disabled], .btn[disabled] {
                opacity: 0.55 !important;
                cursor: not-allowed !important;
                filter: grayscale(30%);
            }
            .btn-action[disabled] i, .btn[disabled] i { opacity: 0.6; }
        `;
        document.head.appendChild(style);
    })();
    function showRejectModal(userId, userName) {
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectForm').action = '/admin/resumes/' + userId + '/reject';
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
