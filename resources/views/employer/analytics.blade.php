<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Analytics - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  .sidebar { position:fixed; left:20px; top:88px; width:250px; height:calc(100vh - 108px); border-radius:8px; background:#FFF; padding:20px; display:flex; flex-direction:column; gap:20px; overflow-y:auto; }
  .sidebar .profile-ellipse { align-self:center; }
  .profile-ellipse { width:62px; height:64px; border-radius:50%; background:linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display:flex; align-items:center; justify-content:center; overflow:hidden; }
  .profile-icon { width:62px; height:64px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:50%; }
  .profile-icon i { font-size:30px; color:#FFF; }
  .profile-icon img { width:100%; height:100%; border-radius:50%; object-fit:cover; border:none; outline:none; box-shadow:none; display:block; }
  .profile-name { align-self:center; font-family:'Poppins', sans-serif; font-size:18px; font-weight:600; color:#000; margin-bottom:8px; }
  .company-name { align-self:center; font-family:'Roboto', sans-serif; font-size:14px; font-weight:400; color:#666; margin-bottom:20px; }
  .sidebar .sidebar-btn { align-self:flex-start; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .hamburger { margin-right:20px; color:#FFF; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .stats-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-bottom:20px; }
  .stat-card { background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); color:#FFF; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
  .stat-card h3 { font-size:14px; font-weight:400; opacity:0.9; margin-bottom:8px; }
  .stat-card .value { font-size:32px; font-weight:700; font-family:'Poppins', sans-serif; }
  .stat-card .change { font-size:12px; margin-top:5px; opacity:0.8; }
  .chart-container { background:#FFF; border-radius:8px; padding:20px; margin-bottom:20px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
  .chart-container h3 { font-family:'Poppins', sans-serif; color:#334A5E; margin-bottom:15px; }
  .table-container { overflow-x:auto; }
  table { width:100%; border-collapse:collapse; }
  thead { background:#f8f9fa; }
  th { padding:12px; text-align:left; font-weight:600; color:#334A5E; border-bottom:2px solid #dee2e6; }
  td { padding:12px; border-bottom:1px solid #e9ecef; }
  .badge { padding:4px 10px; border-radius:12px; font-size:11px; font-weight:600; }
  .badge-success { background:#d1e7dd; color:#0f5132; }
  .badge-warning { background:#fff3cd; color:#856404; }
  .badge-danger { background:#f8d7da; color:#842029; }
  .badge-info { background:#cff4fc; color:#055160; }
  .progress-bar { width:100%; height:8px; background:#e9ecef; border-radius:4px; overflow:hidden; }
  .progress-fill { height:100%; background:#648EB5; transition:width .3s; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>ANALYTICS</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
      @include('partials.notifications')
    </div>
  </div>

  <div class="sidebar">
    <div class="profile-ellipse"><div class="profile-icon">@if($user->profile_picture)<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture">@else<i class="fa fa-building"></i>@endif</div></div>
    <div class="profile-name">{{ $user->first_name }} {{ $user->last_name }}</div>
    <div class="company-name">{{ $user->company_name ?? 'Company Name' }}</div>
    <a href="{{ route('employer.dashboard') }}" class="sidebar-btn"><i class="fa fa-home sidebar-btn-icon"></i> Dashboard</a>
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn active"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
    <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
      @csrf
      <button type="submit" class="sidebar-btn"
        style="border: none; background: #648EB5; color: #FFF; font-size: 20px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 10px; height: 39px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
        Logout
      </button>
    </form>
  </div>

  <div class="main">
    <!-- Overview Statistics -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3><i class="fas fa-briefcase"></i> Total Jobs Posted</h3>
        <div class="value">{{ $totalJobs }}</div>
        <div class="change">{{ $activeJobs }} active</div>
      </div>
      <div class="stat-card">
        <h3><i class="fas fa-file-alt"></i> Total Applications</h3>
        <div class="value">{{ $totalApplications }}</div>
        <div class="change">{{ $recentApplications }} in last 7 days</div>
      </div>
      <div class="stat-card">
        <h3><i class="fas fa-user-check"></i> Total Employees</h3>
        <div class="value">{{ $totalEmployees }}</div>
        <div class="change">{{ $activeEmployees }} currently active</div>
      </div>
      <div class="stat-card">
        <h3><i class="fas fa-handshake"></i> Hire Rate</h3>
        <div class="value">{{ $applicationToHireRate }}%</div>
        <div class="change">{{ $recentHires }} hired in last 7 days</div>
      </div>
    </div>

    <!-- Application Pipeline -->
    <div class="card">
      <h3 style="font-family:'Poppins', sans-serif; color:#334A5E; margin-bottom:20px;">
        <i class="fas fa-funnel-dollar"></i> Hiring Pipeline
      </h3>
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:15px;">
        <div>
          <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span style="font-size:14px; color:#666;">Pending</span>
            <span style="font-size:14px; font-weight:600;">{{ $pendingApplications }}</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $totalApplications > 0 ? ($pendingApplications / $totalApplications) * 100 : 0 }}%; background:#ffc107;"></div>
          </div>
        </div>
        <div>
          <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span style="font-size:14px; color:#666;">Reviewed</span>
            <span style="font-size:14px; font-weight:600;">{{ $reviewedApplications }}</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $totalApplications > 0 ? ($reviewedApplications / $totalApplications) * 100 : 0 }}%; background:#17a2b8;"></div>
          </div>
        </div>
        <div>
          <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span style="font-size:14px; color:#666;">Accepted</span>
            <span style="font-size:14px; font-weight:600;">{{ $acceptedApplications }}</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $totalApplications > 0 ? ($acceptedApplications / $totalApplications) * 100 : 0 }}%; background:#28a745;"></div>
          </div>
        </div>
        <div>
          <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span style="font-size:14px; color:#666;">Rejected</span>
            <span style="font-size:14px; font-weight:600;">{{ $rejectedApplications }}</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:{{ $totalApplications > 0 ? ($rejectedApplications / $totalApplications) * 100 : 0 }}%; background:#dc3545;"></div>
          </div>
        </div>
      </div>
      <div style="margin-top:20px; padding-top:15px; border-top:1px solid #e9ecef;">
        <div style="display:flex; justify-content:space-between; font-size:14px;">
          <span style="color:#666;">Conversion Rate (Application → Hire):</span>
          <span style="font-weight:700; color:#28a745;">{{ $applicationToHireRate }}%</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:14px; margin-top:8px;">
          <span style="color:#666;">Conversion Rate (Accepted → Hire):</span>
          <span style="font-weight:700; color:#28a745;">{{ $acceptanceToHireRate }}%</span>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
      <!-- Hiring Decisions Chart -->
      <div class="chart-container">
        <h3><i class="fas fa-chart-pie"></i> Hiring Decisions</h3>
        <canvas id="decisionsChart"></canvas>
      </div>

      <!-- Employee Retention -->
      <div class="chart-container">
        <h3><i class="fas fa-users-cog"></i> Employee Status</h3>
        <div style="text-align:center; padding:20px;">
          <div style="font-size:48px; font-weight:700; color:#648EB5; font-family:'Poppins', sans-serif;">
            {{ $retentionRate }}%
          </div>
          <div style="font-size:14px; color:#666; margin-top:10px;">Current Retention Rate</div>
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-top:30px;">
            <div>
              <div style="font-size:24px; font-weight:600; color:#28a745;">{{ $activeEmployees }}</div>
              <div style="font-size:12px; color:#666;">Active</div>
            </div>
            <div>
              <div style="font-size:24px; font-weight:600; color:#dc3545;">{{ $totalTerminated }}</div>
              <div style="font-size:12px; color:#666;">Terminated</div>
            </div>
            <div>
              <div style="font-size:24px; font-weight:600; color:#ffc107;">{{ $totalResigned }}</div>
              <div style="font-size:12px; color:#666;">Resigned</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly Trends -->
    <div class="chart-container">
      <h3><i class="fas fa-chart-line"></i> 6-Month Trends</h3>
      <canvas id="trendsChart"></canvas>
    </div>

    <!-- Top Performing Jobs -->
    <div class="card">
      <h3 style="font-family:'Poppins', sans-serif; color:#334A5E; margin-bottom:20px;">
        <i class="fas fa-trophy"></i> Top Job Postings (Most Applications)
      </h3>
      @if($topJobs->count() > 0)
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Rank</th>
                <th>Job Title</th>
                <th>Location</th>
                <th>Applications</th>
                <th>Performance</th>
              </tr>
            </thead>
            <tbody>
              @foreach($topJobs as $index => $job)
                <tr>
                  <td>
                    <span style="font-weight:700; color:#648EB5;">#{{ $index + 1 }}</span>
                  </td>
                  <td style="font-weight:600;">{{ $job['title'] }}</td>
                  <td><i class="fas fa-map-marker-alt" style="color:#648EB5;"></i> {{ $job['location'] }}</td>
                  <td>
                    <span class="badge badge-info">{{ $job['applications'] }} applications</span>
                  </td>
                  <td>
                    <div class="progress-bar" style="width:100px;">
                      <div class="progress-fill" style="width:{{ $topJobs->max('applications') > 0 ? ($job['applications'] / $topJobs->max('applications')) * 100 : 0 }}%;"></div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p style="text-align:center; color:#999; padding:40px;">No job postings yet.</p>
      @endif
    </div>
  </div>

  <script>
    // Hiring Decisions Pie Chart
    const decisionsCtx = document.getElementById('decisionsChart').getContext('2d');
    new Chart(decisionsCtx, {
      type: 'doughnut',
      data: {
        labels: ['Hired', 'Rejected', 'Terminated', 'Resigned'],
        datasets: [{
          data: [{{ $totalHired }}, {{ $totalRejected }}, {{ $totalTerminated }}, {{ $totalResigned }}],
          backgroundColor: ['#28a745', '#dc3545', '#6c757d', '#ffc107'],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: { size: 12 }
            }
          }
        }
      }
    });

    // Monthly Trends Line Chart
    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    new Chart(trendsCtx, {
      type: 'line',
      data: {
        labels: {!! json_encode($monthlyData->pluck('month')) !!},
        datasets: [
          {
            label: 'Applications Received',
            data: {!! json_encode($monthlyData->pluck('applications')) !!},
            borderColor: '#648EB5',
            backgroundColor: 'rgba(100, 142, 181, 0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Hires Made',
            data: {!! json_encode($monthlyData->pluck('hires')) !!},
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              padding: 15,
              font: { size: 12 }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
