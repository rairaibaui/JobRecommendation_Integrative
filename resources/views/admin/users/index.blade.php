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
            border-bottom: 1px solid #E5E7EB;
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
            font-size: 16px;
            font-weight: 600;
            color: #2B4053;
            text-align: center;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
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
            background: #D4EDDA;
            color: #155724;
            border-color: #C3E6CB;
        }

        .badge-pending {
            background: #FFF3CD;
            color: #856404;
            border-color: #FFEAA7;
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

            <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #E5E7EB;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="menu-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
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
                            @if($user->resume_verification_status === 'verified')
                                <span class="badge badge-verified">Verified</span>
                            @elseif($user->resume_verification_status === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @else
                                <span class="badge" style="background: #F8D7DA; color: #721C24; border-color: #F5C6CB;">Needs Review</span>
                            @endif
                        </td>
                        @endif
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-primary">
                                    <i class="fas fa-eye"></i>
                                    View
                                </a>
                                @if($user->user_type !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
</body>
</html>
