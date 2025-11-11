<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verification Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 16px;
        }

        body {
            width: 100%;
            min-height: 100vh;
            display: flex;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
            padding: 0;
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 20px;
            top: 20px;
            width: 250px;
            height: calc(100vh - 40px);
            border-radius: 8px;
            background: #FFF;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .profile-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 20px;
        }

        .profile-ellipse {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .profile-ellipse img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .profile-icon {
            font-size: 28px;
            color: #2B4053;
        }

        .profile-name {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #2B4053;
            text-align: center;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            background: #648EB5;
            color: white;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* Sidebar button styles (match job seeker layout) */
        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            height: 44px;
            padding: 0 14px;
            border-radius: 10px;
            background: transparent;
            box-shadow: none;
            color: #334A5E;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #648EB5;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            background: linear-gradient(90deg, #e8f4fd 0%, #f0f7fc 100%);
            color: #2B4053;
            transform: translateX(4px);
        }

        .menu-item:hover::before {
            transform: scaleY(1);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
            color: #FFF;
            font-weight: 600;
        }

        .menu-item.active::before {
            display: none;
        }

        .menu-item.active:hover {
            transform: translateX(0);
            box-shadow: 0 6px 16px rgba(100, 142, 181, 0.4);
        }

        .menu-item i {
            font-size: 18px;
            min-width: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .menu-item:hover i {
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: #DC143C;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
            line-height: 1.4;
        }

        .menu-item.active .notification-badge {
            background: #DC2626;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 290px;
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .admin-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Admin Header */
        .admin-header {
            background: white;
            border-radius: 8px;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #2B4053;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-header h1 i {
            color: #648EB5;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #648EB5;
            color: white;
        }

        .btn-primary:hover {
            background: #506B81;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #506B81;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
            transform: translateY(-1px);
        }

        /* Page Header */
        .page-header {
            background: white;
            border-radius: 8px;
            padding: 24px 28px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #2B4053;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        /* Card Container */
        .card {
            background: white;
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Tabs */
        .tabs-container {
            border-bottom: 2px solid #E5E7EB;
        }

        .tabs {
            display: flex;
        }

        .tab {
            flex: 1;
            padding: 18px 24px;
            text-align: center;
            cursor: pointer;
            background: white;
            border: none;
            font-size: 15px;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        .tab:hover {
            background: #F9FAFB;
            color: #334155;
        }

        .tab.active {
            color: #648EB5;
            background: #F8FAFC;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: #648EB5;
        }

        .tab-badge {
            display: inline-block;
            background: #fff0f2;
            color: #7f1d25;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 8px;
        }

        .tab.active .tab-badge {
            background: #648EB5;
            color: white;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            border-radius: 8px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .filters-row {
            display: grid;
            grid-template-columns: 1fr 2fr auto;
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #506B81;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-group select,
        .filter-group input {
            padding: 11px 14px;
            border: 1.5px solid #D1D5DB;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            transition: all 0.2s;
            background: white;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #648EB5;
            box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
        }

        .btn {
            padding: 11px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
        }

        .btn-primary {
            background: #648EB5;
            color: white;
        }

        .btn-primary:hover {
            background: #506B81;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #F0F4F8;
            color: #506B81;
            border: 1.5px solid #D1D5DB;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
        }

        /* Content Section */
        .content-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-container {
            max-height: 600px;
            overflow-y: auto;
            position: relative;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #F9FAFB;
        }

        .table th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            color: #506B81;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #E5E7EB;
            font-family: 'Poppins', sans-serif;
        }

        .table td {
            padding: 16px;
            border-bottom: 1px solid #F3F4F6;
            color: #334155;
            font-size: 14px;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background: #F9FAFB;
        }

        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding: 20px 24px;
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #2B4053;
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
            font-weight: 600;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            position: relative;
            cursor: help;
            border: 2px solid;
        }

        .status-badge i {
            font-size: 11px;
        }

        .status-verified {
            background: #DBEAFE;
            color: #1E3A8A;
            border-color: #BFDBFE;
        }

        .status-pending {
            background: #FFF3CD;
            color: #F59E0B;
            border-color: #FFEAA7;
        }

        .status-needs-review {
            background: #FEE2E2;
            color: #7f1d25;
            border-color: #F5C6CB;
        }

        .status-rejected {
            background: #fff0f2;
            color: #7f1d25;
            border-color: #DC143C;
        }

        .status-approved {
            background: #D4EDDA;
            color: #155724;
            border-color: #C3E6CB;
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 250px;
            background-color: #2B4053;
            color: #fff;
            text-align: left;
            border-radius: 8px;
            padding: 12px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            margin-left: -125px;
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
            line-height: 1.5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .tooltip .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #2B4053 transparent transparent transparent;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* AI Score Indicator */
        .ai-score {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .score-bar {
            width: 60px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s;
        }

        .score-fill.high { background: #10b981; }
        .score-fill.medium { background: #f59e0b; }
        .score-fill.low { background: #ef4444; }

        .score-text {
            font-size: 13px;
            font-weight: 600;
            min-width: 40px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #648EB5;
            color: white;
        }

        .btn-view:hover {
            background: #506B81;
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #64748b;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #648EB5 0%, #506B81 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .user-email {
            font-size: 12px;
            color: #64748b;
        }

        /* Flags Display */
        .flags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .flag-item {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .loading i {
            font-size: 32px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .filters-row {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }

        /* Stats Summary */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e2e8f0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
            }

            .filters-row {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-section">
            <div class="profile-ellipse">
                @if(Auth::user()->profile_picture)
                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Admin">
                @else
                    <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; font-size: 24px; font-weight: 600; color: white;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ Auth::user()->name }}</div>
                <div class="admin-badge">System Admin</div>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="{{ route('admin.dashboard') }}" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="menu-item active">
                <i class="fas fa-check-circle"></i>
                <span>Verifications</span>
                @include('admin.partials.notification-badge')
            </a>
            <a href="{{ route('admin.users.index') }}" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.audit.index') }}" class="menu-item">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </nav>

        <div style="margin-top: auto; padding-top: 20px;">
            <form method="POST" action="{{ route('logout') }}>
                @csrf
                <button type="submit" class="menu-item" style="width: 100%; background: #648EB5; color: white; border: none; cursor: pointer; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="admin-header">
            <h1>
                <i class="fas fa-check-circle"></i>
                Verification Management
            </h1>
        </div>

        <!-- Tabs -->
        <div class="tabs-container card">
            <div class="tabs">
                <button class="tab {{ $activeTab === 'resumes' ? 'active' : '' }}" onclick="switchTab('resumes')">
                    <i class="fas fa-file-pdf"></i>
                    Job Seeker Resumes
                    @if($resumeStats['needs_review'] > 0)
                        <span class="tab-badge">{{ $resumeStats['needs_review'] }}</span>
                    @endif
                </button>
                <button class="tab {{ $activeTab === 'permits' ? 'active' : '' }}" onclick="switchTab('permits')">
                    <i class="fas fa-certificate"></i>
                    Business Permits
                    @if($permitStats['pending'] > 0)
                        <span class="tab-badge">{{ $permitStats['pending'] }}</span>
                    @endif
                </button>
            </div>

            <!-- Stats Summary -->
            <div class="stats-summary">
                @if($activeTab === 'resumes')
                    <div class="stat-item">
                        <div class="stat-value">{{ $resumeStats['total'] }}</div>
                        <div class="stat-label">Total Resumes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #3B82F6;">{{ $resumeStats['verified'] }}</div>
                        <div class="stat-label">Verified</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #f59e0b;">{{ $resumeStats['needs_review'] }}</div>
                        <div class="stat-label">Needs Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #6b7280;">{{ $resumeStats['pending'] }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                        <div class="stat-item">
                            <div class="stat-value" style="color: #DC143C;">{{ $resumeStats['rejected'] ?? 0 }}</div>
                            <div class="stat-label">Rejected</div>
                        </div>
                @else
                    <div class="stat-item">
                        <div class="stat-value">{{ $permitStats['total'] }}</div>
                        <div class="stat-label">Total Permits</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #10b981;">{{ $permitStats['approved'] }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #f59e0b;">{{ $permitStats['pending'] }}</div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" style="color: #ef4444;">{{ $permitStats['rejected'] }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.verifications.unified') }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            @if($activeTab === 'resumes')
                                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="needs_review" {{ request('status') === 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            @else
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            @endif
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input 
                            type="text" 
                            name="search" 
                            id="search" 
                            placeholder="Search by name or email..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Content -->
        <div class="content-section">
            <div class="table-container">
                @if($activeTab === 'resumes')
                    @include('admin.partials.resume-table', ['resumes' => $items])
                @else
                    @include('admin.partials.permit-table', ['permits' => $items])
                @endif
            </div>
        </div>
    </div>
    <!-- End Main Content -->

    <script>
        function switchTab(tab) {
            window.location.href = '{{ route("admin.verifications.unified") }}?tab=' + tab;
        }
    </script>
</body>
</html>
