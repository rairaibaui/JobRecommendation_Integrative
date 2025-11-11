<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>User Management - Admin Panel</title>
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

        .menu-item {
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: #506B81;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            font-size: 14px;
            position: relative;
        }

        .menu-item:hover {
            background: #F0F4F8;
            color: #2B4053;
        }

        .menu-item.active {
            background: #648EB5;
            color: white;
            font-weight: 600;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
        }

        .notification-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: #EF4444;
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

        /* Main Content */
        .main-content {
            margin-left: 290px;
            flex: 1;
            padding: 20px;
        }

        .admin-header {
            background: white;
            border-radius: 8px;
            padding: 24px 28px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            color: #2B4053;
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-header h1 i {
            font-size: 32px;
            color: #648EB5;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 20px;
            color: white;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #648EB5, #506B81); }
        .stat-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #2B4053;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Filters */
        .filters-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #506B81;
        }

        .filter-group select,
        .filter-group input {
            padding: 10px 14px;
            border: 1.5px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #648EB5;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 20px;
            border: 2px solid #E5E7EB;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #64748b;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: #648EB5;
            color: white;
            border-color: #648EB5;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #F9FAFB;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #506B81;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid #F3F4F6;
            color: #2B4053;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #648EB5, #506B81);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            border: 2px solid;
        }

        .badge-job-seeker {
            background: #DBEAFE;
            color: #1E40AF;
            border-color: #93C5FD;
        }

        .badge-employer {
            background: #FCE7F3;
            color: #9F1239;
            border-color: #F9A8D4;
        }

        .badge-admin {
            background: #F3E8FF;
            color: #6B21A8;
            border-color: #D8B4FE;
        }

        .badge-verified {
            /* Verified -> Blue */
            background: #DBEAFE;
            color: #1E40AF;
            border-color: #93C5FD;
        }

        .badge-pending {
            /* Pending -> Yellow */
            background: #FFF3CD;
            color: #F59E0B;
            border-color: #FFEAA7;
        }

        .badge-needs-review {
            /* Needs review -> Light red */
            background: #FEE2E2;
            color: #7f1d25;
            border-color: #FECACA;
        }

        .badge-rejected {
            /* Rejected -> Crimson */
            background: #DC143C;
            color: #ffffff;
            border-color: #DC143C;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #648EB5;
            color: white;
        }

        .btn-primary:hover {
            background: #506B81;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .pagination {
            display: flex;
            gap: 5px;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            text-decoration: none;
            color: #506B81;
        }

        .pagination .active {
            background: #648EB5;
            color: white;
            border-color: #648EB5;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        .modal-content {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            width: 94%;
            max-width: 750px; /* make modal wider to avoid cramped content */
            position: relative;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.22);
            animation: modalSlideIn 0.28s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border-radius: 50%;
            line-height: 1;
        }

        .close-btn:hover {
            background: rgba(0,0,0,0.2);
            color: #333;
        }

        /* Make modal internal grid work without Bootstrap: two-column layout */
        .modal .row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin: 0 -8px;
        }

        .modal .col-md-6 {
            flex: 1 1 48%;
            min-width: 220px;
            box-sizing: border-box;
            padding: 0 8px;
        }

        /* Card body spacing inside modal */
        .modal .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .modal .card .card-body {
            padding: 18px;
        }

        /* Ensure modal footer and metadata have breathing room */
        #modalUserStatus {
            margin: 12px 0 8px 0;
            display: inline-block;
        }

        .modal .text-center {
            padding: 0 8px;
        }

        .btn-cancel:hover {
            background: #D0D7DD !important;
        }

        .btn-danger:hover {
            background: #c82333 !important;
        }
        /* Modal badge fixes: make status/type badges appear as neutral pills inside modal */
        #modalUserStatus, #modalUserType {
            display: inline-block;
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 600;
            color: #2B4053;
            background: #F3F4F6; /* neutral light */
            border: 1px solid #E5E7EB; /* subtle border */
            box-shadow: none;
            margin: 6px 0;
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
            <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="menu-item">
                <i class="fas fa-shield-check"></i>
                <span>Verifications</span>
                @include('admin.partials.notification-badge')
            </a>
            <a href="{{ route('admin.users.index') }}" class="menu-item active">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="{{ route('admin.audit.index') }}" class="menu-item">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </nav>

            <div style="margin-top: auto; padding-top: 20px;">
                <form method="POST" action="{{ route('logout') }}" onsubmit="return showLogoutModal(this);">
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
        <div class="admin-header">
            <h1>
                <i class="fas fa-users"></i>
                User Management
            </h1>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['job_seekers']) }}</div>
                <div class="stat-label">Job Seekers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['employers']) }}</div>
                <div class="stat-label">Employers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['active_today']) }}</div>
                <div class="stat-label">Active Today</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn {{ $userType === 'all' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['type' => 'all']) }}'">
                All Users
            </button>
            <button class="tab-btn {{ $userType === 'job_seeker' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['type' => 'job_seeker']) }}'">
                Job Seekers
            </button>
            <button class="tab-btn {{ $userType === 'employer' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['type' => 'employer']) }}'">
                Employers
            </button>
            <button class="tab-btn {{ $userType === 'admin' ? 'active' : '' }}" onclick="window.location='{{ route('admin.users.index', ['type' => 'admin']) }}'">
                Admins
            </button>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <input type="hidden" name="type" value="{{ $userType }}">
                <div class="filters-row">
                    <div class="filter-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" placeholder="Name or email..." value="{{ request('search') }}">
                    </div>
                    @if($userType === 'job_seeker')
                    <div class="filter-group">
                        <label for="status">Verification Status</label>
                        <select name="status" id="status">
                            <option value="">All</option>
                            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="needs_review" {{ request('status') === 'needs_review' ? 'selected' : '' }}>Needs Review</option>
                        </select>
                    </div>
                    @endif
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

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Email</th>
                        @if($userType === 'job_seeker')
                        <th>Resume Status</th>
                        @endif
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $user->name }}</div>
                                    <div style="font-size: 12px; color: #64748b;">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->user_type }}">
                                {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                            </span>
                        </td>
                        <td>{{ $user->email }}</td>
                        @if($userType === 'job_seeker')
                        <td>
                            @php $rStatus = $user->resume_verification_status ?? 'pending'; @endphp
                            @if($rStatus === 'verified')
                                <span class="badge badge-verified">Verified</span>
                            @elseif($rStatus === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($rStatus === 'needs_review')
                                <span class="badge badge-needs-review">Needs Review</span>
                            @elseif($rStatus === 'rejected')
                                <span class="badge badge-rejected">Rejected</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        @endif
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" class="btn btn-primary" onclick="openUserDetailsModal({{ $user->id }}, '{{ addslashes($user->first_name ?? $user->name ?? 'N/A') }}', '{{ addslashes($user->last_name ?? '') }}', '{{ $user->user_type ?? 'job_seeker' }}', '{{ addslashes($user->email) }}', '{{ addslashes($user->phone_number ?? 'N/A') }}', '{{ addslashes($user->company_name ?? 'N/A') }}', '{{ addslashes($user->job_title ?? 'N/A') }}', '{{ $user->employment_status ?? 'unemployed' }}', '{{ $user->created_at->format('M d, Y \a\t h:i A') }}', '{{ $user->updated_at->format('M d, Y \a\t h:i A') }}')">
                                     <i class="fas fa-eye"></i>
                                     View
                                 </button>
                                @if($user->user_type !== 'admin')
                                 <button type="button" class="btn btn-danger" onclick="openDeleteUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->user_type }}')">
                                     <i class="fas fa-trash"></i>
                                 </button>
                                 @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                            <i class="fas fa-users" style="font-size: 48px; opacity: 0.3; margin-bottom: 10px; display: block;"></i>
                            No users found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 600px;">
            <button onclick="closeUserDetailsModal()" class="close-btn">&times;</button>

            <!-- Modal Header with Color -->
            <div style="background: linear-gradient(135deg, #648EB5, #506B81); color: white; padding: 20px; border-radius: 16px 16px 0 0; margin: -24px -24px 20px -24px;">
                <div class="text-center">
                    <h2 class="fw-bold mb-2" style="font-size: 28px; margin: 0;" id="modalUserName"></h2>
                    <span class="badge" id="modalUserType" style="font-size: 12px; padding: 6px 12px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);"></span>
                </div>
            </div>

            <!-- Main Content: Two-Column Grid -->
            <div class="row g-4 mb-4">
                <!-- Column 1: Contact Info -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3" style="color: #506B81;">Contact Information</h6>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-envelope" style="color: #648EB5; width: 16px;"></i>
                                    <strong style="font-size: 14px; color: #506B81;">Email:</strong>
                                </div>
                                <div style="font-size: 15px; color: #2B4053; margin-left: 18px; word-break: break-all; word-wrap: break-word;" id="modalUserEmail"></div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-phone" style="color: #648EB5; width: 16px;"></i>
                                    <strong style="font-size: 14px; color: #506B81;">Phone:</strong>
                                </div>
                                <div style="font-size: 15px; color: #2B4053; margin-left: 18px; word-break: break-all; word-wrap: break-word;" id="modalUserPhone"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Company Info -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3" style="color: #506B81;">Company Information</h6>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-building" style="color: #648EB5; width: 16px;"></i>
                                    <strong style="font-size: 14px; color: #506B81;">Company:</strong>
                                </div>
                                <div style="font-size: 15px; color: #2B4053; margin-left: 18px; word-break: break-all; word-wrap: break-word;" id="modalUserCompany"></div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-briefcase" style="color: #648EB5; width: 16px;"></i>
                                    <strong style="font-size: 14px; color: #506B81;">Job Title:</strong>
                                </div>
                                <div style="font-size: 15px; color: #2B4053; margin-left: 18px; word-break: break-all; word-wrap: break-word;" id="modalUserJobTitle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer / Metadata -->
                <hr class="my-4" style="border-color: #E5E7EB; margin-top: 20px;">
            <div class="text-center">
                <div class="mb-3">
                    <span class="badge" id="modalUserStatus" style="font-size: 12px; padding: 6px 12px;"></span>
                </div>
                <div style="font-size: 13px; color: #64748b;">
                    <div><strong>Created At:</strong> <span id="modalUserCreated"></span></div>
                    <div><strong>Last Updated:</strong> <span id="modalUserUpdated"></span></div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="button" onclick="closeUserDetailsModal()" class="btn btn-secondary" style="padding: 10px 20px;">Close</button>
            </div>  
        </div>
    </div>

    <!-- Delete User Modal -->
    <div id="deleteUserModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width: 500px;">
            <button onclick="closeDeleteUserModal()" class="close-btn">&times;</button>
            <h2 style="color: #dc3545; margin-bottom: 20px; font-size: 22px; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> Confirm User Deletion
            </h2>

            <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px; border-radius: 8px; background: #f8d7da; border: 1px solid #f5c2c7; color: #842029;">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Warning:</strong> This action cannot be undone!
            </div>

            <p style="color: #333; margin-bottom: 15px; line-height: 1.6; font-size: 15px;">
                You are about to permanently delete the account for
                <strong id="deleteUserName"></strong>.
            </p>

            <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                This will immediately and permanently remove:
            </p>

            <ul style="margin: 0 0 20px 20px; color: #666; font-size: 14px; line-height: 1.8;">
                <li><strong>All job applications and bookmarks</strong></li>
                <li><strong>Resume and profile information</strong></li>
                <li><strong>All associated data and history</strong></li>
                <li><strong>Any job postings (if employer)</strong></li>
            </ul>

            <form id="deleteUserForm" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <div class="button-group" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeDeleteUserModal()" class="btn-cancel" style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s;">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteUserBtn" class="btn-danger" style="padding: 14px 26px; background: #dc3545; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-trash-alt"></i> Permanently Delete User
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; backdrop-filter: blur(3px);"></div>

    <script>
        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function hideModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.getElementById('modalOverlay').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openUserDetailsModal(userId, firstName, lastName, userType, email, phone, company, jobTitle, status, created, updated) {
            // Populate modal with user data
            document.getElementById('modalUserName').textContent = firstName + ' ' + lastName;
            document.getElementById('modalUserType').textContent = userType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            document.getElementById('modalUserEmail').textContent = email;
            document.getElementById('modalUserPhone').textContent = phone;
            document.getElementById('modalUserCompany').textContent = company;
            document.getElementById('modalUserJobTitle').textContent = jobTitle;
            document.getElementById('modalUserStatus').textContent = status.replace(/\b\w/g, l => l.toUpperCase());

            // Set status badge color
            const statusBadge = document.getElementById('modalUserStatus');
            if (status === 'employed') {
                statusBadge.className = 'badge badge-success';
            } else {
                statusBadge.className = 'badge badge-info';
            }

            // Set user type badge color
            const typeBadge = document.getElementById('modalUserType');
            if (userType === 'employer') {
                typeBadge.style.background = 'rgba(255,255,255,0.2)';
                typeBadge.style.border = '1px solid rgba(255,255,255,0.3)';
            } else if (userType === 'job_seeker') {
                typeBadge.style.background = 'rgba(255,255,255,0.2)';
                typeBadge.style.border = '1px solid rgba(255,255,255,0.3)';
            } else {
                typeBadge.style.background = 'rgba(255,255,255,0.2)';
                typeBadge.style.border = '1px solid rgba(255,255,255,0.3)';
            }

            document.getElementById('modalUserCreated').textContent = created;
            document.getElementById('modalUserUpdated').textContent = updated;

            showModal('userDetailsModal');
        }

        function closeUserDetailsModal() {
            hideModal('userDetailsModal');
        }

        function openDeleteUserModal(userId, userName, userType) {
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserForm').action = '{{ url("/admin/users") }}/' + userId;
            showModal('deleteUserModal');
        }

        function closeDeleteUserModal() {
            hideModal('deleteUserModal');
        }

        // Handle delete confirmation
        document.getElementById('confirmDeleteUserBtn').addEventListener('click', function() {
            if (confirm('Are you absolutely sure? This action CANNOT be undone. All user data will be permanently deleted.')) {
                document.getElementById('deleteUserForm').submit();
            }
        });

        // Close modal when clicking overlay
        document.addEventListener('click', function(e) {
            if (e.target.id === 'modalOverlay') {
                closeUserDetailsModal();
                closeDeleteUserModal();
            }
        });
    </script>
</body>
</html>

@include('partials.logout-confirm')
