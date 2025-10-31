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
  .employee-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; display:flex; align-items:flex-start; gap:16px; transition: transform .2s, box-shadow .2s; background:#fff; }
  .employee-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .employee-card.accepted-employee-item:hover, .employee-card.resigned-employee-item:hover { 
    border-color: #648EB5; 
    box-shadow: 0 4px 16px rgba(100,142,181,0.2); 
    cursor: pointer;
  }
  .badge-hired { background:#d1e7dd; color:#0f5132; padding:6px 10px; border-radius:12px; font-size:12px; font-weight:600; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>EMPLOYER • EMPLOYEES</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
      @include('partials.notifications')
    </div>
  </div>

  <div class="sidebar">
    <div class="profile-ellipse"><div class="profile-icon">@if($user->profile_picture)<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" style="cursor:pointer;" onclick="showEmpProfilePictureModal()">@else<i class="fa fa-building" style="cursor:pointer;" onclick="showEmpProfilePictureModal()"></i>@endif</div></div>
    <div class="company-name" title="{{ Auth::user()->company_name }}"><i class="fas fa-building"></i> {{ Auth::user()->company_name ?? 'Company Name' }}</div>
    <div class="company-badge">Company</div>
    
    <script>
    function showEmpProfilePictureModal() {
      const oldModal = document.getElementById('empProfilePicModal');
      if (oldModal) oldModal.remove();
      const picUrl = @json($user->profile_picture ? asset('storage/' . $user->profile_picture) : null);
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
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn active"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
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

  <div class="main">
    <!-- Accepted Employees Section -->
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-family:'Poppins', sans-serif; font-size:22px; color:#334A5E;">
          <i class="fas fa-user-check" style="color:#0f5132; margin-right:8px;"></i>Accepted Employees
        </h2>
        <div style="background:#fff; border-radius:10px; padding:12px 16px; border-left:4px solid #0f5132;">
          <div style="font-size:24px; color:#334A5E; font-weight:700;">{{ $stats['total'] }}</div>
          <div style="font-size:12px; color:#666;">Currently Employed</div>
        </div>
      </div>

      <!-- Search Bar for Accepted Employees -->
      <div style="margin-bottom:16px; position:relative;">
        <input type="text" 
               id="searchAccepted" 
               placeholder="Search accepted employees by name, position, email, or phone..." 
               style="width:100%; padding:12px 16px 12px 44px; border:2px solid #e0e0e0; border-radius:10px; font-size:14px; font-family:'Roboto', sans-serif; transition:all 0.3s;"
               oninput="searchEmployees('accepted')"
               onfocus="this.style.borderColor='#0f5132'; this.style.boxShadow='0 0 0 3px rgba(15,81,50,0.1)'"
               onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
        <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#999; font-size:16px;"></i>
      </div>

      @if($employees->count() > 0)
        <div id="acceptedEmployeesList" style="display:flex; flex-direction:column; gap:12px;">
          @foreach($employees as $rec)
            <div class="employee-card accepted-employee-item" 
                 style="cursor:pointer;" 
                 onclick="showEmployeeDetails({{ json_encode($rec) }}, 'accepted')"
                 data-name="{{ strtolower(data_get($rec->applicant_snapshot, 'first_name') . ' ' . data_get($rec->applicant_snapshot, 'last_name')) }}"
                 data-position="{{ strtolower($rec->job_title ?? '') }}"
                 data-email="{{ strtolower(data_get($rec->applicant_snapshot, 'email') ?? '') }}"
                 data-phone="{{ strtolower(data_get($rec->applicant_snapshot, 'phone_number') ?? '') }}">
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
                    <div style="color:#648EB5; font-size:14px; margin-top:4px; font-weight:600;">
                      <i class="fas fa-briefcase"></i> {{ $rec->job_title ?? 'Position Not Specified' }}
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
                  <div style="display:flex; gap:8px; align-items:center;">
                    @php $isStillEmployed = optional($rec->jobSeeker)->employment_status === 'employed'; @endphp
                    <span class="badge-hired" style="background: {{ $isStillEmployed ? '#d1e7dd' : '#e2e3e5' }}; color: {{ $isStillEmployed ? '#0f5132' : '#6c757d' }};">
                      <i class="fas {{ $isStillEmployed ? 'fa-check' : 'fa-user-slash' }}"></i> {{ $isStillEmployed ? 'EMPLOYED' : 'NO LONGER WORKING' }}
                    </span>
                    @if($isStillEmployed)
                      <button type="button" onclick="event.stopPropagation(); openTerminateModal({{ $rec->jobSeeker->id }}, '{{ addslashes(data_get($rec->applicant_snapshot,'first_name').' '.data_get($rec->applicant_snapshot,'last_name')) }}')" class="edit-btn" style="background:#E53935;color:#fff;border:none;">
                        <i class="fas fa-user-times"></i> Terminate
                      </button>
                    @endif
                  </div>
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

    <!-- Resigned/Terminated Employees Section -->
    <div class="card" style="margin-top:20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-family:'Poppins', sans-serif; font-size:22px; color:#334A5E;">
          <i class="fas fa-user-slash" style="color:#6c757d; margin-right:8px;"></i>Resigned / Terminated Employees
        </h2>
        <div style="background:#fff; border-radius:10px; padding:12px 16px; border-left:4px solid #6c757d;">
          <div style="font-size:24px; color:#334A5E; font-weight:700;">{{ $stats['resigned'] ?? 0 }}</div>
          <div style="font-size:12px; color:#666;">Former Employees</div>
        </div>
      </div>

      <!-- Search Bar for Resigned/Terminated Employees -->
      <div style="margin-bottom:16px; position:relative;">
        <input type="text" 
               id="searchResigned" 
               placeholder="Search resigned/terminated employees by name, position, email, or phone..." 
               style="width:100%; padding:12px 16px 12px 44px; border:2px solid #e0e0e0; border-radius:10px; font-size:14px; font-family:'Roboto', sans-serif; transition:all 0.3s;"
               oninput="searchEmployees('resigned')"
               onfocus="this.style.borderColor='#6c757d'; this.style.boxShadow='0 0 0 3px rgba(108,117,125,0.1)'"
               onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
        <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#999; font-size:16px;"></i>
      </div>

      @if(isset($resignedEmployees) && $resignedEmployees->count() > 0)
        <div id="resignedEmployeesList" style="display:flex; flex-direction:column; gap:12px;">
          @foreach($resignedEmployees as $rec)
            <div class="employee-card resigned-employee-item" 
                 style="opacity:0.85; cursor:pointer;" 
                 onclick="showEmployeeDetails({{ json_encode($rec) }}, 'resigned')"
                 data-name="{{ strtolower(data_get($rec->applicant_snapshot, 'first_name') . ' ' . data_get($rec->applicant_snapshot, 'last_name')) }}"
                 data-position="{{ strtolower($rec->job_title ?? '') }}"
                 data-email="{{ strtolower(data_get($rec->applicant_snapshot, 'email') ?? '') }}"
                 data-phone="{{ strtolower(data_get($rec->applicant_snapshot, 'phone_number') ?? '') }}">
              @php
                $pic = data_get($rec->applicant_snapshot, 'profile_picture');
                $picUrl = $pic && (str_starts_with($pic, 'http') || str_starts_with($pic, '/storage/')) ? $pic : ($pic ? asset('storage/' . $pic) : null);
              @endphp
              @if($picUrl)
                <img src="{{ $picUrl }}" alt="Profile" style="width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid #6c757d; flex-shrink:0; filter:grayscale(30%);" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="width:64px; height:64px; border-radius:50%; background:#e8e9ea; display:none; align-items:center; justify-content:center; border:2px solid #6c757d; flex-shrink:0;">
                  <i class="fas fa-user" style="font-size:24px; color:#6c757d;"></i>
                </div>
              @else
                <div style="width:64px; height:64px; border-radius:50%; background:#e8e9ea; display:flex; align-items:center; justify-content:center; border:2px solid #6c757d; flex-shrink:0;">
                  <i class="fas fa-user" style="font-size:24px; color:#6c757d;"></i>
                </div>
              @endif

              <div style="flex:1; min-width:0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                  <div>
                    <div style="font-weight:600; color:#333; font-size:15px;">
                      {{ data_get($rec->applicant_snapshot, 'first_name') }} {{ data_get($rec->applicant_snapshot, 'last_name') }}
                    </div>
                    <div style="color:#6c757d; font-size:14px; margin-top:4px; font-weight:600;">
                      <i class="fas fa-briefcase"></i> {{ $rec->job_title ?? 'Position Not Specified' }}
                    </div>
                    <div style="display:flex; gap:12px; margin-top:6px; color:#555; font-size:13px; flex-wrap:wrap;">
                      @if(data_get($rec->applicant_snapshot, 'email'))
                        <div><i class="fas fa-envelope"></i> {{ data_get($rec->applicant_snapshot, 'email') }}</div>
                      @endif
                      @if(data_get($rec->applicant_snapshot, 'phone_number'))
                        <div><i class="fas fa-phone"></i> {{ data_get($rec->applicant_snapshot, 'phone_number') }}</div>
                      @endif
                      @if(data_get($rec->applicant_snapshot, 'location'))
                        <div><i class="fas fa-map-marker-alt"></i> {{ data_get($rec->applicant_snapshot, 'location') }}</div>
                      @endif
                    </div>
                    @if($rec->rejection_reason && $rec->rejection_reason !== 'Employee resigned')
                      <div style="margin-top:8px; padding:8px 12px; background:#fff3cd; border-left:3px solid #ffc107; border-radius:4px;">
                        <div style="font-size:11px; color:#856404; font-weight:600; margin-bottom:2px;">
                          {{ isset($rec->is_terminated) && $rec->is_terminated ? 'TERMINATION REASON:' : 'RESIGNATION DETAILS:' }}
                        </div>
                        <div style="font-size:13px; color:#856404;">{{ $rec->rejection_reason }}</div>
                      </div>
                    @endif
                  </div>
                  <div style="display:flex; gap:8px; align-items:center;">
                    @php
                      $isTerminated = isset($rec->is_terminated) && $rec->is_terminated;
                      $badgeText = $isTerminated ? 'TERMINATED' : 'RESIGNED';
                      $badgeIcon = $isTerminated ? 'fa-user-times' : 'fa-sign-out-alt';
                    @endphp
                    <span class="badge-hired" style="background:#e2e3e5; color:#6c757d;">
                      <i class="fas {{ $badgeIcon }}"></i> {{ $badgeText }}
                    </span>
                  </div>
                </div>
                <div style="margin-top:8px; font-size:12px; color:#666;">
                  @if(isset($rec->is_terminated) && $rec->is_terminated)
                    <i class="fas fa-calendar"></i> Terminated on {{ $rec->decision_date->format('M d, Y') }}
                  @else
                    <i class="fas fa-calendar"></i> Last worked on {{ $rec->decision_date->format('M d, Y') }}
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center; color:#999; padding:24px; background:#f8f9fa; border-radius:8px;">
          <i class="fas fa-user-slash" style="font-size:32px; opacity:0.4; margin-bottom:8px; display:block;"></i>
          No resigned or terminated employees.
        </div>
      @endif
    </div>
  </div>
  <!-- Termination Modal -->
  <!-- Terminate Modal -->
  <div id="terminateModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div style="background:#fff; border-radius:16px; padding:0; max-width:480px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.4); overflow:hidden; animation:slideIn 0.3s ease-out;">
      <!-- Header -->
      <div style="background:linear-gradient(135deg, #E53935 0%, #C62828 100%); padding:24px; color:#fff;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
          <div style="width:48px; height:48px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-user-times" style="font-size:24px;"></i>
          </div>
          <h3 style="margin:0; font-size:22px; font-weight:700;">Terminate Employment</h3>
        </div>
        <p style="margin:0; opacity:0.9; font-size:13px;">This action will end the employee's current employment</p>
      </div>
      
      <!-- Body -->
      <div style="padding:24px;">
        <div style="background:#fff3cd; border-left:4px solid #ffc107; padding:12px 16px; border-radius:6px; margin-bottom:20px;">
          <p id="terminateText" style="color:#856404; font-size:14px; margin:0; font-weight:500;">
            <i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>
            Are you sure you want to terminate this employee?
          </p>
        </div>
        
        <div style="margin-bottom:4px;">
          <label style="font-weight:600; color:#334A5E; font-size:14px; display:block; margin-bottom:8px;">
            <i class="fas fa-comment-dots" style="margin-right:6px; color:#648EB5;"></i>
            Reason for Termination <span style="color:#999; font-weight:400;">(Optional)</span>
          </label>
          <textarea id="terminateReason" 
                    placeholder="Provide details about the termination reason..." 
                    style="width:100%; min-height:100px; padding:12px; border:2px solid #e0e0e0; border-radius:8px; font-size:14px; font-family:'Roboto', sans-serif; resize:vertical; transition:border-color 0.3s;"
                    onfocus="this.style.borderColor='#648EB5'" 
                    onblur="this.style.borderColor='#e0e0e0'"></textarea>
        </div>
      </div>
      
      <!-- Footer -->
      <div style="background:#f8f9fa; padding:20px 24px; display:flex; gap:12px; justify-content:flex-end; border-top:1px solid #e0e0e0;">
        <button type="button" 
                onclick="closeTerminateModal()" 
                style="padding:10px 24px; border-radius:8px; border:2px solid #6c757d; background:#fff; color:#6c757d; font-weight:600; cursor:pointer; font-size:14px; transition:all 0.3s;"
                onmouseover="this.style.background='#6c757d'; this.style.color='#fff';" 
                onmouseout="this.style.background='#fff'; this.style.color='#6c757d';">
          <i class="fas fa-times"></i> Cancel
        </button>
        <button type="button" 
                onclick="submitTerminate()" 
                style="padding:10px 24px; border-radius:8px; border:none; background:linear-gradient(135deg, #E53935 0%, #C62828 100%); color:#fff; font-weight:600; cursor:pointer; font-size:14px; box-shadow:0 4px 12px rgba(229,57,53,0.3); transition:all 0.3s;"
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(229,57,53,0.4)';" 
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(229,57,53,0.3)';">
          <i class="fas fa-check"></i> Confirm Termination
        </button>
      </div>
    </div>
  </div>

  <style>
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  </style>

  <script>
    let terminateUserId = null;
    function openTerminateModal(userId, name){
      terminateUserId = userId;
      document.getElementById('terminateText').innerHTML = `Are you sure you want to terminate <strong>${name}</strong>?`;
      document.getElementById('terminateModal').style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
    function closeTerminateModal(){
      terminateUserId = null;
      document.getElementById('terminateReason').value = '';
      document.getElementById('terminateModal').style.display = 'none';
      document.body.style.overflow = 'auto';
    }
    function submitTerminate(){
      if(!terminateUserId) return;
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/employer/employees/${terminateUserId}/terminate`;
      const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}';
      const reason = document.createElement('input'); reason.type='hidden'; reason.name='reason'; reason.value=document.getElementById('terminateReason').value;
      form.append(csrf, reason);
      document.body.appendChild(form);
      form.submit();
    }

    // Search functionality
    function searchEmployees(type) {
      const searchInput = type === 'accepted' ? document.getElementById('searchAccepted') : document.getElementById('searchResigned');
      const searchTerm = searchInput.value.toLowerCase().trim();
      const items = type === 'accepted' ? document.querySelectorAll('.accepted-employee-item') : document.querySelectorAll('.resigned-employee-item');
      
      let visibleCount = 0;
      items.forEach(item => {
        const name = item.getAttribute('data-name') || '';
        const position = item.getAttribute('data-position') || '';
        const email = item.getAttribute('data-email') || '';
        const phone = item.getAttribute('data-phone') || '';
        
        const matches = name.includes(searchTerm) || 
                       position.includes(searchTerm) || 
                       email.includes(searchTerm) || 
                       phone.includes(searchTerm);
        
        if (matches) {
          item.style.display = 'flex';
          visibleCount++;
        } else {
          item.style.display = 'none';
        }
      });

      // Show "no results" message if needed
      const listId = type === 'accepted' ? 'acceptedEmployeesList' : 'resignedEmployeesList';
      const list = document.getElementById(listId);
      let noResultsMsg = list.querySelector('.no-results-message');
      
      if (visibleCount === 0 && searchTerm !== '') {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('div');
          noResultsMsg.className = 'no-results-message';
          noResultsMsg.style.cssText = 'text-align:center; color:#999; padding:24px; background:#f8f9fa; border-radius:8px; margin-top:12px;';
          noResultsMsg.innerHTML = `
            <i class="fas fa-search" style="font-size:32px; opacity:0.4; margin-bottom:8px; display:block;"></i>
            No employees found matching "<strong>${searchTerm}</strong>"
          `;
          list.appendChild(noResultsMsg);
        } else {
          noResultsMsg.innerHTML = `
            <i class="fas fa-search" style="font-size:32px; opacity:0.4; margin-bottom:8px; display:block;"></i>
            No employees found matching "<strong>${searchTerm}</strong>"
          `;
        }
      } else if (noResultsMsg) {
        noResultsMsg.remove();
      }
    }

    // Show employee details modal
    function showEmployeeDetails(employee, type) {
      const snapshot = employee.applicant_snapshot || {};
      const firstName = snapshot.first_name || '';
      const lastName = snapshot.last_name || '';
      const fullName = `${firstName} ${lastName}`.trim();
      const email = snapshot.email || 'N/A';
      const phone = snapshot.phone_number || 'N/A';
      const location = snapshot.location || 'N/A';
      const position = employee.job_title || 'Position Not Specified';
      
      const pic = snapshot.profile_picture;
      const picUrl = pic && (pic.startsWith('http') || pic.startsWith('/storage/')) ? pic : (pic ? `/storage/${pic}` : null);
      
      // Calculate employment duration
      let hiredDate, endDate, duration, durationText, hiredDateStr, endDateStr;
      
      if (type === 'resigned') {
        // For resigned employees, use hire_date if available
        hiredDate = employee.hire_date ? new Date(employee.hire_date) : new Date(employee.decision_date);
        endDate = new Date(employee.decision_date);
        
        // Calculate months between dates
        const monthsDiff = (endDate.getFullYear() - hiredDate.getFullYear()) * 12 + (endDate.getMonth() - hiredDate.getMonth());
        const years = Math.floor(monthsDiff / 12);
        const months = monthsDiff % 12;
        
        if (years > 0 && months > 0) {
          durationText = `${years} year${years > 1 ? 's' : ''} and ${months} month${months > 1 ? 's' : ''}`;
        } else if (years > 0) {
          durationText = `${years} year${years > 1 ? 's' : ''}`;
        } else if (months > 0) {
          durationText = `${months} month${months > 1 ? 's' : ''}`;
        } else {
          durationText = 'Less than a month';
        }
        
        hiredDateStr = hiredDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        endDateStr = endDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      } else {
        // For current employees
        hiredDate = new Date(employee.decision_date);
        endDate = new Date();
        
        const monthsDiff = (endDate.getFullYear() - hiredDate.getFullYear()) * 12 + (endDate.getMonth() - hiredDate.getMonth());
        const years = Math.floor(monthsDiff / 12);
        const months = monthsDiff % 12;
        
        if (years > 0 && months > 0) {
          durationText = `${years} year${years > 1 ? 's' : ''} and ${months} month${months > 1 ? 's' : ''}`;
        } else if (years > 0) {
          durationText = `${years} year${years > 1 ? 's' : ''}`;
        } else if (months > 0) {
          durationText = `${months} month${months > 1 ? 's' : ''}`;
        } else {
          durationText = 'Less than a month';
        }
        
        hiredDateStr = hiredDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        endDateStr = 'Present';
      }
      
      const terminationReason = type === 'resigned' && employee.rejection_reason ? employee.rejection_reason : null;
      
      // Create modal
      const oldModal = document.getElementById('employeeDetailsModal');
      if (oldModal) oldModal.remove();
      
      const modal = document.createElement('div');
      modal.id = 'employeeDetailsModal';
      modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:10001; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); animation:fadeIn 0.3s ease-out;';
      
      const statusColor = type === 'accepted' ? '#0f5132' : '#6c757d';
      const statusBg = type === 'accepted' ? '#d1e7dd' : '#e2e3e5';
      const statusIcon = type === 'accepted' ? 'fa-user-check' : 'fa-user-slash';
      const statusText = type === 'accepted' ? 'CURRENTLY EMPLOYED' : 'TERMINATED';
      
      modal.innerHTML = `
        <div style="background:#fff; border-radius:16px; max-width:600px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,0.4); animation:slideInScale 0.3s ease-out;">
          <!-- Header -->
          <div style="background:linear-gradient(135deg, ${statusColor} 0%, ${type === 'accepted' ? '#0a3d24' : '#495057'} 100%); padding:24px; color:#fff; position:relative;">
            <button onclick="document.getElementById('employeeDetailsModal').remove(); document.body.style.overflow='auto';" 
                    style="position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.2); border:none; width:36px; height:36px; border-radius:50%; font-size:20px; cursor:pointer; color:#fff; transition:background 0.3s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'">&times;</button>
            
            <div style="display:flex; align-items:center; gap:20px;">
              ${picUrl ? 
                `<img src="${picUrl}" alt="Profile" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:4px solid rgba(255,255,255,0.3); ${type === 'resigned' ? 'filter:grayscale(30%);' : ''}">` : 
                `<div style="width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; border:4px solid rgba(255,255,255,0.3);">
                  <i class="fas fa-user" style="font-size:40px; color:rgba(255,255,255,0.7);"></i>
                </div>`
              }
              <div style="flex:1;">
                <h2 style="margin:0 0 8px 0; font-size:26px; font-weight:700;">${fullName}</h2>
                <div style="font-size:16px; opacity:0.9; margin-bottom:8px;">
                  <i class="fas fa-briefcase" style="margin-right:6px;"></i>${position}
                </div>
                <span style="display:inline-block; background:${statusBg}; color:${statusColor}; padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600;">
                  <i class="fas ${statusIcon}"></i> ${statusText}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Body -->
          <div style="padding:24px;">
            <!-- Contact Information -->
            <div style="background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:20px;">
              <h3 style="margin:0 0 16px 0; color:#334A5E; font-size:18px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-address-card" style="color:${statusColor};"></i> Contact Information
              </h3>
              <div style="display:grid; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px;">
                  <div style="width:36px; height:36px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-envelope" style="color:${statusColor};"></i>
                  </div>
                  <div>
                    <div style="font-size:12px; color:#666; font-weight:500;">Email</div>
                    <div style="font-size:14px; color:#333;">${email}</div>
                  </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                  <div style="width:36px; height:36px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-phone" style="color:${statusColor};"></i>
                  </div>
                  <div>
                    <div style="font-size:12px; color:#666; font-weight:500;">Phone</div>
                    <div style="font-size:14px; color:#333;">${phone}</div>
                  </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                  <div style="width:36px; height:36px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-map-marker-alt" style="color:${statusColor};"></i>
                  </div>
                  <div>
                    <div style="font-size:12px; color:#666; font-weight:500;">Location</div>
                    <div style="font-size:14px; color:#333;">${location}</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Employment Timeline -->
            <div style="background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:20px;">
              <h3 style="margin:0 0 16px 0; color:#334A5E; font-size:18px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-calendar-alt" style="color:${statusColor};"></i> Employment Timeline
              </h3>
              <div style="display:grid; gap:16px;">
                <div style="display:flex; align-items:start; gap:12px;">
                  <div style="width:36px; height:36px; background:${statusBg}; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-calendar-check" style="color:${statusColor};"></i>
                  </div>
                  <div>
                    <div style="font-size:12px; color:#666; font-weight:500;">
                      ${type === 'accepted' ? 'Hired Date' : 'Application/Hire Date'}
                    </div>
                    <div style="font-size:16px; color:#333; font-weight:600;">${hiredDateStr}</div>
                  </div>
                </div>
                ${type === 'resigned' ? `
                  <div style="display:flex; align-items:start; gap:12px;">
                    <div style="width:36px; height:36px; background:#fff3cd; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                      <i class="fas fa-calendar-times" style="color:#856404;"></i>
                    </div>
                    <div>
                      <div style="font-size:12px; color:#666; font-weight:500;">Termination Date</div>
                      <div style="font-size:16px; color:#333; font-weight:600;">${endDateStr}</div>
                    </div>
                  </div>
                ` : ''}
                <div style="display:flex; align-items:start; gap:12px;">
                  <div style="width:36px; height:36px; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-clock" style="color:${statusColor};"></i>
                  </div>
                  <div>
                    <div style="font-size:12px; color:#666; font-weight:500;">Duration</div>
                    <div style="font-size:16px; color:#333; font-weight:600;">${durationText}</div>
                  </div>
                </div>
              </div>
            </div>
            
            ${terminationReason ? `
              <!-- Termination Reason -->
              <div style="background:#fff3cd; border-left:4px solid #ffc107; border-radius:8px; padding:16px; margin-bottom:20px;">
                <h3 style="margin:0 0 8px 0; color:#856404; font-size:16px; display:flex; align-items:center; gap:8px;">
                  <i class="fas fa-exclamation-triangle"></i> Termination Reason
                </h3>
                <div style="font-size:14px; color:#856404; line-height:1.6;">${terminationReason}</div>
              </div>
            ` : ''}
          </div>
          
          <!-- Footer -->
          <div style="background:#f8f9fa; padding:20px 24px; display:flex; justify-content:flex-end; border-top:1px solid #e0e0e0;">
            <button onclick="document.getElementById('employeeDetailsModal').remove(); document.body.style.overflow='auto';" 
                    style="padding:10px 24px; border-radius:8px; border:none; background:${statusColor}; color:#fff; font-weight:600; cursor:pointer; font-size:14px; transition:all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)';" 
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
              <i class="fas fa-check"></i> Close
            </button>
          </div>
        </div>
      `;
      
      document.body.appendChild(modal);
      document.body.style.overflow = 'hidden';
    }
  </script>

  <style>
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    @keyframes slideInScale {
      from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
  </style>
</body>
</html>
