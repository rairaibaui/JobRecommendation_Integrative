<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Verifications - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
        }

        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .admin-header p {
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e6ed;
        }

        .tab {
            padding: 1rem 2rem;
            background: white;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tab:hover {
            color: #667eea;
            background: #f8f9fa;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
            background: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
        }

    .stat-card.needs-review { border-left-color: #ef4444; }
    .stat-card.verified { border-left-color: #3B82F6; }
    .stat-card.pending { border-left-color: #F59E0B; }
    .stat-card.rejected { border-left-color: #DC143C; }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        .filter-group select,
        .filter-group input {
            padding: 0.75rem;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            font-size: 0.9rem;
            min-width: 200px;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-1px);
        }

        .verifications-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 1rem;
            border-top: 1px solid #f1f5f9;
        }

        tr:hover {
            background: #f8fafc;
        }

        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

    .badge.verified { background: #DBEAFE; color: #1E3A8A; }
    .badge.needs-review { background: #FEE2E2; color: #7f1d25; }
    .badge.pending { background: #FFF3CD; color: #F59E0B; }
    .badge.rejected { background: #DC143C; color: #ffffff; }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }

        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }

        .btn-info { background: #3b82f6; color: white; }
        .btn-info:hover { background: #2563eb; }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }

        .modal-content h2 {
            margin-bottom: 1rem;
            color: #1e293b;
        }

        .modal-content textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 1rem;
        }

        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-secondary {
            background: #94a3b8;
            color: white;
        }

        .btn-secondary:hover {
            background: #64748b;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 600;
        }

        .score-badge i {
            font-size: 0.875rem;
        }

        .flags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            font-size: 0.75rem;
        }

        .flag-badge {
            background: #fff0f2;
            color: #7f1d25;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-file-alt"></i> Resume Verifications</h1>
        <p>Review and manage AI-verified job seeker resumes</p>
    </div>

    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <a href="{{ route('admin.verifications.index', ['tab' => 'business_permits']) }}" class="tab">
                <i class="fas fa-briefcase"></i>
                Business Permits
                @if($pendingCount > 0)
                    <span class="badge needs-review">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.verifications.index', ['tab' => 'resumes']) }}" class="tab active">
                <i class="fas fa-file-alt"></i>
                Job Seeker Resumes
                @if($resumeNeedsReviewCount > 0)
                    <span class="badge needs-review">{{ $resumeNeedsReviewCount }}</span>
                @endif
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card needs-review">
                <h3>{{ $resumeNeedsReviewCount }}</h3>
                <p><i class="fas fa-exclamation-triangle"></i> Needs Review</p>
            </div>
            <div class="stat-card verified">
                <h3>{{ $resumeVerifiedCount }}</h3>
                <p><i class="fas fa-check-circle"></i> Verified</p>
            </div>
            <div class="stat-card pending">
                <h3>{{ $resumePendingCount }}</h3>
                <p><i class="fas fa-clock"></i> Pending</p>
            </div>
            <div class="stat-card rejected">
                <h3>{{ $resumeRejectedCount }}</h3>
                <p><i class="fas fa-ban"></i> Rejected (Non-Resume)</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.verifications.index') }}" class="filters">
            <input type="hidden" name="tab" value="resumes">
            <div class="filter-group">
                <label>Status</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="needs_review" {{ request('status') == 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary" style="align-self: flex-end;">
                <i class="fas fa-search"></i> Search
            </button>
        </form>

        <!-- Verifications Table -->
        <div class="verifications-table">
            <table>
                <thead>
                    <tr>
                        <th>Job Seeker</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>AI Score</th>
                        <th>Flags</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumeVerifications as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->resume_verification_status === 'verified')
                                    <span class="badge verified"><i class="fas fa-check"></i> Verified</span>
                                @elseif($user->resume_verification_status === 'needs_review')
                                    <span class="badge needs-review"><i class="fas fa-exclamation-triangle"></i> Needs Review</span>
                                @elseif($user->resume_verification_status === 'rejected')
                                    <span class="badge rejected"><i class="fas fa-ban"></i> Rejected</span>
                                @else
                                    <span class="badge pending"><i class="fas fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td>
                                <span class="score-badge" style="color: {{ $user->verification_score >= 70 ? '#10b981' : ($user->verification_score >= 40 ? '#f59e0b' : '#ef4444') }}">
                                    <i class="fas fa-star"></i>
                                    {{ $user->verification_score }}/100
                                </span>
                            </td>
                            <td>
                                @php
                                    $flags = json_decode($user->verification_flags, true) ?? [];
                                @endphp
                                @if(count($flags) > 0)
                                    <div class="flags-list">
                                        @foreach($flags as $flag)
                                            <span class="flag-badge">{{ str_replace('_', ' ', $flag) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #94a3b8;">No issues</span>
                                @endif
                            </td>
                            <td>{{ $user->updated_at->format('M d, Y') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.resumes.details', $user->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($user->resume_verification_status !== 'verified')
                                        <button onclick="openApproveModal({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    @endif
                                    <button onclick="openRejectModal({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                No resumes found matching your criteria
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-check-circle" style="color: #10b981;"></i> Approve Resume</h2>
            <p style="margin-bottom: 1rem;">Approve resume for <strong id="approveName"></strong>?</p>
            <form id="approveForm" method="POST">
                @csrf
                <label>Admin Notes (Optional)</label>
                <textarea name="admin_notes" placeholder="Add any notes about this approval..."></textarea>
                <div class="modal-buttons">
                    <button type="button" onclick="closeApproveModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Resume
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h2><i class="fas fa-times-circle" style="color: #ef4444;"></i> Reject Resume</h2>
            <p style="margin-bottom: 1rem;">Reject resume for <strong id="rejectName"></strong>?</p>
            <form id="rejectForm" method="POST">
                @csrf
                <label>Rejection Reason (Required)</label>
                <textarea name="rejection_reason" required placeholder="Explain why this resume is being rejected..."></textarea>
                <div class="modal-buttons">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Resume
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal(userId, name) {
            document.getElementById('approveName').textContent = name;
            document.getElementById('approveForm').action = `/admin/resumes/${userId}/approve`;
            document.getElementById('approveModal').classList.add('active');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.remove('active');
        }

        function openRejectModal(userId, name) {
            document.getElementById('rejectName').textContent = name;
            document.getElementById('rejectForm').action = `/admin/resumes/${userId}/reject`;
            document.getElementById('rejectModal').classList.add('active');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('active');
        }

        // Close modals on background click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
