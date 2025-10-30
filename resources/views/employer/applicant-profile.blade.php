<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Applicant Profile | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  .sidebar { position:fixed; left:20px; top:88px; width:250px; height:calc(100vh - 108px); border-radius:8px; background:#FFF; padding:20px; display:flex; flex-direction:column; gap:20px; }
  .profile-ellipse { width:62px; height:64px; border-radius:50%; background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%); display:flex; align-items:center; justify-content:center; align-self:center; }
  .profile-icon { width:62px; height:64px; display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:50%; }
  .profile-icon i { font-size:30px; color:#FFF; }
  .profile-icon img { width:100%; height:100%; border-radius:50%; object-fit:cover; }
  .profile-name { align-self:center; font-family:'Poppins', sans-serif; font-size:18px; font-weight:600; color:#000; margin-bottom:8px; text-align:center; }
  .company-name { align-self:center; font-family:'Roboto', sans-serif; font-size:14px; font-weight:400; color:#666; margin-bottom:20px; text-align:center; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .section { margin-top:12px; }
  .sec-title { color:#334A5E; font-weight:700; margin:0 0 8px 0; }
  .pill { background:#e8f0f7; color:#334A5E; padding:6px 12px; border-radius:16px; font-size:12px; display:inline-block; margin-right:6px; margin-bottom:6px; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <i class="fas fa-bars"></i>
      <span>APPLICANT PROFILE</span>
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
      @csrf
      <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
  </div>

  <div class="sidebar">
    <div class="profile-ellipse"><div class="profile-icon">@if($user->profile_picture)<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture">@else<i class="fa fa-building"></i>@endif</div></div>
    <div class="profile-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
    <div class="company-name">{{ Auth::user()->company_name ?? 'Company Name' }}</div>
    <a href="{{ route('employer.dashboard') }}" class="sidebar-btn"><i class="fa fa-home sidebar-btn-icon"></i> Dashboard</a>
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn active"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    <div class="card">
      @php $snap = $application->resume_snapshot ?? []; @endphp
      <div style="display:flex; gap:16px; align-items:flex-start;">
        @php
          $pic = data_get($snap, 'profile_picture');
          $picUrl = $pic && (str_starts_with($pic,'http') || str_starts_with($pic,'/storage/')) ? $pic : ($pic ? asset('storage/'.$pic) : null);
        @endphp
        @if($picUrl)
          <img src="{{ $picUrl }}" alt="Profile" style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:2px solid #648EB5;">
        @else
          <div style="width:96px;height:96px;border-radius:50%;background:#e8f0f7;display:flex;align-items:center;justify-content:center;border:2px solid #648EB5;"><i class="fas fa-user" style="font-size:36px;color:#648EB5;"></i></div>
        @endif
        <div style="flex:1;">
          <h2 style="margin:0;color:#334A5E;">{{ data_get($snap,'first_name') }} {{ data_get($snap,'last_name') }}</h2>
          <div style="display:flex;gap:14px;color:#555;margin-top:6px;flex-wrap:wrap;">
            @if(data_get($snap,'email'))<div><i class="fas fa-envelope"></i> <a href="mailto:{{ data_get($snap,'email') }}" style="color:#648EB5;">{{ data_get($snap,'email') }}</a></div>@endif
            @if(data_get($snap,'phone_number'))<div><i class="fas fa-phone"></i> <a href="tel:{{ data_get($snap,'phone_number') }}" style="color:#648EB5;">{{ data_get($snap,'phone_number') }}</a></div>@endif
            @if(data_get($snap,'location'))<div><i class="fas fa-map-marker-alt"></i> {{ data_get($snap,'location') }}</div>@endif
          </div>
          <div style="margin-top:6px;font-size:13px;">
            <span class="pill" style="background: {{ data_get($snap,'employment_status')==='employed' ? '#d1e7dd' : '#d1ecf1' }}; color: {{ data_get($snap,'employment_status')==='employed' ? '#0f5132' : '#0c5460' }};">
              <i class="fas {{ data_get($snap,'employment_status')==='employed' ? 'fa-briefcase' : 'fa-search' }}"></i>
              {{ data_get($snap,'employment_status')==='employed' ? 'EMPLOYED' : 'SEEKING' }}
            </span>
            @if(data_get($snap,'hired_by_company'))
              <span class="pill"><i class="fas fa-building"></i> {{ data_get($snap,'hired_by_company') }}</span>
            @endif
          </div>
        </div>
        <div>
          <div class="pill" style="background:#fff3cd;color:#856404;"><i class="fas fa-calendar"></i> Applied {{ $application->created_at->format('M d, Y') }}</div>
          <div class="pill" style="margin-top:6px; background:#cfe2ff;color:#084298;"><i class="fas fa-briefcase"></i> {{ $application->job_title }}</div>
        </div>
      </div>

      @if(data_get($snap,'summary'))
      <div class="section">
        <h3 class="sec-title"><i class="fas fa-align-left"></i> Summary</h3>
        <p style="color:#555; line-height:1.6;">{{ data_get($snap,'summary') }}</p>
      </div>
      @endif

      @if(data_get($snap,'skills'))
      <div class="section">
        <h3 class="sec-title"><i class="fas fa-cogs"></i> Skills</h3>
        @php $skillsStr = is_array(data_get($snap,'skills')) ? implode(', ', data_get($snap,'skills')) : (string) data_get($snap,'skills'); @endphp
        <div>
          @foreach(array_filter(array_map('trim', explode(',', $skillsStr))) as $skill)
            <span class="pill">{{ $skill }}</span>
          @endforeach
        </div>
      </div>
      @endif

      @if(data_get($snap,'education'))
      <div class="section">
        <h3 class="sec-title"><i class="fas fa-graduation-cap"></i> Education</h3>
        <p style="white-space:pre-wrap; color:#555;">{{ is_array(data_get($snap,'education')) ? json_encode(data_get($snap,'education')) : data_get($snap,'education') }}</p>
      </div>
      @endif

      @if(data_get($snap,'experience'))
      <div class="section">
        <h3 class="sec-title"><i class="fas fa-briefcase"></i> Experience</h3>
        <p style="white-space:pre-wrap; color:#555;">{{ is_array(data_get($snap,'experience')) ? json_encode(data_get($snap,'experience')) : data_get($snap,'experience') }}</p>
      </div>
      @endif

      @if(data_get($snap,'resume_file'))
      <div class="section" style="margin-top:14px;">
        <h3 class="sec-title"><i class="fas fa-file-pdf"></i> Resume</h3>
        <a href="{{ asset('storage/'.data_get($snap,'resume_file')) }}" target="_blank" class="pill" style="text-decoration:none; background:#648EB5; color:#fff;">
          <i class="fas fa-download"></i> Download Resume
        </a>
      </div>
      @endif

      @if($application->interview_date || $application->interview_location || $application->interview_notes)
      <div class="section" style="margin-top:14px;">
        <h3 class="sec-title"><i class="fas fa-calendar-check"></i> Interview</h3>
        <div style="color:#555;">
          @if($application->interview_date)
            <div><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($application->interview_date)->format('M d, Y h:i A') }}</div>
          @endif
          @if($application->interview_location)
            <div><i class="fas fa-map-marker-alt"></i> {{ $application->interview_location }}</div>
          @endif
          @if($application->interview_notes)
            <div><i class="fas fa-sticky-note"></i> {{ $application->interview_notes }}</div>
          @endif
        </div>
      </div>
      @endif
    </div>
  </div>
</body>
</html>
