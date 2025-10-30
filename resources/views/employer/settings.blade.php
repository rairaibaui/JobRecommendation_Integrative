<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employer Settings - Job Portal Mandaluyong</title>
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
  .field { display:flex; flex-direction:column; gap:6px; margin-bottom:12px; }
  .field label { font-weight:600; color:#334A5E; }
  .field input, .field textarea, .field select { padding:10px; border:1px solid #ddd; border-radius:6px; }
  .btn { padding:10px 16px; border-radius:6px; border:none; cursor:pointer; }
  .btn-primary { background:#1E3A5F; color:#fff; }
  .btn-secondary { background:#6c757d; color:#fff; }
  .notice { background:#e8f0f7; border-left:4px solid #648EB5; padding:12px; border-radius:6px; color:#334A5E; margin-bottom:12px; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <i class="fas fa-bars"></i>
      <span>EMPLOYER • SETTINGS</span>
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
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
    <a href="{{ route('employer.employees') }}" class="sidebar-btn"><i class="fa fa-user-check sidebar-btn-icon"></i> Employees</a>
    <a href="{{ route('employer.analytics') }}" class="sidebar-btn"><i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics</a>
    <a href="{{ route('settings') }}" class="sidebar-btn active"><i class="fa fa-cog sidebar-btn-icon"></i> Settings</a>
  </div>

  <div class="main">
    <div class="card">
      <h2 style="margin:0 0 12px 0; color:#334A5E;">Company Profile</h2>
      @if(session('success'))
        <div class="notice"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="notice" style="background:#fdecea; border-left-color:#f44336; color:#b71c1c;">
          <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
      @endif
      <form method="POST" action="{{ route('profile.updateEmployer') }}" enctype="multipart/form-data">
        @csrf
        <div class="field">
          <label>Company Name <span style="color:#dc3545">*</span></label>
          <input type="text" name="company_name" required value="{{ old('company_name', Auth::user()->company_name) }}">
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px;">
          <div class="field">
            <label>Contact First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}">
          </div>
          <div class="field">
            <label>Contact Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}">
          </div>
        </div>
        <div class="field">
          <label>Job Title</label>
          <input type="text" name="job_title" value="{{ old('job_title', Auth::user()->job_title) }}" placeholder="e.g., HR Manager">
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px;">
          <div class="field">
            <label>Email (login)</label>
            <input type="email" value="{{ Auth::user()->email }}" readonly>
          </div>
          <div class="field">
            <label>Contact Number <span style="color:#dc3545">*</span></label>
            <input type="text" name="phone_number" required value="{{ old('phone_number', Auth::user()->phone_number) }}" placeholder="e.g., 0917 123 4567">
          </div>
        </div>
        <div class="field">
          <label>Company Address</label>
          <input type="text" name="address" value="{{ old('address', Auth::user()->address) }}" placeholder="Building, Street, Barangay, City">
        </div>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px;">
          <div class="field">
            <label>Company Logo / Photo</label>
            <input type="file" name="profile_picture" accept="image/*">
            @if(Auth::user()->profile_picture)
              <div style="margin-top:8px;">
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" width="88" height="88" style="border-radius:50%; object-fit:cover; border:2px solid #ddd;">
              </div>
            @endif
          </div>
          <div class="field">
            <label>Business Permit (PDF/JPG/PNG)</label>
            <input type="file" name="business_permit" accept=".pdf,.jpg,.jpeg,.png">
            @if(Auth::user()->business_permit_path)
              <div style="margin-top:8px;">
                Current: <a href="{{ asset('storage/' . Auth::user()->business_permit_path) }}" target="_blank">View file</a>
              </div>
            @endif
          </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:12px;">
          <a href="{{ route('change.password') }}" class="btn btn-secondary" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;"><i class="fas fa-lock"></i> Change Password</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
