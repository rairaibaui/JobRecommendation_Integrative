<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Administrator - Business Permit Verifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            color: #1e293b;
        }
        
        /* Top Navigation */
        .top-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 20px;
            color: #0f172a;
        }
        
        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        
        .profile-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 15px;
        }
        
        .profile-info {
            display: flex;
            flex-direction: column;
        }
        
        .profile-name {
            font-weight: 600;
            font-size: 14px;
            color: #0f172a;
        }
        
        .profile-role {
            font-size: 12px;
            color: #64748b;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            color: #475569;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e0;
            color: #0f172a;
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        
        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #64748b;
            font-size: 15px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        
        .alert i {
            font-size: 18px;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .stat-card {
            background: white;
            padding: 26px;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--card-accent);
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        
        .stat-card.pending { --card-accent: #f59e0b; }
        .stat-card.approved { --card-accent: #10b981; }
        .stat-card.rejected { --card-accent: #ef4444; }
        .stat-card.ai { --card-accent: #8b5cf6; }
        
        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }
        
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .stat-card.pending .stat-icon { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .stat-card.approved .stat-icon { background: linear-gradient(135deg, #34d399, #10b981); }
        .stat-card.rejected .stat-icon { background: linear-gradient(135deg, #f87171, #ef4444); }
        .stat-card.ai .stat-icon { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
        
        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 40px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .stat-desc {
            font-size: 13px;
            color: #94a3b8;
        }
        
        /* AI Detection Toggle */
        .ai-toggle-card {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
            color: white;
            padding: 24px;
            border-radius: 14px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }
        
        .ai-toggle-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .ai-toggle-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 16px;
        }
        
        .toggle-switch {
            position: relative;
            width: 56px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.3);
            transition: 0.3s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background: rgba(16, 185, 129, 0.9);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }
        
        .ai-toggle-desc {
            font-size: 13px;
            opacity: 0.95;
            line-height: 1.6;
        }
        
        /* Verifications Table */
        .verifications-section {
            background: white;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .section-header {
            padding: 24px 28px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title i {
            color: #f59e0b;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8fafc;
        }
        
        th {
            text-align: left;
            padding: 18px 20px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        td {
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }
        
        tbody tr {
            transition: all 0.2s;
        }
        
        tbody tr:hover {
            background: #f8fafc;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.ai-verified {
            background: #ede9fe;
            color: #6d28d9;
        }
        
        .badge.system-flagged {
            background: #fef3c7;
            color: #b45309;
        }
        
        .badge.duplicate {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Company Info */
        .company-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .company-name {
            font-weight: 600;
            color: #0f172a;
        }
        
        .company-address {
            font-size: 13px;
            color: #64748b;
        }
        
        /* Action Buttons */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-view {
            background: #e0e7ff;
            color: #4338ca;
        }
        
        .btn-view:hover {
            background: #c7d2fe;
        }
        
        .btn-approve {
            background: #d1fae5;
            color: #065f46;
        }
        
        .btn-approve:hover {
            background: #a7f3d0;
        }
        
        .btn-reject {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-reject:hover {
            background: #fecaca;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        /* AI Analysis Badge */
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
        
        /* Duplicate Warning */
        .duplicate-warning {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            color: #b45309;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 64px;
            color: #cbd5e0;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        
        /* Modal */
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
        
        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 16px;
            max-width: 520px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .modal-header i {
            font-size: 28px;
            color: #ef4444;
        }
        
        .modal-header h3 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 14px;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            resize: vertical;
            font-size: 14px;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #6366f1;
        }
        
        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
        }
        
        .btn-cancel:hover {
            background: #e2e8f0;
        }
        
        .btn-submit {
            background: #ef4444;
            color: white;
        }
        
        .btn-submit:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="nav-left">
            <div class="admin-brand">
                <div class="brand-icon">
                    <i class="fas fa-shield-check"></i>
                </div>
                <span>System Administrator</span>
            </div>
        </div>
        
        <div class="nav-right">
            <div class="admin-profile">
                <div class="profile-avatar">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1)) }}
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'User' }}</div>
                    <div class="profile-role">System Administrator</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        @if(session('success'))
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="page-header">
            <h1 class="page-title">Business Permit Verifications</h1>
            <p class="page-subtitle">AI-powered document verification and duplicate detection system</p>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-label">Pending Review</div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-desc">Awaiting manual verification</div>
            </div>

            <div class="stat-card approved">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-label">Approved</div>
                <div class="stat-value">{{ $approvedCount }}</div>
                <div class="stat-desc">Valid permits verified</div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-label">Rejected</div>
                <div class="stat-value">{{ $rejectedCount }}</div>
                <div class="stat-desc">Invalid or expired permits</div>
            </div>

            <div class="stat-card ai">
                <div class="stat-top">
                    <div class="stat-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                <div class="stat-label">AI Analyzed</div>
                <div class="stat-value">{{ $approvedCount + $rejectedCount }}</div>
                <div class="stat-desc">Processed by AI system</div>
            </div>
        </div>

        <!-- AI Detection Toggle -->
        <div class="ai-toggle-card">
            <div class="ai-toggle-header">
                <div class="ai-toggle-title">
                    <i class="fas fa-brain"></i>
                    Enhanced AI Detection
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="enhancedAI" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="ai-toggle-desc">
                Advanced content analysis to detect re-scanned, altered, or format-shifted permits across different file types. The AI extracts permit numbers and validates authenticity even when documents are photographed or converted.
            </div>
        </div>

        <!-- Verifications Table -->
        <div class="verifications-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tasks"></i>
                    Pending Verifications
                </h2>
            </div>

            @if($pendingVerifications->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>All Caught Up!</h3>
                    <p>No pending verifications at the moment</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>AI Analysis</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVerifications as $validation)
                                <tr>
                                    <td>
                                        <div class="company-info">
                                            <div class="company-name">{{ $validation->user->company_name ?? 'N/A' }}</div>
                                            <div class="company-address">{{ Str::limit($validation->user->address ?? 'No address', 40) }}</div>
                                        </div>
                                    </td>
                                    <td>{{ $validation->user->first_name }} {{ $validation->user->last_name }}</td>
                                    <td>{{ $validation->user->email }}</td>
                                    <td>
                                        @if($validation->validated_by === 'ai')
                                            <span class="badge ai-verified">
                                                <i class="fas fa-robot"></i>
                                                AI Verified
                                            </span>
                                        @else
                                            <span class="badge system-flagged">
                                                <i class="fas fa-flag"></i>
                                                System Flagged
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($validation->ai_analysis['duplicate_detection']))
                                            <span class="duplicate-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Duplicate Detected
                                            </span>
                                        @else
                                            <span class="badge ai-verified">Ready for Review</span>
                                        @endif
                                    </td>
                                    <td>{{ $validation->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ asset('storage/' . $validation->file_path) }}" target="_blank" class="btn btn-view">
                                                <i class="fas fa-file-pdf"></i>
                                                View
                                            </a>
                                            <form method="POST" action="{{ route('admin.verifications.approve', $validation->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-approve" onclick="return confirm('Approve this business permit?')">
                                                    <i class="fas fa-check"></i>
                                                    Approve
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $validation->id }})" class="btn btn-reject">
                                                <i class="fas fa-times"></i>
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-times-circle"></i>
                <h3>Reject Business Permit</h3>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Reason for Rejection:</label>
                    <textarea name="rejection_reason" rows="5" required placeholder="Enter detailed reason for rejection..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-cancel">Cancel</button>
                    <button type="submit" class="btn btn-submit">Reject Permit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(id) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/verifications/${id}/reject`;
            modal.style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }

        // Auto-hide success messages
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => alert.remove(), 300);
            }
        }, 4000);

        // Enhanced AI toggle
        document.getElementById('enhancedAI').addEventListener('change', function() {
            console.log('Enhanced AI Detection:', this.checked ? 'Enabled' : 'Disabled');
            // You can add AJAX call here to update preference
        });
    </script>
</body>
</html>
