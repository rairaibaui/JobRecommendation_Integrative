<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Applicants - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  .sidebar { position:fixed; left:20px; top:88px; width:250px; height:calc(100vh - 108px); border-radius:8px; background:#FFF; padding:20px; display:flex; flex-direction:column; gap:20px; }
  .profile-ellipse { width:62px; height:64px; border-radius:50%; background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display:flex; align-items:center; justify-content:center; align-self:center; }
  .profile-icon i { font-size:30px; color:#FFF; }
  .profile-name { align-self:center; font-family:'Poppins', sans-serif; font-size:18px; font-weight:600; color:#000; margin-bottom:8px; text-align:center; }
  .company-name { align-self:center; font-family:'Roboto', sans-serif; font-size:14px; font-weight:400; color:#666; margin-bottom:20px; text-align:center; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .hamburger { margin-right:20px; color:#FFF; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .stat-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; }
  .stat { background:#fff; border-radius:10px; padding:16px; border-left:4px solid #648EB5; }
  .stat h3 { margin:0; font-size:24px; color:#334A5E; }
  .stat p { margin:4px 0 0 0; font-size:12px; color:#666; }
  .filters { display:flex; gap:8px; flex-wrap:wrap; }
  .filter-btn { padding:8px 14px; border-radius:20px; border:1px solid #648EB5; background:#fff; color:#648EB5; font-size:12px; cursor:pointer; }
  .filter-btn.active { background:#648EB5; color:#fff; }
  .app-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; transition: transform .2s, box-shadow .2s; }
  .app-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .badge { padding:6px 10px; border-radius:12px; font-size:12px; font-weight:600; }
  .b-pending { background:#fff3cd; color:#856404; }
  .b-reviewing { background:#cfe2ff; color:#084298; }
  .b-accepted { background:#d1e7dd; color:#0f5132; }
  .b-rejected { background:#f8d7da; color:#842029; }
  .actions form { display:inline-block; }
  .actions button { padding:8px 10px; border-radius:8px; border:1px solid #ddd; background:#f8f9fa; cursor:pointer; font-size:12px; margin-left:6px; }
  .actions button.accept { background:#43A047; color:#fff; border:none; }
  .actions button.reject { background:#E53935; color:#fff; border:none; }
  .actions button.review { background:#1E88E5; color:#fff; border:none; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <i class="fas fa-bars hamburger"></i>
      <span>EMPLOYER • APPLICANTS</span>
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
      @csrf
      <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
  </div>

  <div class="sidebar">
    <div class="profile-ellipse"><div class="profile-icon"><i class="fa fa-building"></i></div></div>
    <div class="profile-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
    <div class="company-name">{{ Auth::user()->company_name ?? 'Company Name' }}</div>
    <a href="{{ route('employer.dashboard') }}" class="sidebar-btn"><i class="fa fa-home sidebar-btn-icon"></i> Dashboard</a>
    <a href="#" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn active"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="#" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="#" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-family:'Poppins', sans-serif; font-size:22px; color:#334A5E;">Applicants</h2>
        <div class="filters">
          <a class="filter-btn {{ !$status ? 'active' : '' }}" href="{{ route('employer.applicants') }}">All</a>
          <a class="filter-btn {{ $status==='pending' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'pending']) }}">Pending</a>
          <a class="filter-btn {{ $status==='reviewing' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'reviewing']) }}">Reviewing</a>
          <a class="filter-btn {{ $status==='accepted' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'accepted']) }}">Accepted</a>
          <a class="filter-btn {{ $status==='rejected' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'rejected']) }}">Rejected</a>
        </div>
      </div>

      <div class="stat-grid" style="margin-bottom:16px;">
        <div class="stat"><h3>{{ $stats['total'] }}</h3><p>Total</p></div>
        <div class="stat"><h3>{{ $stats['pending'] }}</h3><p>Pending</p></div>
        <div class="stat"><h3>{{ $stats['reviewing'] }}</h3><p>Reviewing</p></div>
        <div class="stat"><h3>{{ $stats['accepted'] }}</h3><p>Accepted</p></div>
        <div class="stat"><h3>{{ $stats['rejected'] }}</h3><p>Rejected</p></div>
      </div>

      @if($applications->count())
        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($applications as $app)
            <div class="app-card">
              <div style="flex:1;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                  <div>
                    <div style="font-weight:600; color:#333; font-size:16px;">{{ $app->job_title }}</div>
                    <div style="display:flex; gap:16px; margin-top:6px; color:#555; font-size:13px;">
                      <div><i class="fas fa-user"></i> {{ data_get($app->resume_snapshot, 'first_name') }} {{ data_get($app->resume_snapshot, 'last_name') }}</div>
                      @if(data_get($app->resume_snapshot, 'email'))
                        <div><i class="fas fa-envelope"></i> {{ data_get($app->resume_snapshot, 'email') }}</div>
                      @endif
                      <div><i class="fas fa-calendar"></i> Applied {{ $app->created_at->format('M d, Y') }}</div>
                    </div>
                  </div>
                  @php
                    $map = ['pending'=>'b-pending','reviewing'=>'b-reviewing','accepted'=>'b-accepted','rejected'=>'b-rejected'];
                  @endphp
                  <span class="badge {{ $map[$app->status] ?? '' }}" style="text-transform:capitalize;">{{ $app->status }}</span>
                </div>

                @if(data_get($app->resume_snapshot, 'summary'))
                  <div style="margin-top:10px; font-size:13px; color:#444;">{{ \Illuminate\Support\Str::limit(data_get($app->resume_snapshot, 'summary'), 180) }}</div>
                @endif
              </div>
              <div class="actions">
                <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                  @csrf
                  <input type="hidden" name="status" value="reviewing">
                  <button type="submit" class="review" title="Mark as Reviewing"><i class="fas fa-eye"></i></button>
                </form>
                <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                  @csrf
                  <input type="hidden" name="status" value="accepted">
                  <button type="submit" class="accept" title="Accept"><i class="fas fa-check"></i></button>
                </form>
                <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                  @csrf
                  <input type="hidden" name="status" value="rejected">
                  <button type="submit" class="reject" title="Reject"><i class="fas fa-times"></i></button>
                </form>
              </div>
            </div>
          @endforeach
        </div>

        <div style="margin-top:16px;">{{ $applications->withQueryString()->links() }}</div>
      @else
        <div style="text-align:center; color:#666; padding:32px;">
          <i class="fas fa-inbox" style="font-size:48px; opacity:0.4; margin-bottom:8px;"></i>
          <div>No applications found.</div>
        </div>
      @endif
    </div>
  </div>
</body>
</html>
