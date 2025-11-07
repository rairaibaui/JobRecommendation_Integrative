<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Analytics - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-size: 16px; }
        body { width: 100%; min-height: 100vh; display: flex; font-family: 'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); }
        .sidebar { position: fixed; left: 20px; top: 20px; width: 250px; height: calc(100vh - 40px); border-radius: 8px; background: #FFF; padding: 20px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .profile-section { display: flex; flex-direction: column; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #E5E7EB; }
        .profile-ellipse { width: 62px; height: 62px; border-radius: 50%; background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 12px; }
        .profile-ellipse img { width: 100%; height: 100%; object-fit: cover; }
        .profile-info { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .profile-name { font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 600; color: #2B4053; text-align: center; }
        .admin-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 20px; background: #648EB5; color: white; }
        .sidebar-menu { display: flex; flex-direction: column; gap: 8px; }
        .menu-item { padding: 12px 16px; border-radius: 8px; text-decoration: none; color: #506B81; font-weight: 500; display: flex; align-items: center; gap: 12px; transition: all 0.2s; font-size: 14px; position: relative; }
        .menu-item:hover { background: #F0F4F8; color: #2B4053; }
        .menu-item.active { background: #648EB5; color: white; font-weight: 600; }
        .menu-item i { width: 20px; text-align: center; }
        .notification-badge { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #EF4444; color: white; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 12px; min-width: 20px; text-align: center; line-height: 1.4; }
        .menu-item.active .notification-badge { background: #DC2626; }
        .main-content { margin-left: 290px; flex: 1; padding: 20px; }
        .admin-header { background: white; border-radius: 8px; padding: 24px 28px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { color: #2B4053; font-family: 'Poppins', sans-serif; font-size: 28px; font-weight: 800; display: flex; align-items: center; gap: 12px; }
        .filters { background: white; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; gap: 12px; align-items: center; }
        .filters label { font-weight: 600; color: #506B81; font-size: 13px; }
        .filters select { padding: 10px 14px; border: 1.5px solid #D1D5DB; border-radius: 8px; font-size: 14px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card h3 { font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .big { grid-column: span 2; }
        canvas { width: 100% !important; height: 260px !important; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; } .big { grid-column: span 1; } }
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
                    <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:24px;font-weight:600;color:white;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
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
            <a href="{{ route('admin.analytics.index') }}" class="menu-item active">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="menu-item">
                <i class="fas fa-shield-check"></i>
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
        <div style="margin-top:auto;padding-top:20px;border-top:1px solid #E5E7EB;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="admin-header">
            <h1><i class="fas fa-chart-line"></i> Analytics</h1>
            <form class="filters" method="GET" action="{{ route('admin.analytics.index') }}">
                <label for="range">Range</label>
                <select name="range" id="range" onchange="this.form.submit()">
                    <option value="7" {{ $range==7 ? 'selected' : '' }}>7 days</option>
                    <option value="14" {{ $range==14 ? 'selected' : '' }}>14 days</option>
                    <option value="30" {{ $range==30 ? 'selected' : '' }}>30 days</option>
                    <option value="60" {{ $range==60 ? 'selected' : '' }}>60 days</option>
                    <option value="90" {{ $range==90 ? 'selected' : '' }}>90 days</option>
                </select>
            </form>
        </div>

        <div class="grid">
            <div class="card big">
                <h3>User Growth ({{ $range }} days)</h3>
                <canvas id="userGrowthChart"></canvas>
            </div>
            <div class="card">
                <h3>User Distribution</h3>
                <canvas id="userDistChart"></canvas>
            </div>
            <div class="card">
                <h3>Resume Status</h3>
                <canvas id="resumeChart"></canvas>
            </div>
            <div class="card">
                <h3>Permit Status</h3>
                <canvas id="permitChart"></canvas>
            </div>
            <div class="card big">
                <h3>Applications Trend ({{ $range }} days)</h3>
                <canvas id="applicationTrend"></canvas>
            </div>
            <div class="card">
                <h3>Jobs (Active vs Inactive)</h3>
                <canvas id="jobChart"></canvas>
            </div>
            <div class="card">
                <h3>Bookmarks (Top Jobs)</h3>
                <ul>
                    @forelse($topBookmarkedJobs as $row)
                        <li style="margin-bottom:6px;color:#2B4053;">
                            {{ $row->title ?? 'Job' }}
                            @if(!empty($row->company))
                                <span style="color:#64748b;">( {{ $row->company }} )</span>
                            @endif
                            — <strong>{{ $row->count ?? 0 }}</strong> bookmarks
                        </li>
                    @empty
                        <li style="color:#64748b;">No bookmark data</li>
                    @endforelse
                </ul>
            </div>
            <div class="card">
                <h3>Audit Actions</h3>
                <canvas id="auditChart"></canvas>
            </div>
        </div>
    </div>

<script>
const primary = '#648EB5';
const secondary = '#506B81';

// User Growth
new Chart(document.getElementById('userGrowthChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($userGrowth->pluck('date')) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($userGrowth->pluck('count')) !!},
            borderColor: primary,
            backgroundColor: 'rgba(100, 142, 181, 0.2)',
            fill: true,
            tension: 0.3,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// User Distribution
new Chart(document.getElementById('userDistChart'), {
    type: 'doughnut',
    data: {
        labels: ['Job Seekers','Employers','Admins'],
        datasets: [{
            data: [{{ $userStats['job_seekers'] }}, {{ $userStats['employers'] }}, {{ $userStats['admins'] }}],
            backgroundColor: ['#93C5FD', '#F9A8D4', '#D8B4FE'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// Resume Status
new Chart(document.getElementById('resumeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Verified','Pending','Needs Review'],
        datasets: [{
            data: [{{ $resumeStats['verified'] }}, {{ $resumeStats['pending'] }}, {{ $resumeStats['needs_review'] }}],
            backgroundColor: ['#A7F3D0', '#FDE68A', '#FCA5A5'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// Permit Status
new Chart(document.getElementById('permitChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved','Pending','Rejected'],
        datasets: [{
            data: [{{ (int)($permitStats->approved ?? 0) }}, {{ (int)($permitStats->pending_review ?? 0) }}, {{ (int)($permitStats->rejected ?? 0) }}],
            backgroundColor: ['#A7F3D0', '#FDE68A', '#FCA5A5'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// Applications Trend
new Chart(document.getElementById('applicationTrend'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($applicationTrend->pluck('date')) !!},
        datasets: [{
            label: 'Applications',
            data: {!! json_encode($applicationTrend->pluck('count')) !!},
            backgroundColor: primary,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Jobs Active vs Inactive
new Chart(document.getElementById('jobChart'), {
    type: 'bar',
    data: {
        labels: ['Active','Inactive'],
        datasets: [{
            data: [{{ $jobStats['active'] }}, {{ $jobStats['inactive'] }}],
            backgroundColor: [primary, '#94A3B8']
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Audit Actions
new Chart(document.getElementById('auditChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($actionBreakdown->pluck('action')) !!},
        datasets: [{
            data: {!! json_encode($actionBreakdown->pluck('count')) !!},
            backgroundColor: ['#A7F3D0','#93C5FD','#FDE68A','#FCA5A5','#D8B4FE'],
            borderWidth: 0
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
</body>
</html>
