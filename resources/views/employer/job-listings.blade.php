<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>My Job Postings - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  .card { background:#FFF; border-radius:12px; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.08); border:1px solid rgba(100,142,181,0.1); }
  .btn-primary { background:linear-gradient(135deg,#648EB5 0%, #4E8EA2 100%); color:#fff; border:none; padding:12px 24px; border-radius:10px; font-size:15px; font-weight:600; cursor:pointer; transition:all .3s cubic-bezier(.4,0,.2,1); text-decoration:none; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 8px rgba(100,142,181,.25); }
  .btn-primary:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(100,142,181,.35); }
  .job-card { border:1px solid rgba(100,142,181,.15); border-radius:12px; padding:24px; margin-bottom:20px; transition:all .3s cubic-bezier(.4,0,.2,1); background:#FFF; position:relative; overflow:hidden; }
  .job-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background:linear-gradient(180deg,#648EB5 0%, #4E8EA2 100%); transform:scaleY(0); transform-origin:top; transition:transform .3s ease; }
  .job-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(100,142,181,.18); border-color:rgba(100,142,181,.3); }
  .job-card:hover::before { transform:scaleY(1); }
  .badge { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; white-space:nowrap; }
  .badge-active { background:linear-gradient(135deg,#d4edda 0%,#c3e6cb 100%); color:#0f5132; border:1px solid #c3e6cb; }
  .badge-draft { background:linear-gradient(135deg,#fff3cd 0%,#ffeaa7 100%); color:#856404; border:1px solid #ffeaa7; }
  .badge-closed { background:linear-gradient(135deg,#f8d7da 0%,#f5c6cb 100%); color:#842029; border:1px solid #f5c6cb; }
  .job-info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; margin-bottom:16px; }
  .job-info-item { display:flex; align-items:center; gap:10px; font-size:14px; color:#4B5563; padding:8px; background:#F9FAFB; border-radius:8px; border:1px solid #E5E7EB; }
  .job-info-item i { width:18px; color:#648EB5; font-size:14px; text-align:center; }
  .job-description { color:#6B7280; font-size:14px; line-height:1.6; margin-bottom:16px; padding:12px; background:#F9FAFB; border-radius:8px; border-left:3px solid #648EB5; }
  .skills-container { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
  .skill-tag { background:linear-gradient(135deg,#648EB5 0%, #4E8EA2 100%); color:#fff; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:500; display:inline-flex; align-items:center; gap:6px; }
  .job-actions-grid { display:flex; gap:10px; flex-wrap:wrap; }
  .btn-delete { background:linear-gradient(135deg,#dc3545 0%, #c82333 100%); color:#fff; border:none; padding:8px 16px; border-radius:8px; font-size:13px; cursor:pointer; transition:all .3s ease; display:inline-flex; align-items:center; gap:6px; font-weight:500; }
  .btn-edit { background:linear-gradient(135deg,#648EB5 0%, #4E8EA2 100%); color:#fff; border:none; padding:8px 16px; border-radius:8px; font-size:13px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:all .3s ease; font-weight:500; }
  .btn-close { background:linear-gradient(135deg,#ffc107 0%, #e0a800 100%); color:#000; border:none; padding:8px 16px; border-radius:8px; font-size:13px; cursor:pointer; transition:all .3s ease; display:inline-flex; align-items:center; gap:6px; font-weight:500; }
  .btn-reopen { background:linear-gradient(135deg,#28a745 0%, #218838 100%); color:#fff; border:none; padding:8px 16px; border-radius:8px; font-size:13px; cursor:pointer; transition:all .3s ease; display:inline-flex; align-items:center; gap:6px; font-weight:500; }
  @media (max-width:768px){ .card{padding:20px;} .job-card{padding:18px;} .job-info-grid{grid-template-columns:1fr;} .job-actions-grid{width:100%;} .job-actions-grid>*{flex:1; min-width:100px;} }
  @media (max-width:480px){ .btn-primary{font-size:14px; padding:10px 18px;} .job-actions-grid{flex-direction:column;} .job-actions-grid>*{width:100%;} }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  @if(session('success'))
    <div class="flash-message" style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:8px; border:1px solid #c3e6cb; transition:opacity .3s ease;"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
  @endif

  <div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
      <h2 style="font-family:'Poppins', sans-serif; color:#334A5E; margin:0;">My Job Postings</h2>
      <a href="{{ route('employer.jobs.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Post New Job</a>
    </div>

    @if($jobPostings->count() > 0)
      @foreach($jobPostings as $job)
        <div class="job-card">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px;">
            <div style="flex:1;"><h3 style="font-family:'Poppins', sans-serif; color:#334A5E; font-size:20px; margin:0 0 8px 0; font-weight:600;">{{ $job->title }}</h3></div>
            <span class="badge badge-{{ strtolower($job->status) }}">{{ ucfirst($job->status) }}</span>
          </div>

          <div class="job-info-grid">
            <div class="job-info-item"><i class="fas fa-map-marker-alt"></i><span>{{ $job->location }}</span></div>
            <div class="job-info-item"><i class="fas fa-briefcase"></i><span>{{ $job->type }}</span></div>
            <div class="job-info-item"><i class="fas fa-money-bill-wave"></i><span>{{ $job->salary }}</span></div>
            <div class="job-info-item"><i class="fas fa-calendar-alt"></i><span>Posted {{ $job->created_at->format('M d, Y') }}</span></div>
            <div class="job-info-item"><i class="fas fa-users"></i><span>{{ $job->applications->count() }} {{ Str::plural('Application', $job->applications->count()) }}</span></div>
          </div>

          @if($job->description)
            <div class="job-description">{{ \Illuminate\Support\Str::limit($job->description, 250) }}</div>
          @endif

          @if($job->skills && count($job->skills) > 0)
            <div class="skills-container">
              @foreach($job->skills as $skill)
                <span class="skill-tag"><i class="fas fa-check-circle"></i> {{ $skill }}</span>
              @endforeach
            </div>
          @endif

          <div style="border-top:2px solid #F3F4F6; padding-top:16px; margin-top:16px;">
            <div class="job-actions-grid">
              <a href="{{ route('employer.jobs.edit', $job) }}" class="btn-edit"><i class="fas fa-edit"></i> Edit Job</a>
              @if($job->status === 'active')
                <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="margin:0;">@csrf @method('PATCH')<input type="hidden" name="status" value="closed"><button type="submit" class="btn-close" title="Close this job (position filled or no longer hiring)"><i class="fas fa-lock"></i> Close Job</button></form>
              @elseif($job->status === 'closed')
                <form method="POST" action="{{ route('employer.jobs.updateStatus', $job) }}" style="margin:0;">@csrf @method('PATCH')<input type="hidden" name="status" value="active"><button type="submit" class="btn-reopen" title="Reopen this job posting"><i class="fas fa-unlock"></i> Reopen Job</button></form>
              @endif
              <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return handleDeleteJob(event, this);" style="margin:0;">@csrf @method('DELETE')<button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i> Delete</button></form>
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div style="text-align:center; padding:40px; color:#666;">
        <i class="fas fa-briefcase" style="font-size:48px; opacity:0.3; margin-bottom:16px;"></i>
        <p style="font-size:16px;">You haven't posted any jobs yet.</p>
        <a href="{{ route('employer.jobs.create') }}" class="btn-primary" style="margin-top:16px;"><i class="fas fa-plus"></i> Post Your First Job</a>
      </div>
    @endif
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const flashMessage = document.querySelector('.flash-message');
      if (flashMessage) { setTimeout(()=>{ flashMessage.style.opacity='0'; setTimeout(()=>flashMessage.remove(), 300); }, 2000); }
    });
    async function handleDeleteJob(event, form){
      event.preventDefault();
      const confirmed = await customConfirm('Are you sure you want to delete this job posting? This action cannot be undone.','Delete Job Posting','Yes, Delete');
      if (confirmed) { form.submit(); }
      return false;
    }
  </script>

  @include('partials.custom-modals')

</div>

@include('partials.logout-confirm')

</body>
</html>