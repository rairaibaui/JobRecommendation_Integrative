<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>System Administrator - Business Permit Verifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            color: #1a202c;
        }
        
        /* Top Navigation Bar */
        .top-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 30px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .top-nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 18px;
            color: #2d3748;
        }
        
        .admin-logo i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .admin-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: #f7fafc;
            border-radius: 8px;
        }
        
        .admin-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        
        .admin-details {
            display: flex;
            flex-direction: column;
        }
        
        .admin-name {
            font-weight: 600;
            font-size: 14px;
            color: #2d3748;
        }
        
        .admin-role {
            font-size: 12px;
            color: #718096;
        }
        
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            color: #4a5568;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
            color: #2d3748;
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #718096;
            font-size: 15px;
        }
        
        /* Statistics Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
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
            background: var(--accent-color);
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-card.pending { --accent-color: #f59e0b; }
        .stat-card.approved { --accent-color: #10b981; }
        .stat-card.rejected { --accent-color: #ef4444; }
        .stat-card.ai-analyzed { --accent-color: #8b5cf6; }
        
        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
        }
        
        .stat-card.pending .stat-icon { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
        .stat-card.approved .stat-icon { background: linear-gradient(135deg, #34d399 0%, #10b981 100%); }
        .stat-card.rejected .stat-icon { background: linear-gradient(135deg, #f87171 0%, #ef4444 100%); }
        .stat-card.ai-analyzed .stat-icon { background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%); }
        
        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #1a202c;
            line-height: 1;
        }
        
        .stat-description {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 15px;
            background: #f8f9fa;
            color: #334A5E;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        .badge.approved {
            background: #d4edda;
            color: #155724;
        }
        .badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #648EB5;
            color: white;
        }
        .btn-primary:hover {
            background: #527396;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        .flash-message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .flash-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
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
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
        }
        .modal h3 {
            margin-bottom: 15px;
            color: #334A5E;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Roboto', sans-serif;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="flash-message success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="header">
            <h1><i class="fas fa-check-circle"></i> Business Permit Verifications</h1>
            <p>Review and approve employer business permits</p>
        </div>

        <div class="stats">
            <div class="stat-card pending">
                <div class="label">Pending Review</div>
                <div class="value">{{ $pendingCount }}</div>
            </div>
            <div class="stat-card approved">
                <div class="label">Approved</div>
                <div class="value">{{ $approvedCount }}</div>
            </div>
            <div class="stat-card rejected">
                <div class="label">Rejected</div>
                <div class="value">{{ $rejectedCount }}</div>
            </div>
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 20px; color: #334A5E; font-size: 20px; font-weight: 600;">
                <i class="fas fa-hourglass-half"></i> Pending Verifications
            </h2>

            @if($pendingVerifications->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>All caught up!</h3>
                    <p>No pending verifications at the moment.</p>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Submitted</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVerifications as $validation)
                            <tr>
                                <td>
                                    <strong>{{ $validation->user->company_name ?? 'N/A' }}</strong><br>
                                    <small style="color: #666;">{{ $validation->user->address ?? 'No address' }}</small>
                                </td>
                                <td>{{ $validation->user->first_name }} {{ $validation->user->last_name }}</td>
                                <td>{{ $validation->user->email }}</td>
                                <td>{{ $validation->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ asset('storage/' . $validation->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-file-pdf"></i> View
                                    </a>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.verifications.approve', $validation->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this business permit?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $validation->id }})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-times-circle" style="color: #dc3545;"></i> Reject Business Permit</h3>
            <form id="rejectForm" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 500;">Reason for Rejection:</label>
                    <textarea name="rejection_reason" rows="4" required placeholder="Enter detailed reason..."></textarea>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeRejectModal()" class="btn" style="background: #6c757d; color: white;">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
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
            const flash = document.querySelector('.flash-message');
            if (flash) {
                flash.style.opacity = '0';
                flash.style.transition = 'opacity 0.3s';
                setTimeout(() => flash.remove(), 300);
            }
        }, 3000);
    </script>
</body>
</html>
