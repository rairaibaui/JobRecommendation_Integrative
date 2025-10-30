<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employees - Employer | Job Portal Mandaluyong</title>
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
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .employee-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; display:flex; align-items:flex-start; gap:16px; transition: transform .2s, box-shadow .2s; background:#fff; }
  .employee-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .badge-hired { background:#d1e7dd; color:#0f5132; padding:6px 10px; border-radius:12px; font-size:12px; font-weight:600; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <i class="fas fa-bars"></i>
      <span>EMPLOYER • EMPLOYEES</span>
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
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn active"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-family:'Poppins', sans-serif; font-size:22px; color:#334A5E;">Accepted Employees</h2>
        <div style="background:#fff; border-radius:10px; padding:12px 16px; border-left:4px solid #648EB5;">
          <div style="font-size:24px; color:#334A5E; font-weight:700;">{{ $stats['total'] }}</div>
          <div style="font-size:12px; color:#666;">Total Employees</div>
        </div>
      </div>

      @if($employees->count() > 0)
        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($employees as $rec)
            <div class="employee-card">
              @php
                $pic = data_get($rec->applicant_snapshot, 'profile_picture');
                $picUrl = $pic && (str_starts_with($pic, 'http') || str_starts_with($pic, '/storage/')) ? $pic : ($pic ? asset('storage/' . $pic) : null);
              @endphp
              @if($picUrl)
                <img src="{{ $picUrl }}" alt="Profile" style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid #648EB5; flex-shrink:0;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="width:64px; height:64px; border-radius:50%; background:#e8f0f7; display:none; align-items:center; justify-content:center; border:2px solid #648EB5; flex-shrink:0;">
                  <i class="fas fa-user" style="font-size:24px; color:#648EB5;"></i>
                </div>
              @else
                <div style="width:64px; height:64px; border-radius:50%; background:#e8f0f7; display:flex; align-items:center; justify-content:center; border:2px solid #648EB5; flex-shrink:0;">
                  <i class="fas fa-user" style="font-size:24px; color:#648EB5;"></i>
                </div>
              @endif

              <div style="flex:1; min-width:0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                  <div>
                    <div style="font-weight:600; color:#333; font-size:15px;">
                      {{ data_get($rec->applicant_snapshot, 'first_name') }} {{ data_get($rec->applicant_snapshot, 'last_name') }}
                    </div>
                    <div style="color:#666; font-size:13px; margin-top:4px;">
                      <i class="fas fa-briefcase"></i> {{ $rec->job_title }}
                    </div>
                    <div style="display:flex; gap:12px; margin-top:6px; color:#555; font-size:13px; flex-wrap:wrap;">
                      @if(data_get($rec->applicant_snapshot, 'email'))
                        <div><i class="fas fa-envelope"></i> <a href="mailto:{{ data_get($rec->applicant_snapshot, 'email') }}" style="color:#648EB5; text-decoration:none;">{{ data_get($rec->applicant_snapshot, 'email') }}</a></div>
                      @endif
                      @if(data_get($rec->applicant_snapshot, 'phone_number'))
                        <div><i class="fas fa-phone"></i> <a href="tel:{{ data_get($rec->applicant_snapshot, 'phone_number') }}" style="color:#648EB5; text-decoration:none;">{{ data_get($rec->applicant_snapshot, 'phone_number') }}</a></div>
                      @endif
                      @if(data_get($rec->applicant_snapshot, 'location'))
                        <div><i class="fas fa-map-marker-alt"></i> {{ data_get($rec->applicant_snapshot, 'location') }}</div>
                      @endif
                    </div>
                  </div>
                  <span class="badge-hired"><i class="fas fa-check"></i> HIRED</span>
                </div>
                <div style="margin-top:8px; font-size:12px; color:#666;">
                  <i class="fas fa-calendar"></i> Hired on {{ $rec->decision_date->format('M d, Y') }}
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center; color:#999; padding:24px; background:#f8f9fa; border-radius:8px;">
          <i class="fas fa-user-check" style="font-size:32px; opacity:0.4; margin-bottom:8px; display:block;"></i>
          No accepted employees yet.
        </div>
      @endif
    </div>
  </div>
</body>
</html>
