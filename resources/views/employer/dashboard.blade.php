<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employer Dashboard - Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
/* Dashboard-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  .welcome { font-family: 'Poppins', sans-serif; font-size: 32px; font-weight: 600; color: #FFF; margin-bottom: 10px; }
  .stats-cards { display:flex; gap:20px; margin-bottom:20px; }
  .stat-card { flex:1; background:#FFF; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:24px; display:flex; align-items:center; gap:18px; transition:all .3s cubic-bezier(.4,0,.2,1); border:1px solid rgba(100,142,181,.1); position:relative; overflow:hidden; }
  .stat-card::before { content:''; position:absolute; top:0; left:0; width:100%; height:3px; background:linear-gradient(90deg,#648EB5 0%, #4E8EA2 100%); transform:scaleX(0); transform-origin:left; transition:transform .4s ease; }
  .stat-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(100,142,181,.15); border-color:rgba(100,142,181,.3); }
  .stat-card:hover::before { transform:scaleX(1); }
  .stat-icon { width:64px; height:64px; border-radius:12px; background:linear-gradient(135deg,#648EB5 0%, #4E8EA2 100%); display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 12px rgba(100,142,181,.25); transition:all .3s ease; }
  .stat-card:hover .stat-icon { transform:scale(1.05) rotate(-5deg); box-shadow:0 6px 16px rgba(100,142,181,.35); }
  .stat-icon i { font-size:30px; color:#FFF; transition:transform .3s ease; }
  .stat-card:hover .stat-icon i { transform:scale(1.1); }
  .stat-content h3 { font-family:'Poppins', sans-serif; font-size:32px; font-weight:700; color:#334A5E; margin:0 0 4px 0; line-height:1; }
  .stat-content p { font-family:'Roboto', sans-serif; font-size:14px; color:#6B7280; margin:0; font-weight:500; letter-spacing:.3px; }
  .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
  .section-title { font-family:'Poppins', sans-serif; font-size:24px; font-weight:600; color:#FFF; }
  .btn-primary { background:linear-gradient(135deg,#648EB5,#334A5E); color:#FFF; border:none; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:500; cursor:pointer; display:flex; align-items:center; gap:8px; transition:all .3s ease; text-decoration:none; }
  .btn-primary:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.2); }
  .jobs-container { display:grid; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); gap:20px; padding-right:10px; }
  .job-card { background:#FFF; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.08); padding:24px; transition:all .3s cubic-bezier(.4,0,.2,1); border:1px solid rgba(100,142,181,.1); position:relative; overflow:hidden; }
  .job-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:linear-gradient(180deg,#648EB5 0%, #4E8EA2 100%); transform:scaleY(0); transform-origin:top; transition:transform .3s ease; }
  .job-card:hover { transform:translateY(-6px); box-shadow:0 12px 28px rgba(100,142,181,.18); border-color:rgba(100,142,181,.3); }
  .job-card:hover::before { transform:scaleY(1); }
  .job-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; gap:12px; }
  .job-title { font-family:'Poppins', sans-serif; font-size:19px; font-weight:600; color:#334A5E; margin:0 0 6px 0; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .job-department { font-size:13px; color:#6B7280; font-weight:500; display:flex; align-items:center; gap:6px; }
  .job-department i { font-size:12px; color:#648EB5; }
  .job-status { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; white-space:nowrap; flex-shrink:0; }
  .status-active { background:linear-gradient(135deg,#d4edda 0%,#c3e6cb 100%); color:#155724; border:1px solid #c3e6cb; }
  .status-closed { background:linear-gradient(135deg,#f8d7da 0%,#f5c6cb 100%); color:#721c24; border:1px solid #f5c6cb; }
  .status-draft { background:linear-gradient(135deg,#fff3cd 0%,#ffeaa7 100%); color:#856404; border:1px solid #ffeaa7; }
  .job-details { display:flex; flex-direction:column; gap:10px; margin-bottom:18px; }
  .job-detail-item { display:flex; align-items:center; gap:10px; font-size:14px; color:#4B5563; padding:4px 0; }
  .job-detail-item i { width:18px; color:#648EB5; font-size:14px; text-align:center; }
  .job-footer { display:flex; justify-content:space-between; align-items:center; padding-top:16px; border-top:2px solid #F3F4F6; gap:12px; }
  .applications-count { display:flex; align-items:center; gap:8px; font-size:14px; color:#648EB5; font-weight:600; padding:6px 12px; background:linear-gradient(135deg,#f0f7fc 0%,#e8f4fd 100%); border-radius:8px; border:1px solid rgba(100,142,181,.2); }
  .applications-count i { font-size:16px; }
  .job-actions { display:flex; gap:8px; flex-wrap:wrap; }
  .btn-icon { width:36px; height:36px; border:none; border-radius:8px; background:#F3F4F6; color:#6B7280; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .2s cubic-bezier(.4,0,.2,1); position:relative; overflow:hidden; }
  .btn-icon:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.15); background:linear-gradient(135deg,#648EB5 0%, #4E8EA2 100%); color:#FFF; }
  @media (max-width:1024px){ .stats-cards{flex-wrap:wrap;} .stat-card{min-width:calc(50% - 10px);} .jobs-container{grid-template-columns:repeat(auto-fill,minmax(300px,1fr));} }
  @media (max-width:768px){ .stats-cards{flex-direction:column; gap:12px;} .stat-card{min-width:100%; padding:18px;} .stat-icon{width:56px; height:56px;} .stat-icon i{font-size:26px;} .stat-content h3{font-size:28px;} .jobs-container{grid-template-columns:1fr; gap:16px;} .job-card{padding:20px;} .job-header{flex-direction:column; gap:10px;} .job-status{align-self:flex-start;} .job-footer{flex-direction:column; align-items:flex-start; gap:12px;} .applications-count{width:100%; justify-content:center;} .job-actions{width:100%; justify-content:space-between;} .btn-icon{flex:1; min-width:44px;} .welcome{font-size:24px;} .section-title{font-size:20px;} .btn-primary{font-size:13px; padding:8px 16px;} }
  @media (max-width:480px){ .stat-content h3{font-size:24px;} .stat-content p{font-size:13px;} .job-title{font-size:17px;} .job-detail-item{font-size:13px;} }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <div class="welcome">Welcome, {{ $user->company_name ?? $user->first_name }}! 👋</div>

  {{-- Personal Email Verification Notice Component --}}
  <x-personal-email-notice />

  {{-- Validation Status Alerts --}}
  @if($validation)
    @php
      $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
      $companyNameMismatch = ($approvedCompanyName && $user->company_name !== $approvedCompanyName);
    @endphp

    @if($companyNameMismatch)
      <div style="background:#fff3cd;color:#856404;padding:18px 20px;border-radius:10px;margin-bottom:20px;border-left:4px solid #ffc107;display:flex;align-items:start;gap:12px;">
        <i class="fas fa-exclamation-triangle" style="font-size:24px;margin-top:2px;"></i>
        <div style="flex:1;">
          <strong style="display:block;margin-bottom:6px;font-size:17px;">⚠️ Business Name Mismatch</strong>
          <p style="margin:0;line-height:1.6;font-size:14px;">Your verified business permit is registered to <strong style="background:#fff;padding:2px 6px;border-radius:4px;">{{ $approvedCompanyName }}</strong>, but your current Company Name is <strong style="background:#fff;padding:2px 6px;border-radius:4px;">{{ $user->company_name }}</strong>.</p>
          <p style="margin:8px 0 0 0;line-height:1.6;font-size:14px;"><strong>Policy:</strong> Each employer account is tied to <strong>one verified business permit only</strong> for legal compliance.</p>
        </div>
      </div>
    @endif

    @if($validation->validation_status === 'pending_review')
      <div style="background:#fff3cd;color:#856404;padding:16px 20px;border-radius:10px;margin-bottom:20px;border-left:4px solid #ffc107;display:flex;align-items:start;gap:12px;">
        <i class="fas fa-hourglass-half" style="font-size:24px;margin-top:2px;"></i>
        <div style="flex:1;">
          <strong style="display:block;margin-bottom:6px;font-size:16px;">⚠️ Business Permit Under Review</strong>
          <p style="margin:0;line-height:1.5;font-size:14px;">Your business permit is currently being reviewed. You'll receive an email notification once the review is complete.</p>
          @if($validation->reason)
            <p style="margin:8px 0 0 0;font-size:13px;opacity:.9;"><strong>Reason:</strong> {{ $validation->reason }}</p>
          @endif
        </div>
      </div>
    @elseif($validation->validation_status === 'rejected')
      <div style="background:#f8d7da;color:#721c24;padding:16px 20px;border-radius:10px;margin-bottom:20px;border-left:4px solid #dc3545;display:flex;align-items:start;gap:12px;">
        <i class="fas fa-times-circle" style="font-size:24px;margin-top:2px;"></i>
        <div style="flex:1;">
          <strong style="display:block;margin-bottom:6px;font-size:16px;">❌ Business Permit Verification Failed</strong>
          <p style="margin:0;line-height:1.5;font-size:14px;">Please upload a valid Philippine business permit (DTI, SEC, or Barangay clearance).</p>
          @if($validation->reason)
            <p style="margin:8px 0 0 0;font-size:13px;opacity:.9;"><strong>Reason:</strong> {{ $validation->reason }}</p>
          @endif
          <button onclick="openReuploadPermitModal()" style="display:inline-block;margin-top:12px;background:#721c24;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;border:none;cursor:pointer;"><i class="fas fa-upload"></i> Re-Upload Business Permit</button>
        </div>
      </div>
    @elseif($validation->validation_status === 'approved')
      <div style="background:#d4edda;color:#155724;padding:16px 20px;border-radius:10px;margin-bottom:20px;border-left:4px solid #28a745;display:flex;align-items:start;gap:12px;">
        <i class="fas fa-check-circle" style="font-size:24px;margin-top:2px;"></i>
        <div style="flex:1;">
          <strong style="display:block;margin-bottom:6px;font-size:16px;">✅ Business Permit Verified!</strong>
          <p style="margin:0;line-height:1.5;font-size:14px;">You can now post job openings and manage applications.</p>
        </div>
      </div>
    @endif
  @else
    <div style="background:#e2e3e5;color:#383d41;padding:16px 20px;border-radius:10px;margin-bottom:20px;border-left:4px solid #6c757d;display:flex;align-items:start;gap:12px;">
      <i class="fas fa-exclamation-triangle" style="font-size:24px;margin-top:2px;"></i>
      <div style="flex:1;">
        <strong style="display:block;margin-bottom:6px;font-size:16px;">📄 Business Permit Required</strong>
        <p style="margin:0;line-height:1.5;font-size:14px;">Please upload your business permit to verify your company and unlock all employer features including job posting.</p>
        <a href="{{ route('settings') }}" style="display:inline-block;margin-top:12px;background:#6c757d;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;"><i class="fas fa-upload"></i> Upload Business Permit</a>
      </div>
    </div>
  @endif

  @if(session('success'))
    <div class="flash-message" style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:8px;margin-bottom:20px;border:1px solid #c3e6cb;transition:opacity .3s ease;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
  @endif

  @if($errors->has('validation'))
    <div class="flash-message" style="background:#f8d7da;color:#721c24;padding:12px 20px;border-radius:8px;margin-bottom:20px;border:1px solid #f5c6cb;"><i class="fas fa-exclamation-circle"></i> {{ $errors->first('validation') }}</div>
  @endif

  <div class="stats-cards">
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-briefcase"></i></div><div class="stat-content"><h3>{{ count($jobPostings) }}</h3><p>Active Job Postings</p></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-file-alt"></i></div><div class="stat-content"><h3>{{ $jobPostings->sum('applications_count') }}</h3><p>Total Applications</p></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-user-check"></i></div><div class="stat-content"><h3>{{ $hiredCount ?? 0 }}</h3><p>Candidates Hired</p></div></div>
  </div>

  <div class="section-header">
    <h2 class="section-title">Your Job Postings</h2>
    @php
      $canPostJobs = $validation && $validation->validation_status === 'approved';
      if ($canPostJobs) {
        $approvedCompanyName = $validation->ai_analysis['approved_company_name'] ?? null;
        if ($approvedCompanyName && $user->company_name !== $approvedCompanyName) { $canPostJobs = false; }
      }
    @endphp
    @if($canPostJobs)
      <a href="{{ route('employer.jobs.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Post New Job</a>
    @else
      <a href="#" class="btn-primary" style="background:#6c757d;cursor:not-allowed;" title="You cannot post jobs until your business permit is approved." onclick="return false;"><i class="fas fa-lock"></i> Post New Job</a>
    @endif
  </div>

  <div class="jobs-container">
    @forelse($jobPostings as $job)
      <div class="job-card">
        <div class="job-header">
          <div>
            <h3 class="job-title">{{ $job->title }}</h3>
            <p class="job-department"><i class="fas fa-briefcase"></i> {{ $job->type }}</p>
          </div>
          <span class="job-status status-{{ strtolower($job->status) }}">{{ ucfirst($job->status) }}</span>
        </div>

        <div class="job-details">
          <div class="job-detail-item"><i class="fas fa-clock"></i><span>{{ $job->type }}</span></div>
          <div class="job-detail-item"><i class="fas fa-money-bill-wave"></i><span>{{ $job->salary }}</span></div>
          <div class="job-detail-item"><i class="fas fa-calendar-alt"></i><span>Posted {{ $job->created_at->format('M d, Y') }}</span></div>
          @if($job->location)
            <div class="job-detail-item"><i class="fas fa-map-marker-alt"></i><span>{{ $job->location }}</span></div>
          @endif
        </div>

        <div class="job-footer">
          <div class="applications-count"><i class="fas fa-users"></i><span>{{ $job->applications_count }} {{ Str::plural('Application', $job->applications_count) }}</span></div>
          <div class="job-actions">
            <a href="{{ route('employer.jobs.edit', $job) }}" class="btn-icon" title="Edit Job"><i class="fas fa-edit"></i></a>
            @if($job->status === 'active')
              <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="display:inline;margin:0;">@csrf @method('PATCH')<input type="hidden" name="status" value="closed"><button type="submit" class="btn-icon" title="Close Job" style="background:#ffc107;color:#000;"><i class="fas fa-lock"></i></button></form>
            @elseif($job->status === 'closed')
              <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="display:inline;margin:0;">@csrf @method('PATCH')<input type="hidden" name="status" value="active"><button type="submit" class="btn-icon" title="Reopen Job" style="background:#28a745;"><i class="fas fa-unlock"></i></button></form>
            @endif
            <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return handleDeleteJob(event, this);" style="display:inline;margin:0;">@csrf @method('DELETE')<button type="submit" class="btn-icon" title="Delete Job" style="background:#dc3545;"><i class="fas fa-trash-alt"></i></button></form>
          </div>
        </div>
      </div>
    @empty
      <div style="grid-column:1 / -1; text-align:center; padding:40px; background:#FFF; border-radius:8px;">
        <i class="fas fa-briefcase" style="font-size:48px; color:#ccc; margin-bottom:15px;"></i>
        <p style="color:#666; font-size:16px;">No job postings yet. Click "Post New Job" to get started!</p>
      </div>
    @endforelse
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const flashMessage = document.querySelector('.flash-message');
      if (flashMessage) {
        setTimeout(() => { flashMessage.style.opacity = '0'; setTimeout(() => flashMessage.remove(), 300); }, 2000);
      }
    });
    async function handleDeleteJob(event, form) {
      event.preventDefault();
      const confirmed = await customConfirm('Are you sure you want to delete this job posting? This action cannot be undone.','Delete Job Posting','Yes, Delete');
      if (confirmed) { form.submit(); }
      return false;
    }
    function openReuploadPermitModal(){ document.getElementById('reuploadPermitModal').style.display='flex'; }
    function closeReuploadPermitModal(){ document.getElementById('reuploadPermitModal').style.display='none'; }
  </script>

  <!-- Re-Upload Business Permit Modal -->
  <div id="reuploadPermitModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:24px; width:90%; max-width:520px; box-shadow:0 10px 40px rgba(0,0,0,0.25);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <h3 style="margin:0; color:#334A5E;">Re-Upload Business Permit</h3>
        <button onclick="closeReuploadPermitModal()" style="background:transparent; border:none; font-size:24px; cursor:pointer; color:#334A5E;">&times;</button>
      </div>
      <p style="margin:0 0 12px 0; color:#666;">Upload your corrected business permit for re-verification. Accepted formats: PDF, JPG, PNG. Max 5MB.</p>
      <form method="POST" action="{{ route('employer.permit.reupload') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="business_permit" accept=".pdf,.jpg,.jpeg,.png" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;">
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:16px;">
          <button type="button" onclick="closeReuploadPermitModal()" class="btn" style="background:#6c757d; color:white;">Cancel</button>
          <button type="submit" class="btn" style="background:#648EB5; color:white;">Submit</button>
        </div>
      </form>
    </div>
  </div>

</div>

@include('partials.logout-confirm')

</body>
</html>