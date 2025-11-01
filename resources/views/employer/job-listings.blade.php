<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>My Job Postings - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
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
  .hamburger { margin-right:20px; color:#FFF; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .btn-primary { background:#648EB5; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all .3s; text-decoration:none; display:inline-block; }
  .btn-primary:hover { background:#4E8EA2; transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
  .job-card { border:1px solid #e5e7eb; border-radius:10px; padding:20px; margin-bottom:16px; transition:transform .2s, box-shadow .2s; }
  .job-card:hover { transform:translateY(-2px); box-shadow:0 8px 16px rgba(0,0,0,0.12); }
  .badge { padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600; }
  .badge-active { background:#d1e7dd; color:#0f5132; }
  .badge-draft { background:#fff3cd; color:#856404; }
  .badge-closed { background:#f8d7da; color:#842029; }
  .btn-delete { background:#dc3545; color:#fff; border:none; padding:8px 12px; border-radius:6px; font-size:12px; cursor:pointer; }
  .btn-delete:hover { background:#c82333; }
  .btn-edit { background:#648EB5; color:#fff; border:none; padding:8px 12px; border-radius:6px; font-size:12px; cursor:pointer; text-decoration:none; display:inline-block; }
  .btn-edit:hover { background:#4E8EA2; }
  .btn-close { background:#ffc107; color:#000; border:none; padding:6px 10px; border-radius:6px; font-size:11px; cursor:pointer; }
  .btn-close:hover { background:#e0a800; }
  .btn-reopen { background:#28a745; color:#fff; border:none; padding:6px 10px; border-radius:6px; font-size:11px; cursor:pointer; }
  .btn-reopen:hover { background:#218838; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>JOB POSTINGS</span>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
      @include('partials.notifications')
    </div>
  </div>

  <div class="sidebar">
    <div class="profile-ellipse"><div class="profile-icon">@if($user->profile_picture)<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" style="cursor:pointer;" onclick="showEmpProfilePictureModal()">@else<i class="fa fa-building" style="cursor:pointer;" onclick="showEmpProfilePictureModal()"></i>@endif</div></div>
    <div class="company-name" title="{{ $user->company_name }}"><i class="fas fa-building"></i> {{ $user->company_name ?? 'Company Name' }}</div>
    <div class="company-badge">Company</div>
    
    <script>
    function showEmpProfilePictureModal() {
      const oldModal = document.getElementById('empProfilePicModal');
      if (oldModal) oldModal.remove();
      const picUrl = @json($user->profile_picture ? asset('storage/' . $user->profile_picture) : null);
      const name = @json($user->company_name ?? 'Company');
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
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn active"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('settings') }}" class="sidebar-btn"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;" onsubmit="return showLogoutModal(this);">
      @csrf
      <button type="submit" class="sidebar-btn"
        style="border: none; background: #648EB5; color: #FFF; font-size: 20px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 10px; height: 39px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
        Logout
      </button>
    </form>
  </div>

  @include('partials.logout-confirm')

  <div class="main">
    @if(session('success'))
      <div class="flash-message" style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:8px; border:1px solid #c3e6cb; transition: opacity 0.3s ease;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
      </div>
    @endif

    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="font-family:'Poppins', sans-serif; color:#334A5E; margin:0;">My Job Postings</h2>
        <a href="{{ route('employer.jobs.create') }}" class="btn-primary">
          <i class="fas fa-plus"></i> Post New Job
        </a>
      </div>

      @if($jobPostings->count() > 0)
        @foreach($jobPostings as $job)
          <div class="job-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div style="flex:1;">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                  <h3 style="font-family:'Poppins', sans-serif; color:#334A5E; font-size:18px; margin:0;">{{ $job->title }}</h3>
                  <span class="badge badge-{{ strtolower($job->status) }}">{{ ucfirst($job->status) }}</span>
                </div>
                <div style="display:flex; gap:20px; color:#666; font-size:14px; margin-bottom:12px;">
                  <div><i class="fas fa-map-marker-alt" style="color:#648EB5; width:16px;"></i> {{ $job->location }}</div>
                  <div><i class="fas fa-briefcase" style="color:#648EB5; width:16px;"></i> {{ $job->type }}</div>
                  <div><i class="fas fa-money-bill-wave" style="color:#648EB5; width:16px;"></i> {{ $job->salary }}</div>
                </div>
                <div style="color:#555; font-size:14px; line-height:1.6; margin-bottom:12px;">
                  {{ \Illuminate\Support\Str::limit($job->description, 200) }}
                </div>
                @if($job->skills && count($job->skills) > 0)
                  <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    @foreach($job->skills as $skill)
                      <span style="background:#648EB5; color:#fff; padding:4px 10px; border-radius:12px; font-size:12px;">{{ $skill }}</span>
                    @endforeach
                  </div>
                @endif
              </div>
              <div style="display:flex; flex-direction:column; gap:8px; margin-left:20px;">
                <div style="text-align:right; font-size:13px; color:#666; margin-bottom:8px;">
                  <div><i class="fas fa-calendar"></i> {{ $job->created_at->format('M d, Y') }}</div>
                  <div><i class="fas fa-users"></i> {{ $job->applications->count() }} Applications</div>
                </div>
                
                <a href="{{ route('employer.jobs.edit', $job) }}" class="btn-edit">
                  <i class="fas fa-edit"></i> Edit
                </a>
                
                @if($job->status === 'active')
                  <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="closed">
                    <button type="submit" class="btn-close" title="Close this job (position filled or no longer hiring)">
                      <i class="fas fa-lock"></i> Close Job
                    </button>
                  </form>
                @elseif($job->status === 'closed')
                  <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="margin:0;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn-reopen" title="Reopen this job posting">
                      <i class="fas fa-unlock"></i> Reopen
                    </button>
                  </form>
                @endif
                
                <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return handleDeleteJob(event, this);" style="margin:0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-delete">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <div style="text-align:center; padding:40px; color:#666;">
          <i class="fas fa-briefcase" style="font-size:48px; opacity:0.3; margin-bottom:16px;"></i>
          <p style="font-size:16px;">You haven't posted any jobs yet.</p>
          <a href="{{ route('employer.jobs.create') }}" class="btn-primary" style="margin-top:16px;">
            <i class="fas fa-plus"></i> Post Your First Job
          </a>
        </div>
      @endif
    </div>
  </div>

  <script>
    // Auto-hide flash messages after 2 seconds
    document.addEventListener('DOMContentLoaded', function() {
      const flashMessage = document.querySelector('.flash-message');
      if (flashMessage) {
        setTimeout(() => {
          flashMessage.style.opacity = '0';
          setTimeout(() => flashMessage.remove(), 300);
        }, 2000);
      }
    });

    // Handle delete job
    async function handleDeleteJob(event, form) {
      event.preventDefault();
      
      const confirmed = await customConfirm(
        'Are you sure you want to delete this job posting? This action cannot be undone.',
        'Delete Job Posting',
        'Yes, Delete'
      );
      
      if (confirmed) {
        form.submit();
      }
      
      return false;
    }
  </script>

  @include('partials.custom-modals')
</body>
</html>
