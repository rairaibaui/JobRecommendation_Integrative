<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Notifications - Job Recommendation System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #648EB5 0%, #334A5E 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            color: #334A5E;
            font-weight: 600;
        }

        .header .back-btn {
            background: #648EB5;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .header .back-btn:hover {
            background: #334A5E;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #334A5E;
            margin-bottom: 5px;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card.warning .stat-value { color: #ffc107; }
        .stat-card.danger .stat-value { color: #dc3545; }
        .stat-card.info .stat-value { color: #648EB5; }

        .filters-section {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filters-section h3 {
            color: #334A5E;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .filter-group select,
        .filter-group input {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #648EB5;
            box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: #648EB5;
            color: white;
        }

        .btn-primary:hover {
            background: #334A5E;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .notifications-section {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .bulk-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .bulk-actions-left {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-success {
            background: #43A047;
            color: white;
        }

        .btn-success:hover {
            background: #2e7d32;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #bd2130;
        }

        .notifications-table {
            width: 100%;
            border-collapse: collapse;
        }

        .notifications-table thead {
            background: #f8f9fa;
        }

        .notifications-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #334A5E;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }

        .notifications-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #333;
        }

        .notifications-table tr:hover {
            background: #f8f9fa;
        }

        .notifications-table tr.unread {
            background: #fff8e1;
        }

        .notifications-table tr.unread:hover {
            background: #fff3cd;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-unread {
            background: #648EB5;
            color: white;
            margin-left: 5px;
        }

        .notification-message {
            max-width: 500px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-details {
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-btn.mark-read {
            background: #43A047;
            color: white;
        }

        .action-btn.mark-read:hover {
            background: #2e7d32;
        }

        .action-btn.delete {
            background: #dc3545;
            color: white;
        }

        .action-btn.delete:hover {
            background: #bd2130;
        }

        .action-btn.view {
            background: #648EB5;
            color: white;
        }

        .action-btn.view:hover {
            background: #334A5E;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
        }

        .pagination-links {
            display: flex;
            gap: 5px;
        }

        .pagination-links a,
        .pagination-links span {
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            color: #334A5E;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .pagination-links a:hover {
            background: #648EB5;
            color: white;
            border-color: #648EB5;
        }

        .pagination-links .active {
            background: #648EB5;
            color: white;
            border-color: #648EB5;
        }

        .pagination-links .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #334A5E;
        }

        .empty-state p {
            font-size: 14px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .select-all-label {
            font-size: 13px;
            color: #6c757d;
            cursor: pointer;
            user-select: none;
        }

        .select-all-label input {
            margin-right: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📬 Admin Notifications</h1>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">← Back to Dashboard</a>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $totalCount }}</div>
                <div class="stat-label">Total Notifications</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ $unreadCount }}</div>
                <div class="stat-label">Unread</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $warningCount }}</div>
                <div class="stat-label">Expiring Soon</div>
            </div>
            <div class="stat-card danger">
                <div class="stat-value">{{ $errorCount }}</div>
                <div class="stat-label">Expired</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <h3>🔍 Filters & Search</h3>
            <form method="GET" action="{{ route('admin.notifications.index') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="type">Type</label>
                        <select name="type" id="type">
                            <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Expiring Soon</option>
                            <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread Only</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read Only</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" placeholder="Company name, email, message..." value="{{ request('search') }}">
                    </div>

                    <div class="filter-group">
                        <label for="per_page">Per Page</label>
                        <select name="per_page" id="per_page">
                            <option value="10" {{ request('per_page', 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 25) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Notifications Table -->
        <div class="notifications-section">
            @if(session('success'))
                <div class="alert alert-success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <div class="bulk-actions">
                <div class="bulk-actions-left">
                    <label class="select-all-label">
                        <input type="checkbox" id="selectAll">
                        Select All
                    </label>
                    <form method="POST" action="{{ route('admin.notifications.bulkMarkRead') }}" id="bulkMarkReadForm" style="display: inline;">
                        @csrf
                        <input type="hidden" name="ids" id="bulkMarkReadIds">
                        <button type="submit" class="btn btn-success" id="bulkMarkReadBtn" disabled>
                            ✓ Mark Selected as Read
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.notifications.bulkDelete') }}" id="bulkDeleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="ids" id="bulkDeleteIds">
                        <button type="submit" class="btn btn-danger" id="bulkDeleteBtn" disabled onclick="return confirm('Are you sure you want to delete the selected notifications?')">
                            🗑️ Delete Selected
                        </button>
                    </form>
                </div>
                <div>
                    <form method="POST" action="{{ route('admin.notifications.markAllRead') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">✓ Mark All as Read</button>
                    </form>
                </div>
            </div>

            @if($notifications->count() > 0)
                <table class="notifications-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" id="selectAllHeader">
                            </th>
                            <th>Type</th>
                            <th>Company / Message</th>
                            <th>Expiry Date</th>
                            <th>Received</th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data ?? []; // Already cast as array in model
                                $isUnread = !$notification->read;
                            @endphp
                            <tr class="{{ $isUnread ? 'unread' : '' }}">
                                <td>
                                    <input type="checkbox" class="notification-checkbox" value="{{ $notification->id }}">
                                </td>
                                <td>
                                    <span class="badge badge-{{ $notification->type == 'warning' ? 'warning' : 'danger' }}">
                                        {{ $notification->type == 'warning' ? '⚠️ Expiring' : '🚨 Expired' }}
                                    </span>
                                    @if($isUnread)
                                        <span class="badge badge-unread">New</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $data['company_name'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="notification-message">{{ $notification->message }}</div>
                                    <div class="notification-details">{{ $data['email'] ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    @if(isset($data['expiry_date']))
                                        {{ \Carbon\Carbon::parse($data['expiry_date'])->format('M d, Y') }}
                                    @else
                                        <span style="color: #6c757d;">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $notification->created_at->diffForHumans() }}
                                    <div class="notification-details">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="actions" style="justify-content: center;">
                                        @if(isset($data['validation_id']))
                                            <a href="{{ route('admin.verifications.index') }}?id={{ $data['validation_id'] }}" class="action-btn view" title="View Verification">
                                                👁️ View
                                            </a>
                                        @endif
                                        
                                        @if($isUnread)
                                            <form method="POST" action="{{ route('admin.notifications.markRead', $notification->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-btn mark-read" title="Mark as Read">
                                                    ✓ Read
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('admin.notifications.destroy', $notification->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this notification?')">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info">
                        Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} notifications
                    </div>
                    <div class="pagination-links">
                        {{ $notifications->links('pagination::simple-default') }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                    </svg>
                    <h3>No Notifications Found</h3>
                    <p>{{ request()->hasAny(['type', 'status', 'search']) ? 'Try adjusting your filters.' : 'You don\'t have any notifications yet.' }}</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Defensive DOM-ready binding to avoid null addEventListener errors
        (function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllHeader = document.getElementById('selectAllHeader');
            const checkboxes = document.querySelectorAll('.notification-checkbox');
            const bulkMarkReadBtn = document.getElementById('bulkMarkReadBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkMarkReadIds = document.getElementById('bulkMarkReadIds');
            const bulkDeleteIds = document.getElementById('bulkDeleteIds');
            const bulkMarkReadForm = document.getElementById('bulkMarkReadForm');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');

            function updateBulkActions() {
                const selectedIds = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                const hasSelection = selectedIds.length > 0;
                if (bulkMarkReadBtn) bulkMarkReadBtn.disabled = !hasSelection;
                if (bulkDeleteBtn) bulkDeleteBtn.disabled = !hasSelection;

                if (bulkMarkReadIds) bulkMarkReadIds.value = JSON.stringify(selectedIds);
                if (bulkDeleteIds) bulkDeleteIds.value = JSON.stringify(selectedIds);
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    if (selectAllHeader) selectAllHeader.checked = this.checked;
                    updateBulkActions();
                });
            }

            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    if (selectAllCheckbox) selectAllCheckbox.checked = this.checked;
                    updateBulkActions();
                });
            }

            if (checkboxes && checkboxes.length) {
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', function() {
                        const allChecked = Array.from(checkboxes).every(c => c.checked);
                        if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
                        if (selectAllHeader) selectAllHeader.checked = allChecked;
                        updateBulkActions();
                    });
                });
            }

            if (bulkMarkReadForm) {
                bulkMarkReadForm.addEventListener('submit', function(e) {
                    const selectedIds = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    
                    if (selectedIds.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one notification.');
                        return;
                    }

                    // Convert to array format for Laravel validation
                    const singleIds = this.querySelector('input[name="ids"]');
                    if (singleIds) singleIds.remove();
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        this.appendChild(input);
                    });
                });
            }

            if (bulkDeleteForm) {
                bulkDeleteForm.addEventListener('submit', function(e) {
                    const selectedIds = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    
                    if (selectedIds.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one notification.');
                        return;
                    }

                    // Convert to array format for Laravel validation
                    const singleIds = this.querySelector('input[name="ids"]');
                    if (singleIds) singleIds.remove();
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        this.appendChild(input);
                    });
                });
            }

            // Initialize state once on load
            updateBulkActions();
        })();
    </script>
</body>
</html>
