<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Audit Logs - Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  .sidebar { position:fixed; left:20px; top:88px; width:250px; height:calc(100vh - 108px); border-radius:8px; background:#FFF; padding:20px; display:flex; flex-direction:column; gap:20px; }
  .sidebar .profile-ellipse { align-self:center; }
  .profile-ellipse { width:62px; height:64px; border-radius:50%; background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display:flex; align-items:center; justify-content:center; overflow:hidden; }
  .profile-icon { width:62px; height:64px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:50%; }
  .profile-icon img { width:100%; height:100%; border-radius:50%; object-fit:cover; border:none; outline:none; box-shadow:none; display:block; }
  .profile-name { align-self:center; font-family:'Poppins', sans-serif; font-size:18px; font-weight:600; color:#000; margin-bottom:20px; }
  .company-name { align-self:center; font-family:'Roboto', sans-serif; font-size:14px; font-weight:400; color:#666; margin-bottom:20px; }
  .sidebar .sidebar-btn { align-self:flex-start; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }

  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .filters { display:flex; gap:10px; align-items:center; margin-bottom:10px; }
  .filters select, .filters input { padding:8px 10px; border:1px solid #ccc; border-radius:6px; }
  .logs { width:100%; border-collapse:separate; border-spacing:0 8px; }
  .logs tr { background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.05); }
  .logs th { text-align:left; color:#334A5E; font-weight:700; padding:10px 12px; }
  .logs td { padding:12px; vertical-align:top; }
  .tag { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
  .tag.info { background:#e7f1ff; color:#084298; }
  .tag.success { background:#d1e7dd; color:#0f5132; }
  .tag.warn { background:#fff3cd; color:#856404; }
  .tag.error { background:#f8d7da; color:#842029; }
  .muted { color:#6c757d; font-size:12px; }
  .json { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-size:12px; background:#f8f9fa; border:1px solid #eee; border-radius:6px; padding:8px; white-space:pre-wrap; max-height:200px; overflow:auto; }
  .btn { background:#648EB5; color:#fff; border:none; border-radius:6px; padding:8px 12px; font-size:12px; cursor:pointer; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>AUDIT LOGS</span>
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
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('employer.auditLogs') }}" class="sidebar-btn active"><i class="fa fa-clipboard-list sidebar-btn-icon"></i> Audit Logs</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    <div class="card">
      <form method="GET" class="filters">
        <select name="event" onchange="this.form.submit()">
          <option value="">All events</option>
          @foreach($events as $ev)
            <option value="{{ $ev }}" {{ request('event') === $ev ? 'selected' : '' }}>{{ $ev }}</option>
          @endforeach
        </select>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title or message..." />
        <button class="btn" type="submit"><i class="fas fa-search"></i> Filter</button>
      </form>

      <table class="logs">
        <thead>
          <tr>
            <th style="width:18%">When</th>
            <th style="width:18%">Event</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            @php
              $tagClass = match(true) {
                str_contains($log->event, 'accepted') => 'success',
                str_contains($log->event, 'rejected') || str_contains($log->event, 'terminated') => 'error',
                str_contains($log->event, 'interview') => 'warn',
                default => 'info'
              };
            @endphp
            <tr>
              <td>
                <div>{{ $log->created_at->format('M d, Y h:i A') }}</div>
                <div class="muted">IP: {{ $log->ip_address ?? 'n/a' }}</div>
              </td>
              <td>
                <span class="tag {{ $tagClass }}"><i class="fas fa-circle"></i> {{ $log->event }}</span>
                @if($log->actor)
                  <div class="muted">by {{ $log->actor->first_name }} {{ $log->actor->last_name }}</div>
                @endif
              </td>
              <td>
                <div style="font-weight:600; color:#334A5E;">{{ $log->title }}</div>
                <div style="color:#555; margin-top:4px;">{{ $log->message }}</div>
                @if($log->data)
                  <details style="margin-top:8px;">
                    <summary style="cursor:pointer; color:#648EB5; font-size:13px;"><i class="fas fa-code"></i> Payload</summary>
                    <pre class="json">{{ json_encode($log->data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                  </details>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; color:#666; padding:20px;">No audit entries yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div style="margin-top:12px;">
        {{ $logs->links() }}
      </div>
    </div>
  </div>
</body>
</html>
