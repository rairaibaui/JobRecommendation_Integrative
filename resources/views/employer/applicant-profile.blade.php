<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Applicant Profile - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  
  .page-header { margin-bottom: 0; }
  .page-title { font-family: 'Poppins', sans-serif; font-size: 32px; font-weight: 600; color: #FFF; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px; }
  
  .card { background:#FFF; border-radius:8px; padding:24px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .profile-header { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; padding-bottom:20px; border-bottom:2px solid #e5e7eb; margin-bottom:20px; }
  .profile-header .left { display:flex; gap:16px; flex:1; }
  .profile-header .right { display:flex; flex-direction:column; gap:8px; align-items:flex-end; }
  .pill { display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:600; margin-right:6px; }
  .pill-primary { background:#648EB5; color:#FFF; transition:all 0.3s; }
  .pill-primary:hover { background:#567a9c; transform:translateY(-1px); box-shadow:0 4px 8px rgba(100,142,181,0.3); }
  .section { margin-bottom:24px; }
  .sec-title { font-size:18px; font-weight:600; color:#334A5E; margin-bottom:12px; display:flex; align-items:center; gap:8px; }
  
  @media (max-width: 768px) {
    body { padding: 88px 12px 20px 12px; }
    .main { margin-left: 0; }
    .profile-header { flex-direction: column; }
    .profile-header .right { align-items: flex-start; }
  }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <div class="page-header">
    <h1 class="page-title"><i class="fas fa-user"></i> Applicant Profile</h1>
  </div>

  <div class="card">
    @php 
      $snap = $application->resume_snapshot ?? []; 
      
      // Try to get profile picture from snapshot first, then from applicant user model
      $pic = data_get($snap, 'profile_picture') ?? $applicant->profile_picture ?? null;
      $picUrl = null;
      
      if ($pic) {
        if (str_starts_with($pic, 'http://') || str_starts_with($pic, 'https://')) {
          $picUrl = $pic;
        } elseif (str_starts_with($pic, '/storage/')) {
          $picUrl = asset($pic);
        } elseif (str_starts_with($pic, 'storage/')) {
          $picUrl = asset($pic);
        } elseif (str_starts_with($pic, 'profile_pictures/')) {
          $picUrl = asset('storage/' . $pic);
        } else {
          $picUrl = asset('storage/profile_pictures/' . $pic);
        }
      }
    @endphp
    <div class="profile-header">
      <div class="left">
      @if($picUrl)
        <img src="{{ $picUrl }}" alt="Profile" style="width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #648EB5;box-shadow:0 2px 8px rgba(0,0,0,0.1);" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div style="width:96px;height:96px;border-radius:50%;background:#e8f0f7;display:none;align-items:center;justify-content:center;border:3px solid #648EB5;"><i class="fas fa-user" style="font-size:36px;color:#648EB5;"></i></div>
      @else
        <div style="width:96px;height:96px;border-radius:50%;background:#e8f0f7;display:flex;align-items:center;justify-content:center;border:3px solid #648EB5;"><i class="fas fa-user" style="font-size:36px;color:#648EB5;"></i></div>
      @endif
      <div style="flex:1; min-width:0;">
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
      </div>
      <div class="right">
        <div class="pill" style="background:#fff3cd;color:#856404;"><i class="fas fa-calendar"></i> Applied {{ $application->created_at->format('M d, Y') }}</div>
        <div class="pill" style="background:#cfe2ff;color:#084298;"><i class="fas fa-briefcase"></i> {{ $application->job_title }}</div>
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
      @php 
        $education = data_get($snap,'education');
        if (!is_array($education)) {
          try {
            $education = json_decode($education, true) ?: [];
          } catch (Exception $e) {
            $education = [];
          }
        }
      @endphp
      @if(!empty($education))
        @foreach($education as $edu)
          <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #EEE;">
            <strong style="color:#333; display:block; margin-bottom:4px;">{{ $edu['degree'] ?? 'Degree' }}</strong>
            <div style="color:#666; font-size:14px;">
              @if(isset($edu['school'])){{ $edu['school'] }}@endif
              @if(isset($edu['year'])) • Class of {{ $edu['year'] }}@endif
            </div>
          </div>
        @endforeach
      @endif
    </div>
    @endif

    @if(data_get($snap,'experience'))
    <div class="section">
      <h3 class="sec-title"><i class="fas fa-briefcase"></i> Experience</h3>
      @php 
        $experience = data_get($snap,'experience');
        if (!is_array($experience)) {
          try {
            $experience = json_decode($experience, true) ?: [];
          } catch (Exception $e) {
            $experience = [];
          }
        }
      @endphp
      @if(!empty($experience))
        @foreach($experience as $exp)
          <div style="margin-bottom:12px; padding-bottom:12px; border-bottom:1px solid #EEE;">
            <strong style="color:#333; display:block; margin-bottom:4px;">{{ $exp['position'] ?? 'Position' }}</strong>
            <div style="color:#666; font-size:14px;">
              @if(isset($exp['company'])){{ $exp['company'] }}@endif
              @if(isset($exp['start_date']) || isset($exp['end_date']))
                <br><i class="fas fa-calendar"></i> 
                {{ $exp['start_date'] ?? '' }}@if(isset($exp['start_date']) && isset($exp['end_date'])) - @endif{{ $exp['end_date'] ?? '' }}
              @endif
            </div>
            @if(isset($exp['description']))
              <p style="color:#555; margin-top:6px; font-size:14px;">{{ $exp['description'] }}</p>
            @endif
          </div>
        @endforeach
      @endif
    </div>
    @endif

    @if(data_get($snap,'resume_file'))
    <div class="section" style="margin-top:14px;">
      <h3 class="sec-title"><i class="fas fa-file-pdf"></i> Resume</h3>
      <a href="{{ asset('storage/'.data_get($snap,'resume_file')) }}" target="_blank" class="pill pill-primary" style="text-decoration:none;">
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

@include('partials.logout-confirm')

</body>
</html>
