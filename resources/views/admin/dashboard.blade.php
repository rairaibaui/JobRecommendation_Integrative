<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Admin Dashboard - Job Recommendation System</title>
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

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .admin-header {
            background: white;
            border-radius: 8px;
            padding: 24px 28px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .admin-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            font-size: 14px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #648EB5, #506B81);
            color: white;
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .stat-icon.indigo {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
        }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-card-icon.blue { background: linear-gradient(135deg, #648EB5, #506B81); }
        .stat-card-icon.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card-icon.orange { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .stat-card-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stat-card h3 {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-value {
            font-size: 36px;
            font-weight: 700;
            color: #2B4053;
            margin-bottom: 10px;
        }

        .stat-card-subtitle {
            color: #64748b;
            font-size: 13px;
        }

        .section {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
        }

        .section-header h2 {
            color: #2B4053;
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #F9FAFB;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #506B81;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px 12px;
            border-bottom: 1px solid #E5E7EB;
            color: #64748b;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success { background: #D4EDDA; color: #155724; }
        .badge-warning { background: #FFF3CD; color: #856404; }
        .badge-danger { background: #F8D7DA; color: #721C24; }
        .badge-info { background: #D1ECF1; color: #0c5460; }
        .badge-secondary { background: #E9ECEF; color: #495057; }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            transition: width 0.5s;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        @media (max-width: 968px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                gap: 15px;
            }
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .quick-action-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .quick-action-card i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }

        .quick-action-card h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .quick-action-card p {
            font-size: 13px;
            opacity: 0.9;
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .admin-header {
                flex-direction: column;
                gap: 15px;
            }

            .admin-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
            <a href="{{ route('admin.dashboard') }}" class="menu-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="menu-item">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="menu-item">
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
            <form method="POST" action="{{ route('logout') }}">
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
        <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>
                <i class="fas fa-shield-alt"></i>
                Admin Dashboard
            </h1>
            <div class="admin-actions">
                @if($adminUnreadNotifications > 0)
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-bell"></i>
                        {{ $adminUnreadNotifications }} Notifications
                    </a>
                @endif
                <a href="{{ route('admin.verifications.unified') }}" class="btn btn-primary">
                    <i class="fas fa-tasks"></i>
                    Verifications
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if($resumesNeedingReview > 0 || $pendingPermits > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
                <div>
                    <strong>Action Required:</strong>
                    @if($resumesNeedingReview > 0)
                        {{ $resumesNeedingReview }} resume(s) need your review.
                    @endif
                    @if($pendingPermits > 0)
                        {{ $pendingPermits }} business permit(s) pending verification.
                    @endif
                    <a href="{{ route('admin.verifications.index') }}" style="color: #856404; text-decoration: underline; margin-left: 10px;">Review Now</a>
                </div>
            </div>
        @endif

        @if($expiringSoonPermits > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle" style="font-size: 20px;"></i>
                <div>
                    <strong>Notice:</strong> {{ $expiringSoonPermits }} business permit(s) expiring within 30 days.
                </div>
            </div>
        @endif

        <!-- Overview Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Total Users</h3>
                    <div class="stat-card-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-card-subtitle">
                    {{ number_format($newUsersThisMonth) }} new this month
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Job Seekers</h3>
                    <div class="stat-card-icon green">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($totalJobSeekers) }}</div>
                <div class="stat-card-subtitle">
                    {{ number_format($verifiedResumes) }} verified resumes
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Employers</h3>
                    <div class="stat-card-icon orange">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($totalEmployers) }}</div>
                <div class="stat-card-subtitle">
                    {{ number_format($verifiedEmployers) }} verified
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Job Postings</h3>
                    <div class="stat-card-icon purple">
                        <i class="fas fa-briefcase"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($totalJobPostings) }}</div>
                <div class="stat-card-subtitle">
                    {{ number_format($activeJobPostings) }} active
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Applications</h3>
                    <div class="stat-card-icon red">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($totalApplications) }}</div>
                <div class="stat-card-subtitle">
                    {{ number_format($applicationsThisWeek) }} this week
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <h3>Pending Reviews</h3>
                    <div class="stat-card-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card-value">{{ number_format($resumesNeedingReview + $pendingPermits) }}</div>
                <div class="stat-card-subtitle">
                    Resumes: {{ $resumesNeedingReview }} | Permits: {{ $pendingPermits }}
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="section">
            <div class="section-header">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            </div>
            <div class="quick-actions">
                <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes', 'status' => 'needs_review']) }}" class="quick-action-card">
                    <i class="fas fa-file-pdf"></i>
                    <h3>Review Resumes</h3>
                    <p>{{ $resumesNeedingReview }} waiting</p>
                </a>
                <a href="{{ route('admin.verifications.unified', ['tab' => 'permits', 'status' => 'pending_review']) }}" class="quick-action-card">
                    <i class="fas fa-certificate"></i>
                    <h3>Review Permits</h3>
                    <p>{{ $pendingPermits }} waiting</p>
                </a>
                <a href="{{ route('admin.verifications.unified') }}" class="quick-action-card">
                    <i class="fas fa-tasks"></i>
                    <h3>All Verifications</h3>
                    <p>Manage all</p>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="quick-action-card">
                    <i class="fas fa-bell"></i>
                    <h3>Notifications</h3>
                    <p>{{ $adminUnreadNotifications }} unread</p>
                </a>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid-2">
            <!-- Job Seeker Verification Status -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-user-graduate"></i> Job Seeker Resumes</h2>
                    <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="btn btn-primary">
                        View All
                    </a>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 14px; color: #495057;">With Resume</span>
                        <span style="font-size: 14px; font-weight: 600; color: #212529;">{{ $jobSeekersWithResume }} / {{ $totalJobSeekers }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $totalJobSeekers > 0 ? ($jobSeekersWithResume / $totalJobSeekers * 100) : 0 }}%"></div>
                    </div>
                </div>

                <table class="table">
                    <tr>
                        <td><span class="badge badge-success">Verified</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($verifiedResumes) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-warning">Needs Review</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($resumesNeedingReview) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-info">Pending</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($pendingResumes) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-danger">Rejected</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($rejectedResumes) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-secondary">No Resume</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($jobSeekersWithoutResume) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Employer Verification Status -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-building"></i> Employer Permits</h2>
                    <a href="{{ route('admin.verifications.unified', ['tab' => 'permits']) }}" class="btn btn-primary">
                        View All
                    </a>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span style="font-size: 14px; color: #495057;">Verified Employers</span>
                        <span style="font-size: 14px; font-weight: 600; color: #212529;">{{ $verifiedEmployers }} / {{ $totalEmployers }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $totalEmployers > 0 ? ($verifiedEmployers / $totalEmployers * 100) : 0 }}%"></div>
                    </div>
                </div>

                <table class="table">
                    <tr>
                        <td><span class="badge badge-success">Approved</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($verifiedEmployers) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-warning">Pending Review</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($pendingPermits) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-danger">Rejected</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($rejectedPermits) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge badge-info">Expiring Soon</span></td>
                        <td style="text-align: right; font-weight: 600;">{{ number_format($expiringSoonPermits) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid-2">
            <!-- Recent Users -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-user-plus"></i> Recent Users</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>
                                    <span class="user-avatar">{{ strtoupper(substr($user->first_name ?? $user->email, 0, 1)) }}</span>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </td>
                                <td>
                                    <span class="badge {{ $user->user_type === 'employer' ? 'badge-info' : 'badge-success' }}">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #868e96;">No recent users</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Recent Applications -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-paper-plane"></i> Recent Applications</h2>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $application)
                            <tr>
                                <td>{{ $application->user->first_name ?? 'N/A' }} {{ $application->user->last_name ?? '' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($application->status) {
                                            'accepted' => 'badge-success',
                                            'rejected' => 'badge-danger',
                                            'reviewing' => 'badge-info',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($application->status) }}</span>
                                </td>
                                <td>{{ $application->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #868e96;">No recent applications</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="section">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Recent Admin Activity</h2>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAuditLogs as $log)
                        <tr>
                            <td>
                                <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($log->event)) }}</span>
                            </td>
                            <td>{{ $log->title }}</td>
                            <td style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $log->description }}
                            </td>
                            <td>{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No recent activity</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <!-- End Main Content -->
</body>
</html>
