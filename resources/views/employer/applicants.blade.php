<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Applicants - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@php
  // Helper function to safely convert any value to string
  if (!function_exists('toDisplayString')) {
      function toDisplayString($value) {
          if (is_null($value)) return '';
          if (is_string($value)) return $value;
          if (is_numeric($value)) return (string)$value;
          if (is_bool($value)) return $value ? 'Yes' : 'No';
          if (is_array($value)) {
              // Flatten nested arrays
              $flattened = [];
              array_walk_recursive($value, function($item) use (&$flattened) {
                  if (!is_array($item) && !is_object($item)) {
                      $flattened[] = $item;
                  }
              });
              return implode(', ', array_filter($flattened));
          }
          if (is_object($value)) return json_encode($value);
          return '';
      }
  }
@endphp

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
  .app-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; display:flex; align-items:flex-start; gap:16px; transition: transform .2s, box-shadow .2s; }
  .app-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .applicant-info { flex:1; min-width:0; }
  .actions { display:flex; gap:8px; flex-shrink:0; align-items:flex-start; }
  .badge { padding:6px 10px; border-radius:12px; font-size:12px; font-weight:600; }
  .b-pending { background:#fff3cd; color:#856404; }
  .b-reviewing { background:#cfe2ff; color:#084298; }
    .b-for_interview { background:#d1ecf1; color:#0c5460; }
    .b-interviewed { background:#fff3cd; color:#856404; }
  .b-accepted { background:#d1e7dd; color:#0f5132; }
  .b-rejected { background:#f8d7da; color:#842029; }
  .actions form { display:inline-block; }
  .actions button { padding:8px 10px; border-radius:8px; border:1px solid #ddd; background:#f8f9fa; cursor:pointer; font-size:12px; margin-left:6px; }
  .actions button.accept { background:#43A047; color:#fff; border:none; }
  .actions button.reject { background:#E53935; color:#fff; border:none; }
  .actions button.review { background:#1E88E5; color:#fff; border:none; }
    .actions button.interview { background:#17a2b8; color:#fff; border:none; }
    .actions button.interviewed-btn { background:#ffc107; color:#fff; border:none; }
  .job-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; transition: all .3s; cursor:pointer; }
  .job-card:hover { box-shadow:0 12px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }
  .job-title { font-size:18px; font-weight:600; color:#334A5E; margin-bottom:8px; }
  .job-preview { display:flex; gap:20px; flex-wrap:wrap; font-size:13px; color:#666; }
  .job-preview > div { display:flex; align-items:center; gap:6px; }
  .job-details { display:none; margin-top:16px; padding-top:16px; border-top:1px solid #e5e7eb; }
  .job-card.expanded .job-details { display:block; }
  .applicants-list { margin-top:16px; display:flex; flex-direction:column; gap:12px; }
  .applicant-full-details { display:none; margin-top:12px; padding-top:12px; border-top:1px solid #e9ecef; }
  .applicant-full-details.expanded { display:block; }
  .btn-view-profile { background:#648EB5; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; margin-top:8px; transition:all .3s; }
  .btn-view-profile:hover { background:#334A5E; }
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
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
  <a href="{{ route('employer.applicants') }}" class="sidebar-btn active"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
  <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
  <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    @if(session('success'))
      <div style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:8px; border:1px solid #c3e6cb; margin-bottom:16px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-family:'Poppins', sans-serif; font-size:22px; color:#334A5E;">Applicants</h2>
        <div class="filters">
          <a class="filter-btn {{ !$status ? 'active' : '' }}" href="{{ route('employer.applicants') }}">All</a>
          <a class="filter-btn {{ $status==='pending' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'pending']) }}">Pending</a>
          <a class="filter-btn {{ $status==='reviewing' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'reviewing']) }}">Reviewing</a>
            <a class="filter-btn {{ $status==='for_interview' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'for_interview']) }}">For Interview</a>
            <a class="filter-btn {{ $status==='interviewed' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'interviewed']) }}">Interviewed</a>
          <a class="filter-btn {{ $status==='accepted' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'accepted']) }}">Accepted</a>
          <a class="filter-btn {{ $status==='rejected' ? 'active' : '' }}" href="{{ route('employer.applicants', ['status'=>'rejected']) }}">Rejected</a>
        </div>
      </div>

      <div class="stat-grid" style="margin-bottom:16px;">
        <div class="stat"><h3>{{ $stats['total'] }}</h3><p>Total</p></div>
        <div class="stat"><h3>{{ $stats['pending'] }}</h3><p>Pending</p></div>
        <div class="stat"><h3>{{ $stats['reviewing'] }}</h3><p>Reviewing</p></div>
          <div class="stat" style="border-left-color:#17a2b8;"><h3>{{ $stats['for_interview'] ?? 0 }}</h3><p>For Interview</p></div>
          <div class="stat" style="border-left-color:#ffc107;"><h3>{{ $stats['interviewed'] ?? 0 }}</h3><p>Interviewed</p></div>
        <div class="stat"><h3>{{ $stats['accepted'] }}</h3><p>Accepted</p></div>
        <div class="stat"><h3>{{ $stats['rejected'] }}</h3><p>Rejected</p></div>
      </div>

      @if($jobPostings->count())
        <div style="display:flex; flex-direction:column; gap:16px;">
          @foreach($jobPostings as $job)
            <div class="job-card" onclick="toggleJob(this)">
              <div class="job-header">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                  <div style="flex:1;">
                    <div class="job-title">{{ $job->title }}</div>
                    <div class="job-preview">
                      <div><i class="fas fa-building"></i> {{ $job->company_name }}</div>
                      <div><i class="fas fa-map-marker-alt"></i> {{ $job->location }}</div>
                      <div><i class="fas fa-briefcase"></i> {{ $job->type }}</div>
                      @if($job->salary)
                        <div><i class="fas fa-dollar-sign"></i> {{ $job->salary }}</div>
                      @endif
                      <div><i class="fas fa-users"></i> {{ $job->applications_count }} {{ $job->applications_count == 1 ? 'Applicant' : 'Applicants' }}</div>
                    </div>
                  </div>
                  <i class="fas fa-chevron-down" style="transition: transform .3s; color:#648EB5;"></i>
                </div>
              </div>

              <div class="job-details">
                <div style="margin-bottom:16px;">
                  <h4 style="color:#334A5E; margin-bottom:8px;">Job Description</h4>
                  <p style="color:#555; line-height:1.6; white-space:pre-wrap;">{{ $job->description }}</p>
                </div>

                @if($job->skills && count($job->skills) > 0)
                  <div style="margin-bottom:16px;">
                    <h4 style="color:#334A5E; margin-bottom:8px;">Required Skills</h4>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                      @foreach($job->skills as $skill)
                        <span style="background:#e8f0f7; color:#334A5E; padding:6px 12px; border-radius:16px; font-size:12px;">{{ $skill }}</span>
                      @endforeach
                    </div>
                  </div>
                @endif

                <h4 style="color:#334A5E; margin-bottom:12px;">
                  <i class="fas fa-users"></i> Applicants ({{ $job->applications->count() }})
                </h4>

                @if($job->applications->count() > 0)
                  <div class="applicants-list">
                    @foreach($job->applications as $app)
                      <div class="app-card" onclick="event.stopPropagation()">
                        <!-- Profile Picture (Left Side) -->
                        @php
                          $profilePic = data_get($app->resume_snapshot, 'profile_picture');
                          // Check if it's already a full URL or just a path
                          $profilePicUrl = $profilePic && (str_starts_with($profilePic, 'http') || str_starts_with($profilePic, '/storage/')) 
                            ? $profilePic 
                            : ($profilePic ? asset('storage/' . $profilePic) : null);
                        @endphp
                        @if($profilePicUrl)
                          <img src="{{ $profilePicUrl }}" 
                               alt="Profile" 
                               style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #648EB5; flex-shrink:0;"
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                          <div style="width:80px; height:80px; border-radius:50%; background:#e8f0f7; display:none; align-items:center; justify-content:center; border:2px solid #648EB5; flex-shrink:0;">
                            <i class="fas fa-user" style="font-size:32px; color:#648EB5;"></i>
                          </div>
                        @else
                          <div style="width:80px; height:80px; border-radius:50%; background:#e8f0f7; display:flex; align-items:center; justify-content:center; border:2px solid #648EB5; flex-shrink:0;">
                            <i class="fas fa-user" style="font-size:32px; color:#648EB5;"></i>
                          </div>
                        @endif

                        <div class="applicant-info">
                          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                              <div style="font-weight:600; color:#333; font-size:15px; display:flex; align-items:center; gap:8px;">
                                {{ data_get($app->resume_snapshot, 'first_name') }} {{ data_get($app->resume_snapshot, 'last_name') }}
                                @php
                                  $empStatus = data_get($app->resume_snapshot, 'employment_status', 'unemployed');
                                @endphp
                                @if($empStatus === 'employed')
                                  <span style="background:#28a745; color:#fff; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:600;">
                                    <i class="fas fa-briefcase"></i> EMPLOYED
                                  </span>
                                @else
                                  <span style="background:#17a2b8; color:#fff; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:600;">
                                    <i class="fas fa-search"></i> SEEKING
                                  </span>
                                @endif
                              </div>
                              @if($empStatus === 'employed' && data_get($app->resume_snapshot, 'hired_by_company'))
                                <div style="font-size:12px; color:#28a745; margin-top:4px;">
                                  <i class="fas fa-building"></i> Currently at: <strong>{{ data_get($app->resume_snapshot, 'hired_by_company') }}</strong>
                                </div>
                              @endif
                              <div style="display:flex; gap:16px; margin-top:6px; color:#555; font-size:13px; flex-wrap: wrap;">
                                @if(data_get($app->resume_snapshot, 'location'))
                                  <div><i class="fas fa-map-marker-alt" style="color:#648EB5;"></i> {{ toDisplayString(data_get($app->resume_snapshot, 'location')) }}</div>
                                @endif
                                @if(data_get($app->resume_snapshot, 'email'))
                                  <div><i class="fas fa-envelope"></i> <a href="mailto:{{ data_get($app->resume_snapshot, 'email') }}" style="color:#648EB5; text-decoration:none;">{{ data_get($app->resume_snapshot, 'email') }}</a></div>
                                @endif
                                @if(data_get($app->resume_snapshot, 'phone_number'))
                                  <div><i class="fas fa-phone"></i> <a href="tel:{{ data_get($app->resume_snapshot, 'phone_number') }}" style="color:#648EB5; text-decoration:none;">{{ data_get($app->resume_snapshot, 'phone_number') }}</a></div>
                                @endif
                                <div><i class="fas fa-calendar"></i> Applied {{ $app->created_at->format('M d, Y') }}</div>
                              </div>
                            </div>
                            @php
                      $map = ['pending'=>'b-pending','reviewing'=>'b-reviewing','for_interview'=>'b-for_interview','interviewed'=>'b-interviewed','accepted'=>'b-accepted','rejected'=>'b-rejected'];
                      $statusLabels = ['for_interview' => 'For Interview', 'interviewed' => 'Interviewed', 'pending' => 'Pending', 'reviewing' => 'Reviewing', 'accepted' => 'Accepted', 'rejected' => 'Rejected'];
                            @endphp
                     <span class="badge {{ $map[$app->status] ?? '' }}">{{ $statusLabels[$app->status] ?? ucfirst($app->status) }}</span>
                          </div>

                          @if(data_get($app->resume_snapshot, 'summary'))
                            <div style="margin-top:10px; font-size:13px; color:#444;">{{ \Illuminate\Support\Str::limit(toDisplayString(data_get($app->resume_snapshot, 'summary')), 180) }}</div>
                          @endif
                          
                          @if(data_get($app->resume_snapshot, 'location') || data_get($app->resume_snapshot, 'skills'))
                            <div style="margin-top:10px; display:flex; gap:16px; font-size:12px; color:#666;">
                              @if(data_get($app->resume_snapshot, 'location'))
                                <div><i class="fas fa-map-marker-alt" style="color:#648EB5;"></i> {{ toDisplayString(data_get($app->resume_snapshot, 'location')) }}</div>
                              @endif
                              @if(data_get($app->resume_snapshot, 'skills'))
                                <div><i class="fas fa-cog" style="color:#648EB5;"></i> Skills: {{ \Illuminate\Support\Str::limit(toDisplayString(data_get($app->resume_snapshot, 'skills')), 50) }}</div>
                              @endif
                            </div>
                          @endif

                          <!-- View Full Profile Button -->
                          <button class="btn-view-profile" onclick="toggleApplicantDetails(this, event)">
                            <i class="fas fa-chevron-down"></i> View Full Profile
                          </button>

                          <!-- Open dedicated Applicant Profile Page -->
                          <a href="{{ route('employer.applicants.show', $app) }}" class="btn-view-profile" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-left:8px;">
                            <i class="fas fa-external-link-alt"></i> Open Profile Page
                          </a>

                          <!-- Full Applicant Details -->
                          <div class="applicant-full-details">
                            <h5 style="color:#334A5E; margin-bottom:10px; font-size:14px;"><i class="fas fa-file-alt"></i> Complete Application</h5>
                            
                            @if(data_get($app->resume_snapshot, 'cover_letter'))
                              <div style="background:#f8f9fa; padding:12px; border-radius:6px; margin-bottom:12px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;">Cover Letter</h6>
                                <p style="margin:0; font-size:13px; color:#555; white-space:pre-wrap; line-height:1.6;">{{ toDisplayString(data_get($app->resume_snapshot, 'cover_letter')) }}</p>
                              </div>
                            @endif

                            @if(data_get($app->resume_snapshot, 'skills'))
                              <div style="margin-bottom:12px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;"><i class="fas fa-cogs"></i> Skills</h6>
                                <p style="margin:0; font-size:13px; color:#555; line-height:1.6;">{{ toDisplayString(data_get($app->resume_snapshot, 'skills')) }}</p>
                              </div>
                            @endif

                            @if(data_get($app->resume_snapshot, 'education'))
                              <div style="margin-bottom:12px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;"><i class="fas fa-graduation-cap"></i> Education</h6>
                                <p style="margin:0; font-size:13px; color:#555; white-space:pre-wrap; line-height:1.6;">{{ toDisplayString(data_get($app->resume_snapshot, 'education')) }}</p>
                              </div>
                            @endif

                            @if(data_get($app->resume_snapshot, 'experience'))
                              <div style="margin-bottom:12px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;"><i class="fas fa-briefcase"></i> Work Experience</h6>
                                <p style="margin:0; font-size:13px; color:#555; white-space:pre-wrap; line-height:1.6;">{{ toDisplayString(data_get($app->resume_snapshot, 'experience')) }}</p>
                              </div>
                            @endif

                            @if(data_get($app->resume_snapshot, 'certifications'))
                              <div style="margin-bottom:12px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;"><i class="fas fa-certificate"></i> Certifications</h6>
                                <p style="margin:0; font-size:13px; color:#555; white-space:pre-wrap; line-height:1.6;">{{ toDisplayString(data_get($app->resume_snapshot, 'certifications')) }}</p>
                              </div>
                            @endif

                            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; margin-top:12px; font-size:12px; color:#666;">
                              @if(data_get($app->resume_snapshot, 'birthdate'))
                                <div><i class="fas fa-birthday-cake" style="color:#648EB5;"></i> <strong>Birthdate:</strong> {{ \Carbon\Carbon::parse(data_get($app->resume_snapshot, 'birthdate'))->format('M d, Y') }}</div>
                              @endif
                              @if(data_get($app->resume_snapshot, 'gender'))
                                <div><i class="fas fa-venus-mars" style="color:#648EB5;"></i> <strong>Gender:</strong> {{ ucfirst(toDisplayString(data_get($app->resume_snapshot, 'gender'))) }}</div>
                              @endif
                              @if(data_get($app->resume_snapshot, 'nationality'))
                                <div><i class="fas fa-flag" style="color:#648EB5;"></i> <strong>Nationality:</strong> {{ toDisplayString(data_get($app->resume_snapshot, 'nationality')) }}</div>
                              @endif
                              @if(data_get($app->resume_snapshot, 'languages'))
                                <div><i class="fas fa-language" style="color:#648EB5;"></i> <strong>Languages:</strong> {{ toDisplayString(data_get($app->resume_snapshot, 'languages')) }}</div>
                              @endif
                            </div>

                            <!-- Resume File Download -->
                            @if(data_get($app->resume_snapshot, 'resume_file'))
                              <div style="margin-top:16px; padding:12px; background:#e8f0f7; border-radius:6px; border-left:3px solid #648EB5;">
                                <div style="display:flex; align-items:center; justify-content:space-between;">
                                  <div>
                                    <i class="fas fa-file-pdf" style="font-size:20px; color:#648EB5; margin-right:8px;"></i>
                                    <strong style="color:#334A5E;">Resume/CV Available</strong>
                                  </div>
                                  <a href="{{ asset('storage/' . data_get($app->resume_snapshot, 'resume_file')) }}" 
                                     target="_blank" 
                                     download
                                     style="background:#648EB5; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; transition:all .3s;"
                                     onmouseover="this.style.background='#334A5E'"
                                     onmouseout="this.style.background='#648EB5'">
                                    <i class="fas fa-download"></i> Download Resume
                                  </a>
                                </div>
                              </div>
                            @endif

                            <!-- Portfolio Links -->
                            @if(data_get($app->resume_snapshot, 'portfolio_links'))
                              <div style="margin-top:16px;">
                                <h6 style="color:#334A5E; margin:0 0 8px 0; font-size:13px; font-weight:600;"><i class="fas fa-link"></i> Portfolio & Links</h6>
                                @php
                                  $links = toDisplayString(data_get($app->resume_snapshot, 'portfolio_links'));
                                  $linkArray = array_map('trim', explode(',', $links));
                                @endphp
                                <div style="display:flex; flex-direction:column; gap:6px;">
                                  @foreach($linkArray as $link)
                                    @if($link)
                                      <a href="{{ $link }}" target="_blank" style="color:#648EB5; text-decoration:none; font-size:13px;">
                                        <i class="fas fa-external-link-alt"></i> {{ $link }}
                                      </a>
                                    @endif
                                  @endforeach
                                </div>
                              </div>
                            @endif

                            <!-- Availability -->
                            @if(data_get($app->resume_snapshot, 'availability'))
                              <div style="margin-top:16px; padding:10px; background:#d4edda; border-radius:6px; border-left:3px solid #28a745;">
                                <strong style="color:#155724;"><i class="fas fa-calendar-check"></i> Availability:</strong>
                                <span style="color:#155724;">
                                  @php
                                    $avail = data_get($app->resume_snapshot, 'availability');
                                    echo match($avail) {
                                      'immediate' => 'Available Immediately',
                                      '2_weeks' => 'Available in 2 Weeks',
                                      '1_month' => 'Available in 1 Month',
                                      default => ucfirst($avail)
                                    };
                                  @endphp
                                </span>
                              </div>
                            @endif
                          </div>
                        </div>
                        <div class="actions">
                          <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                            @csrf
                            <input type="hidden" name="status" value="reviewing">
                            <button type="submit" class="review" title="Mark as Reviewing"><i class="fas fa-eye"></i></button>
                          </form>
                          <!-- For Interview: opens scheduling modal -->
                          <button type="button" class="interview" title="Set For Interview" onclick="openInterviewModal({{ $app->id }})"><i class="fas fa-calendar-alt"></i></button>
                          
                          <!-- Mark Interviewed: after onsite interview done -->
                          <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                            @csrf
                            <input type="hidden" name="status" value="interviewed">
                            <button type="submit" class="interviewed-btn" title="Mark Interviewed"><i class="fas fa-user-check"></i></button>
                          </form>
                          <form method="POST" action="{{ route('employer.applications.updateStatus', $app) }}">
                            @csrf
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="accept" title="Accept (Hire)" onclick="return confirm('Are you sure you want to hire this applicant? This will be recorded in your hiring history.')"><i class="fas fa-check"></i></button>
                          </form>
                          <button type="button" class="reject" title="Reject" onclick="openRejectModal({{ $app->id }}, '{{ $app->resume_snapshot['first_name'] ?? '' }} {{ $app->resume_snapshot['last_name'] ?? '' }}')"><i class="fas fa-times"></i></button>
                          <form method="POST" action="{{ route('employer.applications.destroy', $app) }}" onsubmit="return confirm('Are you sure you want to delete this application? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" title="Delete Application" style="background:#6c757d; color:#fff; border:none; padding:8px 10px; border-radius:6px; font-size:12px; cursor:pointer;"><i class="fas fa-trash"></i></button>
                          </form>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div style="text-align:center; color:#999; padding:24px; background:#f8f9fa; border-radius:8px;">
                    <i class="fas fa-inbox" style="font-size:32px; opacity:0.4; margin-bottom:8px; display:block;"></i>
                    No applicants for this position yet.
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center; color:#666; padding:32px;">
          <i class="fas fa-briefcase" style="font-size:48px; opacity:0.4; margin-bottom:8px;"></i>
          <div>No job postings yet. Create your first job posting to start receiving applications!</div>
        </div>
      @endif
    </div>
  </div>

  <script>
    // Load expanded state from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
      const expandedJobs = JSON.parse(localStorage.getItem('expandedApplicantJobs') || '[]');
      
      document.querySelectorAll('.job-card').forEach((card, index) => {
        if (expandedJobs.includes(index)) {
          card.classList.add('expanded');
          const icon = card.querySelector('.fa-chevron-down');
          if (icon) icon.style.transform = 'rotate(180deg)';
        }
      });
    });

    function toggleJob(card) {
      const wasExpanded = card.classList.contains('expanded');
      const cards = Array.from(document.querySelectorAll('.job-card'));
      const cardIndex = cards.indexOf(card);
      
      // Toggle clicked card
      if (wasExpanded) {
        card.classList.remove('expanded');
        const icon = card.querySelector('.fa-chevron-down');
        if (icon) icon.style.transform = 'rotate(0deg)';
      } else {
        card.classList.add('expanded');
        const icon = card.querySelector('.fa-chevron-down');
        if (icon) icon.style.transform = 'rotate(180deg)';
      }
      
      // Save expanded state to localStorage
      const expandedJobs = [];
      cards.forEach((c, index) => {
        if (c.classList.contains('expanded')) {
          expandedJobs.push(index);
        }
      });
      localStorage.setItem('expandedApplicantJobs', JSON.stringify(expandedJobs));
    }

    function toggleApplicantDetails(button, event) {
      event.stopPropagation();
      const appCard = button.closest('.app-card');
      const details = appCard.querySelector('.applicant-full-details');
      const icon = button.querySelector('i');
      
      if (details.classList.contains('expanded')) {
        details.classList.remove('expanded');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        button.innerHTML = '<i class="fas fa-chevron-down"></i> View Full Profile';
      } else {
        details.classList.add('expanded');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        button.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Full Profile';
      }
    }

    // Interview Modal Functions
    let currentInterviewAppId = null;
    function openInterviewModal(appId) {
      currentInterviewAppId = appId;
      document.getElementById('interviewModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    function closeInterviewModal() {
      currentInterviewAppId = null;
      document.getElementById('interviewModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    function submitInterviewSchedule() {
      const date = document.getElementById('interviewDate').value;
      const location = document.getElementById('interviewLocation').value;
      const notes = document.getElementById('interviewNotes').value;
      if (!date) { alert('Please choose an interview date.'); return; }
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/employer/applications/${currentInterviewAppId}/status`;
      const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      const status = document.createElement('input'); status.type = 'hidden'; status.name = 'status'; status.value = 'for_interview';
      const fDate = document.createElement('input'); fDate.type = 'hidden'; fDate.name = 'interview_date'; fDate.value = date;
      const fLoc = document.createElement('input'); fLoc.type = 'hidden'; fLoc.name = 'interview_location'; fLoc.value = location;
      const fNotes = document.createElement('input'); fNotes.type = 'hidden'; fNotes.name = 'interview_notes'; fNotes.value = notes;
      form.append(csrf, status, fDate, fLoc, fNotes);
      document.body.appendChild(form);
      form.submit();
    }

    // Rejection Modal Functions
    let currentRejectAppId = null;
    
    function openRejectModal(appId, applicantName) {
      currentRejectAppId = appId;
      document.getElementById('rejectApplicantName').textContent = applicantName;
      document.getElementById('rejectModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
      currentRejectAppId = null;
      document.getElementById('rejectModal').style.display = 'none';
      document.getElementById('rejectionReason').value = '';
      document.body.style.overflow = 'auto';
    }

    function submitRejection() {
      if (!currentRejectAppId) return;
      
      const reason = document.getElementById('rejectionReason').value.trim();
      if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
      }

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/employer/applications/${currentRejectAppId}/status`;
      
      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = '{{ csrf_token() }}';
      
      const status = document.createElement('input');
      status.type = 'hidden';
      status.name = 'status';
      status.value = 'rejected';
      
      const reasonInput = document.createElement('input');
      reasonInput.type = 'hidden';
      reasonInput.name = 'rejection_reason';
      reasonInput.value = reason;
      
      form.appendChild(csrf);
      form.appendChild(status);
      form.appendChild(reasonInput);
      document.body.appendChild(form);
      form.submit();
    }
  </script>

  <!-- Interview Scheduling Modal -->
  <div id="interviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; max-width:520px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
      <h3 style="margin:0 0 20px 0; color:#17a2b8; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-calendar-alt"></i> Schedule Interview
      </h3>
      <div style="display:flex; flex-direction:column; gap:12px;">
        <label style="font-weight:600; color:#334A5E;">Interview Date & Time</label>
        <input type="datetime-local" id="interviewDate" style="padding:10px; border:1px solid #ddd; border-radius:6px;">
        <label style="font-weight:600; color:#334A5E;">Location</label>
        <input type="text" id="interviewLocation" placeholder="Company office or online meeting link" style="padding:10px; border:1px solid #ddd; border-radius:6px;">
        <label style="font-weight:600; color:#334A5E;">Notes</label>
        <textarea id="interviewNotes" placeholder="Optional instructions (what to bring, who to look for)" style="padding:10px; border:1px solid #ddd; border-radius:6px; min-height:90px;"></textarea>
      </div>
      <div style="display:flex; gap:10px; margin-top:20px; justify-content:flex-end;">
        <button type="button" onclick="closeInterviewModal()" style="padding:10px 20px; background:#6c757d; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">Cancel</button>
        <button type="button" onclick="submitInterviewSchedule()" style="padding:10px 20px; background:#17a2b8; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;"><i class="fas fa-save"></i> Save</button>
      </div>
    </div>
  </div>

  <!-- Rejection Reason Modal -->
  <div id="rejectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:30px; max-width:500px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.3);">
      <h3 style="margin:0 0 20px 0; color:#dc3545; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-times-circle"></i> Reject Application
      </h3>
      <p style="margin:0 0 15px 0; color:#555;">
        You are about to reject the application from <strong id="rejectApplicantName"></strong>.
      </p>
      <p style="margin:0 0 15px 0; color:#666; font-size:14px;">
        Please provide a reason (e.g., "Skills do not match requirements", "Position already filled", etc.):
      </p>
      <textarea id="rejectionReason" placeholder="Enter rejection reason..." style="width:100%; min-height:100px; padding:12px; border:1px solid #ddd; border-radius:6px; font-family:inherit; font-size:14px; resize:vertical;" maxlength="500"></textarea>
      <div style="display:flex; gap:10px; margin-top:20px; justify-content:flex-end;">
        <button type="button" onclick="closeRejectModal()" style="padding:10px 20px; background:#6c757d; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
          Cancel
        </button>
        <button type="button" onclick="submitRejection()" style="padding:10px 20px; background:#dc3545; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
          <i class="fas fa-times"></i> Confirm Rejection
        </button>
      </div>
    </div>
  </div>

</body>
</html>
