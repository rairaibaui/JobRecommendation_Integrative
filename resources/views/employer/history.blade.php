<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Hiring History - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  .sidebar { position:fixed; left:20px; top:88px; width:250px; height:calc(100vh - 108px); border-radius:8px; background:#FFF; padding:20px; display:flex; flex-direction:column; gap:20px; }
  .sidebar .profile-ellipse { align-self:center; }
  .profile-ellipse { width:62px; height:64px; border-radius:50%; background:linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display:flex; align-items:center; justify-content:center; overflow:hidden; }
  .profile-icon { width:62px; height:64px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:50%; }
  .profile-icon i { font-size:30px; color:#FFF; }
  .profile-icon img { width:100%; height:100%; border-radius:50%; object-fit:cover; border:none; outline:none; box-shadow:none; display:block; }
  .profile-name { align-self:center; font-family:'Poppins', sans-serif; font-size:18px; font-weight:600; color:#000; margin-bottom:8px; }
  .company-name {
    align-self:center;
    font-family:'Roboto', sans-serif;
    font-size:14px;
    font-weight:600;
    color:#506B81;
    background:#eaf2fb;
    border:1px solid #cddff2;
    border-radius:999px;
    padding:5px 12px;
    display:inline-flex;
    align-items:center;
    gap:6px;
    letter-spacing:0.3px;
    margin-bottom:4px;
  }
  .company-badge {
    align-self:center;
    font-family:'Roboto', sans-serif;
    font-size:16px;
    font-weight:700;
    color:#2B4053;
    display:inline-flex;
    align-items:center;
    text-transform:uppercase;
    margin-bottom:20px;
  }
  .sidebar .sidebar-btn { align-self:flex-start; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .stat-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; }
  .stat { background:#fff; border-radius:10px; padding:16px; border-left:4px solid #648EB5; }
  .stat h3 { margin:0; font-size:24px; color:#334A5E; }
  .stat p { margin:4px 0 0 0; font-size:12px; color:#666; }
  .filters { display:flex; gap:8px; flex-wrap:wrap; }
  .filter-btn { padding:8px 14px; border-radius:20px; border:1px solid #648EB5; background:#fff; color:#648EB5; font-size:12px; cursor:pointer; text-decoration:none; }
  .filter-btn.active { background:#648EB5; color:#fff; }
  .history-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; margin-bottom:12px; transition: transform .2s, box-shadow .2s; }
  .history-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .history-card.hired { border-left:4px solid #43A047; background:#f1f8f4; }
  .history-card.rejected { border-left:4px solid #E53935; background:#fef5f5; }
  .history-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; }
  .decision-badge { padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600; }
  .decision-badge.hired { background:#43A047; color:#fff; }
  .decision-badge.rejected { background:#E53935; color:#fff; }
  .info-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; font-size:13px; color:#555; }
  .info-label { font-weight:600; color:#334A5E; }
  .rejection-reason { background:#fff3cd; border-left:3px solid #ffc107; padding:10px; margin-top:10px; border-radius:6px; }
  .pagination { display:flex; gap:8px; justify-content:center; margin-top:20px; }
  .pagination a, .pagination span { padding:8px 12px; border-radius:6px; background:#fff; color:#648EB5; text-decoration:none; border:1px solid #648EB5; }
  .pagination .active { background:#648EB5; color:#fff; }
</style>
</head>
<body>
  <!-- Top Navbar -->
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>Hiring History</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
      @include('partials.notifications')
    </div>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile-ellipse">
      <div class="profile-icon">
        @if(Auth::user()->profile_picture)
          <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" style="cursor:pointer;" onclick="showEmpProfilePictureModal()">
        @else
          <i class="fa fa-building" style="cursor:pointer;" onclick="showEmpProfilePictureModal()"></i>
        @endif
      </div>
    </div>
    <div class="company-name" title="{{ Auth::user()->company_name }}"><i class="fas fa-building"></i> {{ Auth::user()->company_name ?? 'Company Name' }}</div>
    <div class="company-badge">Company</div>
    
    <script>
    function showEmpProfilePictureModal() {
      const oldModal = document.getElementById('empProfilePicModal');
      if (oldModal) oldModal.remove();
      const picUrl = @json(Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : null);
      const name = @json(Auth::user()->company_name ?? 'Company');
      const modal = document.createElement('div');
      modal.id = 'empProfilePicModal';
      modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10001; display:flex; align-items:center; justify-content:center;';
      modal.innerHTML = `
        <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 10px 40px rgba(0,0,0,0.3); display:flex; flex-direction:column; align-items:center; max-width:350px; width:90%; position:relative;">
          <button onclick="document.getElementById('empProfilePicModal').remove();" style="position:absolute; top:15px; right:15px; background:rgba(0,0,0,0.1); border:none; width:32px; height:32px; border-radius:50%; font-size:18px; cursor:pointer; color:#333;">&times;</button>
          <h3 style="margin-bottom:18px; color:#648EB5; font-size:20px; font-weight:600;">Company Profile</h3>
          ${picUrl ? `<img src='${picUrl}' alt='Profile Picture' style='width:120px; height:120px; object-fit:cover; border-radius:50%; border:4px solid #648EB5; margin-bottom:12px;'>` : `<div style='width:120px; height:120px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:48px; color:#aaa; margin-bottom:12px;'><i class='fas fa-building'></i></div>`}
          <div style="font-size:16px; color:#333; font-weight:500;">${name}</div>
          <button onclick="document.getElementById('empProfilePicModal').remove();" style="margin-top:22px; background:#6c757d; color:white; border:none; padding:8px 22px; border-radius:8px; cursor:pointer; font-size:14px;">Close</button>
        </div>
      `;
      document.body.appendChild(modal);
    }
    </script>
    <a href="{{ route('employer.dashboard') }}" class="sidebar-btn"><i class="fa fa-home sidebar-btn-icon"></i> Dashboard</a>
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn active"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
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

  <!-- Main Content -->
  <div class="main">
    <!-- Stats Card -->
    <div class="card">
      <h2 style="margin:0 0 15px 0; color:#334A5E;"><i class="fas fa-chart-bar"></i> Hiring & Rejection Records</h2>
      <div class="stat-grid">
        <div class="stat">
          <h3>{{ $stats['total'] }}</h3>
          <p>Total Records</p>
        </div>
        <div class="stat" style="border-left-color:#43A047;">
          <h3>{{ $stats['hired'] }}</h3>
          <p>Hired</p>
        </div>
        <div class="stat" style="border-left-color:#E53935;">
          <h3>{{ $stats['rejected'] }}</h3>
          <p>Rejected</p>
        </div>
        <div class="stat" style="border-left-color:#6c757d;">
          <h3>{{ $stats['terminated'] }}</h3>
          <p>Terminated</p>
        </div>
        <div class="stat" style="border-left-color:#ffc107;">
          <h3>{{ $stats['resigned'] }}</h3>
          <p>Resigned</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="filters">
        <a href="{{ route('employer.history') }}" class="filter-btn {{ !request('decision') ? 'active' : '' }}">
          All Records
        </a>
        <a href="{{ route('employer.history', ['decision' => 'hired']) }}" class="filter-btn {{ request('decision') === 'hired' ? 'active' : '' }}">
          <i class="fas fa-check-circle"></i> Hired
        </a>
        <a href="{{ route('employer.history', ['decision' => 'rejected']) }}" class="filter-btn {{ request('decision') === 'rejected' ? 'active' : '' }}">
          <i class="fas fa-times-circle"></i> Rejected
        </a>
        <a href="{{ route('employer.history', ['decision' => 'terminated']) }}" class="filter-btn {{ request('decision') === 'terminated' ? 'active' : '' }}">
          <i class="fas fa-user-slash"></i> Terminated
        </a>
        <a href="{{ route('employer.history', ['decision' => 'resigned']) }}" class="filter-btn {{ request('decision') === 'resigned' ? 'active' : '' }}">
          <i class="fas fa-door-open"></i> Resigned
        </a>
      </div>
    </div>

    <!-- History List -->
    <div class="card">
      <h3 style="margin:0 0 15px 0; color:#334A5E;">
        @if(request('decision') === 'hired')
          <i class="fas fa-check-circle" style="color:#43A047;"></i> Hired Applicants
        @elseif(request('decision') === 'rejected')
          <i class="fas fa-times-circle" style="color:#E53935;"></i> Rejected Applicants
        @elseif(request('decision') === 'terminated')
          <i class="fas fa-user-slash" style="color:#6c757d;"></i> Terminated Employees
        @elseif(request('decision') === 'resigned')
          <i class="fas fa-door-open" style="color:#ffc107;"></i> Resignations
        @else
          <i class="fas fa-list"></i> All Records
        @endif
      </h3>

      @if($history->count() > 0)
        @foreach($history as $record)
          <div class="history-card {{ $record->decision }}">
            <div class="history-header">
              <div>
                <h4 style="margin:0 0 4px 0; color:#334A5E; font-size:16px;">
                  {{ data_get($record->applicant_snapshot, 'first_name') }} {{ data_get($record->applicant_snapshot, 'last_name') }}
                </h4>
                <p style="margin:0; color:#666; font-size:14px;">
                  Applied for: <strong>{{ $record->job_title }}</strong>
                  @if($record->company_name)
                    at {{ $record->company_name }}
                  @endif
                </p>
              </div>
              <span class="decision-badge {{ $record->decision }}" style="{{ $record->decision === 'hired' ? 'background:#43A047;color:#fff;' : ($record->decision === 'rejected' ? 'background:#E53935;color:#fff;' : ($record->decision === 'terminated' ? 'background:#6c757d;color:#fff;' : 'background:#ffc107;color:#000;')) }}">
                @if($record->decision === 'hired')
                  <i class="fas fa-check"></i> HIRED
                @elseif($record->decision === 'rejected')
                  <i class="fas fa-times"></i> REJECTED
                @elseif($record->decision === 'terminated')
                  <i class="fas fa-user-slash"></i> TERMINATED
                @elseif($record->decision === 'resigned')
                  <i class="fas fa-door-open"></i> RESIGNED
                @endif
              </span>
            </div>

            <div class="info-grid">
              @if(data_get($record->applicant_snapshot, 'email'))
                <div>
                  <span class="info-label"><i class="fas fa-envelope"></i> Email:</span> 
                  <a href="mailto:{{ data_get($record->applicant_snapshot, 'email') }}" style="color:#648EB5; text-decoration:none;">
                    {{ data_get($record->applicant_snapshot, 'email') }}
                  </a>
                </div>
              @endif
              @if(data_get($record->applicant_snapshot, 'phone_number'))
                <div>
                  <span class="info-label"><i class="fas fa-phone"></i> Phone:</span> 
                  <a href="tel:{{ data_get($record->applicant_snapshot, 'phone_number') }}" style="color:#648EB5; text-decoration:none;">
                    {{ data_get($record->applicant_snapshot, 'phone_number') }}
                  </a>
                </div>
              @endif
              @if(data_get($record->applicant_snapshot, 'location'))
                <div>
                  <span class="info-label"><i class="fas fa-map-marker-alt"></i> Location:</span> 
                  {{ data_get($record->applicant_snapshot, 'location') }}
                </div>
              @endif
              <div>
                <span class="info-label"><i class="fas fa-calendar"></i> Decision Date:</span> 
                {{ $record->decision_date->format('M d, Y h:i A') }}
              </div>
            </div>

            @if(in_array($record->decision, ['rejected','terminated','resigned']) && $record->rejection_reason)
              <div class="rejection-reason">
                <strong style="color:#856404;"><i class="fas fa-info-circle"></i> {{ ucfirst($record->decision) }} Reason:</strong>
                <p style="margin:4px 0 0 0; color:#856404;">{{ $record->rejection_reason }}</p>
              </div>
            @endif
          </div>
        @endforeach

        <!-- Pagination -->
        @if($history->hasPages())
          <div class="pagination">
            @if($history->onFirstPage())
              <span style="opacity:0.5;"><i class="fas fa-chevron-left"></i></span>
            @else
              <a href="{{ $history->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($history->links()->elements[0] as $page => $url)
              @if($page == $history->currentPage())
                <span class="active">{{ $page }}</span>
              @else
                <a href="{{ $url }}">{{ $page }}</a>
              @endif
            @endforeach

            @if($history->hasMorePages())
              <a href="{{ $history->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
              <span style="opacity:0.5;"><i class="fas fa-chevron-right"></i></span>
            @endif
          </div>
        @endif
      @else
        <div style="text-align:center; color:#999; padding:40px; background:#f8f9fa; border-radius:8px;">
          <i class="fas fa-inbox" style="font-size:48px; opacity:0.4; margin-bottom:12px; display:block;"></i>
          <p style="margin:0; font-size:16px;">No records found.</p>
          <p style="margin:8px 0 0 0; font-size:14px;">Hiring and rejection records will appear here.</p>
        </div>
      @endif
    </div>
  </div>
</body>
</html>
