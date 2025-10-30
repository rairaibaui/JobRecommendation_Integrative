<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employer Dashboard - Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body {
    width: 100vw;
    height: 100vh;
    display: flex;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
    padding: 88px 20px 20px 20px;
    gap: 20px;
  }

  /* Sidebar */
  .sidebar {
    position: fixed;
    left: 20px;
    top: 88px;
    width: 250px;
    height: calc(100vh - 108px);
    border-radius: 8px;
    background: #FFF;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .sidebar .profile-ellipse {
    align-self: center;
  }

  .profile-name {
    align-self: center;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #000;
    margin-bottom: 20px;
    text-align: center;
  }

  .company-name {
    align-self: center;
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    font-weight: 400;
    color: #666;
    margin-bottom: 20px;
    text-align: center;
  }

  .sidebar .sidebar-btn {
    align-self: flex-start;
  }

  .profile-ellipse {
    width: 62px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-icon {
    width: 62px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 50%;
  }

  .profile-icon i {
    font-size: 30px;
    color: #FFF;
  }

  .profile-icon img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
  }

  .sidebar-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 39px;
    padding: 0 10px;
    border-radius: 8px;
    background: transparent;
    box-shadow: none;
    color: #000;
    font-size: 20px;
    font-weight: 400;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .sidebar-btn:hover {
    background: #e8f0f7;
  }

  .sidebar-btn.active {
    background: #648EB5;
    box-shadow: 0 7px 4px rgba(0,0,0,0.25);
    color: #000;
    width: 100%;
  }

  .sidebar-btn-icon {
    margin-right: 10px;
  }

  /* Main content */
  .main {
    margin-left: 290px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .top-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 68px;
    background: #2B4053;
    border-radius: 0;
    display: flex;
    align-items: center;
    padding: 0 20px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 800;
    z-index: 1000;
    justify-content: space-between;
  }

  .navbar-left {
    display: flex;
    align-items: center;
  }

  .hamburger {
    margin-right: 20px;
    color: #FFF;
  }

  .logout-btn {
    background: transparent;
    border: 1px solid #FFF;
    color: #FFF;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
  }

  .logout-btn:hover {
    background: #FFF;
    color: #2B4053;
  }

  .welcome {
    font-family: 'Poppins', sans-serif;
    font-size: 32px;
    font-weight: 600;
    color: #FFF;
    margin-bottom: 10px;
  }

  /* Stats Cards */
  .stats-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }

  .stat-card {
    flex: 1;
    background: #FFF;
    border-radius: 8px;
    box-shadow: 0 8px 4px 0 rgba(144, 141, 141, 0.3);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    background: linear-gradient(135deg, #648EB5, #334A5E);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stat-icon i {
    font-size: 28px;
    color: #FFF;
  }

  .stat-content h3 {
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #334A5E;
    margin: 0;
  }

  .stat-content p {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #666;
    margin: 0;
  }

  /* Job Postings Section */
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .section-title {
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 600;
    color: #FFF;
  }

  .btn-primary {
    background: linear-gradient(135deg, #648EB5, #334A5E);
    color: #FFF;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }

  /* Job Cards */
  .jobs-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    overflow-y: auto;
    max-height: calc(100vh - 350px);
    padding-right: 10px;
  }

  .job-card {
    background: #FFF;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 20px;
    transition: all 0.3s ease;
  }

  .job-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
  }

  .job-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
  }

  .job-title {
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #334A5E;
    margin: 0 0 5px 0;
  }

  .job-department {
    font-size: 13px;
    color: #666;
  }

  .job-status {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
  }

  .status-active {
    background: #d4edda;
    color: #155724;
  }

  .status-closed {
    background: #f8d7da;
    color: #721c24;
  }

  .job-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
  }

  .job-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #555;
  }

  .job-detail-item i {
    width: 16px;
    color: #648EB5;
  }

  .job-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    border-top: 1px solid #e9ecef;
  }

  .applications-count {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #648EB5;
    font-weight: 500;
  }

  .job-actions {
    display: flex;
    gap: 8px;
  }

  .btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    background: #f8f9fa;
    color: #555;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .btn-icon:hover {
    background: #648EB5;
    color: #FFF;
  }

  /* Scrollbar styling */
  .jobs-container::-webkit-scrollbar {
    width: 8px;
  }

  .jobs-container::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
  }

  .jobs-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 4px;
  }

  .jobs-container::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
  }
