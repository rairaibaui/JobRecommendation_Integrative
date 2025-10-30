<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Edit Job - Employer | Job Portal Mandaluyong</title>
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
  .company-name { align-self:center; font-family:'Roboto', sans-serif; font-size:14px; font-weight:400; color:#666; margin-bottom:20px; }
  .sidebar .sidebar-btn { align-self:flex-start; }
  .sidebar-btn { display:flex; align-items:center; gap:10px; height:39px; padding:0 10px; border-radius:8px; background:transparent; color:#000; font-size:20px; cursor:pointer; text-decoration:none; transition:all .3s; }
  .sidebar-btn:hover { background:#e8f0f7; }
  .sidebar-btn.active { background:#648EB5; box-shadow:0 7px 4px rgba(0,0,0,0.25); color:#000; width:100%; }
  .main { margin-left:290px; flex:1; display:flex; flex-direction:column; gap:20px; max-width:900px; }
  .top-navbar { position:fixed; top:0; left:0; width:100%; height:68px; background:#2B4053; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#FFF; font-family:'Poppins', sans-serif; font-size:24px; font-weight:800; z-index:1000; }
  .hamburger { margin-right:20px; color:#FFF; }
  .logout-btn { background:transparent; border:1px solid #FFF; color:#FFF; padding:8px 16px; border-radius:6px; cursor:pointer; font-size:14px; transition:all .3s; }
  .logout-btn:hover { background:#FFF; color:#2B4053; }
  .card { background:#FFF; border-radius:8px; padding:30px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .form-group { margin-bottom:20px; }
  .form-group label { display:block; margin-bottom:6px; font-weight:600; color:#333; font-size:14px; }
  .form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; font-family:'Roboto', sans-serif; }
  .form-group textarea { min-height:120px; resize:vertical; }
  .form-group small { display:block; margin-top:4px; color:#666; font-size:12px; }
  .btn-primary { background:#648EB5; color:#fff; border:none; padding:12px 24px; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; transition:all .3s; }
  .btn-primary:hover { background:#4E8EA2; transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
  .btn-secondary { background:#6c757d; color:#fff; border:none; padding:12px 24px; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer; transition:all .3s; margin-left:10px; text-decoration:none; display:inline-block; }
  .btn-secondary:hover { background:#5a6268; }
  .error { color:#dc3545; font-size:13px; margin-top:4px; }
</style>
</head>
<body>
  <div class="top-navbar">
    <div style="display:flex; align-items:center; gap:12px;">
      <span>EDIT JOB</span>
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
    <a href="{{ route('employer.jobs') }}" class="sidebar-btn active"><i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings</a>
    <a href="{{ route('employer.applicants') }}" class="sidebar-btn"><i class="fa fa-users sidebar-btn-icon"></i> Applicants</a>
    <a href="{{ route('employer.history') }}" class="sidebar-btn"><i class="fa fa-history sidebar-btn-icon"></i> History</a>
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

  <div class="main">
    <div class="card">
      <h2 style="margin-bottom:24px; color:#334A5E; font-family:'Poppins', sans-serif;">Edit Job Posting</h2>

      @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #f5c6cb;">
          <strong>Please fix the following errors:</strong>
          <ul style="margin:8px 0 0 20px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('employer.jobs.update', $jobPosting) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="title">Job Title <span style="color:red;">*</span></label>
          <input type="text" id="title" name="title" value="{{ old('title', $jobPosting->title) }}" required placeholder="e.g. Senior Web Developer">
          @error('title')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="{{ old('location', $jobPosting->location) }}" placeholder="e.g. Mandaluyong, Manila">
          @error('location')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="type">Job Type <span style="color:red;">*</span></label>
          <select id="type" name="type" required>
            <option value="Full-time" {{ old('type', $jobPosting->type) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
            <option value="Part-time" {{ old('type', $jobPosting->type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
            <option value="Contract" {{ old('type', $jobPosting->type) == 'Contract' ? 'selected' : '' }}>Contract</option>
            <option value="Internship" {{ old('type', $jobPosting->type) == 'Internship' ? 'selected' : '' }}>Internship</option>
            <option value="Freelance" {{ old('type', $jobPosting->type) == 'Freelance' ? 'selected' : '' }}>Freelance</option>
          </select>
          @error('type')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="salary">Salary Range</label>
          <input type="text" id="salary" name="salary" value="{{ old('salary', $jobPosting->salary) }}" placeholder="e.g. PHP 30,000 - 50,000/month or Negotiable">
          @error('salary')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="description">Job Description <span style="color:red;">*</span></label>
          <textarea id="description" name="description" required placeholder="Describe the role, responsibilities, and requirements...">{{ old('description', $jobPosting->description) }}</textarea>
          @error('description')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="skills">Required Skills</label>
          <input type="text" id="skills" name="skills" value="{{ old('skills', is_array($jobPosting->skills) ? implode(', ', $jobPosting->skills) : '') }}" placeholder="e.g. PHP, Laravel, JavaScript, React (comma-separated)">
          <small>Enter skills separated by commas</small>
          @error('skills')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="active" {{ old('status', $jobPosting->status) == 'active' ? 'selected' : '' }}>Active (Accepting applications)</option>
            <option value="closed" {{ old('status', $jobPosting->status) == 'closed' ? 'selected' : '' }}>Closed (Position filled / No longer hiring)</option>
            <option value="draft" {{ old('status', $jobPosting->status) == 'draft' ? 'selected' : '' }}>Draft (Not visible to job seekers)</option>
          </select>
          <small>Set to "Closed" when position is filled or no more applicants needed</small>
          @error('status')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div style="margin-top:30px;">
          <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <a href="{{ route('employer.jobs') }}" class="btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