</style>
</head>
<body>
  <!-- Top Navbar -->
  <div class="top-navbar">
    <div class="navbar-left">
      <div class="hamburger"><i class="fa fa-bars"></i></div>
      <span>EMPLOYER DASHBOARD</span>
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
      @csrf
      <button type="submit" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
      </button>
    </form>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile-ellipse">
      <div class="profile-icon">
        @if($user->profile_picture)
          <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture">
        @else
          <i class="fa fa-building"></i>
        @endif
      </div>
    </div>
    <div class="profile-name">{{ $user->first_name }} {{ $user->last_name }}</div>
    <div class="company-name">{{ $user->company_name ?? 'Company Name' }}</div>

    <a href="{{ route('employer.dashboard') }}" class="sidebar-btn active"><i class="fa fa-home sidebar-btn-icon"></i> Dashboard</a>
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="welcome">Welcome, {{ $user->company_name ?? $user->first_name }}! 👋</div>

    @if(session('success'))
      <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <!-- Stats Cards -->
    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
          <h3>{{ count($jobPostings) }}</h3>
          <p>Active Job Postings</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
          <h3>{{ $jobPostings->sum('applications_count') }}</h3>
          <p>Total Applications</p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-content">
          <h3>{{ $hiredCount ?? 0 }}</h3>
          <p>Candidates Hired</p>
        </div>
      </div>
    </div>

    <!-- Job Postings Section -->
    <div class="section-header">
      <h2 class="section-title">Your Job Postings</h2>
      <a href="{{ route('employer.jobs.create') }}" class="btn-primary" style="text-decoration:none;">
        <i class="fas fa-plus"></i>
        Post New Job
      </a>
    </div>

    <div class="jobs-container">
      @forelse($jobPostings as $job)
        <div class="job-card">
          <div class="job-header">
            <div>
              <h3 class="job-title">{{ $job->title }}</h3>
              <p class="job-department">{{ $job->type }}</p>
            </div>
            <span class="job-status status-{{ strtolower($job->status) }}">
              {{ ucfirst($job->status) }}
            </span>
          </div>

          <div class="job-details">
            <div class="job-detail-item">
              <i class="fas fa-clock"></i>
              <span>{{ $job->type }}</span>
            </div>
            <div class="job-detail-item">
              <i class="fas fa-money-bill-wave"></i>
              <span>{{ $job->salary }}</span>
            </div>
            <div class="job-detail-item">
              <i class="fas fa-calendar"></i>
              <span>Posted: {{ $job->created_at->format('M d, Y') }}</span>
            </div>
          </div>

          <div class="job-footer">
            <div class="applications-count">
              <i class="fas fa-users"></i>
              <span>{{ $job->applications_count }} Applications</span>
            </div>
            <div class="job-actions">
              <a href="{{ route('employer.jobs.edit', $job) }}" class="btn-icon" title="Edit Job">
                <i class="fas fa-edit"></i>
              </a>
              
              @if($job->status === 'active')
                <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="display:inline; margin:0;">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="closed">
                  <button type="submit" class="btn-icon" title="Close Job (Position Filled)" style="background:#ffc107; color:#000;">
                    <i class="fas fa-lock"></i>
                  </button>
                </form>
              @elseif($job->status === 'closed')
                <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="display:inline; margin:0;">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="active">
                  <button type="submit" class="btn-icon" title="Reopen Job" style="background:#28a745;">
                    <i class="fas fa-unlock"></i>
                  </button>
                </form>
              @endif
              
              <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return confirm('Are you sure you want to delete this job posting? This action cannot be undone.');" style="display:inline; margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon" title="Delete Job" style="background:#dc3545;">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: #FFF; border-radius: 8px;">
          <i class="fas fa-briefcase" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
          <p style="color: #666; font-size: 16px;">No job postings yet. Click "Post New Job" to get started!</p>
        </div>
      @endforelse
    </div>
  </div>
</body>
</html>
