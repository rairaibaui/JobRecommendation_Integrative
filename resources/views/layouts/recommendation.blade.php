<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Recommendation - Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body {
    width: 100vw;
    height: 100vh;
    display: flex;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
    padding: 88px 20px 20px 20px;
    gap: 20px;
  }

  /* Sidebar */
  .sidebar {
    position: fixed;
    left: 20px;
    top: 88px;
    width: 250px;
    height: calc(100vh - 108px);
    border-radius: 8px;
    background: #FFF;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .sidebar .profile-ellipse {
    align-self: center;
  }

  .profile-name {
    align-self: center;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #000;
    margin-bottom: 20px;
  }

  .sidebar .sidebar-btn {
    align-self: flex-start;
  }

  .profile-ellipse {
    width: 62px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-icon {
    width: 40px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-icon i {
    font-size: 30px;
    color: #FFF;
  }

  .sidebar-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 39px;
    padding: 0 10px;
    border-radius: 8px;
    background: transparent;
    box-shadow: none;
    color: #000;
    font-size: 20px;
    font-weight: 400;
    cursor: pointer;
    text-decoration: none;
    border: none;
    width: 100%;
  }

  .sidebar-btn.active {
    background: #648EB5;
    box-shadow: 0 7px 4px rgba(0,0,0,0.25);
    color: #000;
    width: 100%;
  }

  .sidebar-btn-icon {
    margin-right: 10px;
  }

  /* Main content */
  .main {
    margin-left: 290px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .top-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 68px;
    background: #2B4053;
    border-radius: 0;
    display: flex;
    align-items: center;
    padding: 0 20px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 800;
    z-index: 1000;
  }

  .hamburger {
    margin-right: 20px;
    color: #FFF;
  }

  .welcome {
    font-family: 'Poppins', sans-serif;
    font-size: 32px;
    font-weight: 600;
    color: #FFF;
    margin-bottom: 10px;
  }

  /* Top Job Recommendations */
  .card-large {
    width: 100%;
    flex: 1;
    background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .recommendation-header {
    text-align: left;
    margin-bottom: 10px;
  }

  .recommendation-header h3 {
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 600;
    color: #000;
    margin: 0 0 5px 0;
  }

  .recommendation-header p {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #666;
    margin: 0;
  }

  .job-card {
    width: 100%;
    height: auto;
    border-radius: 8px;
    border: 1px solid #BBB1B1;
    background: #FFF;
    box-shadow: 0 10px 4px rgba(0,0,0,0.25);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: all 0.3s ease;
  }

  .job-preview {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }

  .job-details {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    opacity: 0;
    margin-top: 0;
    padding: 0 10px;
  }

  .job-details.expanded {
    max-height: 1000px;
    opacity: 1;
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 20px;
  }

  .job-details h4 {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    font-weight: 500;
    color: #333;
    margin-bottom: 10px;
  }

  .skills-section {
    margin-top: 20px;
  }

  /* subtle lift on hover for cards */
  .job-card {
    transition: transform 200ms ease, box-shadow 200ms ease;
  }
  .job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 30px rgba(0,0,0,0.14);
  }

  .job-title {
    font-family: 'Roboto', sans-serif;
    font-size: 20px;
    font-weight: 500;
    color: #000;
  }

  .job-location, .job-type, .job-salary {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .job-location i, .job-type i, .job-salary i {
    color: #648EB5;
    width: 16px;
  }

  .job-description {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #666;
    margin-top: 8px;
    line-height: 1.4;
  }

  .skills-header {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #333;
    margin-top: 8px;
    font-weight: 600;
  }

  .job-skills {
    display: flex;
    gap: 10px;
    margin-top: 5px;
  }

  .skill {
    background: #648EB5;
    color: #FFF;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 14px;
  }

  .job-actions {
    display: flex;
    gap: 20px;
    margin-top: auto;
  }

  .jobs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }

  .view-details, .save-job {
    padding: 10px 25px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
  }

  .view-details {
    background: #FFF;
    border: 1px solid #648EB5;
    color: #648EB5;
  }

  .save-job {
    background: #648EB5;
    color: #FFF;
  }

  /* Bookmark button shared styles (used in dashboard/recommendation/bookmarks) */
  .bookmark-btn {
    background: transparent;
    border: 1px solid rgba(100,142,181,0.15);
    color: #648EB5;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    font-size: 18px;
    padding: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 180ms ease;
  }
  .bookmark-btn i { transition: color 160ms ease, transform 160ms ease; }
  .bookmark-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.08); }
  .bookmark-btn i.fas { color: #FFD166; /* gold for bookmarked */ transform: scale(1.06); }
  .bookmark-btn i.far { color: #648EB5; }

</style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile-ellipse">
      <div class="profile-icon">
        @if(Auth::user()->profile_picture)
          <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture"
            style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
        @else
          <i class="fas fa-user"></i>
        @endif
      </div>
    </div>
  
    <!-- Dynamic User Name -->
    <div class="profile-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
  
    <a href="{{ route('dashboard') }}" class="sidebar-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="fas fa-home sidebar-btn-icon"></i>
      Dashboard
    </a>
    <a href="{{ route('recommendation') }}"
      class="sidebar-btn {{ request()->routeIs('recommendation') ? 'active' : '' }}">
      <i class="fas fa-suitcase sidebar-btn-icon"></i>
      Recommendation
    </a>
    <a href="{{ route('my-applications') }}"
      class="sidebar-btn {{ request()->routeIs('my-applications') ? 'active' : '' }}">
      <i class="fas fa-file-alt sidebar-btn-icon"></i>
      My Applications
    </a>
    <a href="{{ route('bookmarks') }}" class="sidebar-btn {{ request()->routeIs('bookmarks') ? 'active' : '' }}">
      <i class="fas fa-bookmark sidebar-btn-icon"></i>
      Bookmarks
    </a>
    <a href="{{ route('settings') }}" class="sidebar-btn {{ request()->routeIs('settings') ? 'active' : '' }}">
      <i class="fas fa-cog sidebar-btn-icon"></i>
      Settings
    </a>
  
    <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
      @csrf
      <button type="submit" class="sidebar-btn"
        style="border: none; background: #648EB5; color: #FFF; font-size: 20px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 10px; height: 39px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
        Log out
      </button>
    </form>
  </div>

  @yield('content')
  <script src="{{ asset('js/job-details.js') }}"></script>
  
  <!-- Apply Modal -->
  <div id="applyOverlay" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="apply-modal-container" style="background:white; border-radius:16px; width:90%; max-width:800px; max-height:85vh; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalSlideIn 0.3s ease;">
      
      <!-- Modal Header -->
      <div style="background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); padding:30px; color:white; position:relative;">
        <button onclick="closeApplyModal()" style="position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.2); border:none; width:36px; height:36px; border-radius:50%; font-size:20px; cursor:pointer; color:white; display:flex; align-items:center; justify-content:center; transition:all 0.2s;">&times;</button>
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
          <div style="background:rgba(255,255,255,0.2); width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-briefcase" style="font-size:24px;"></i>
          </div>
          <div>
            <h2 id="applyJobTitle" style="margin:0; font-size:24px; font-weight:600;">Apply for Position</h2>
            <p style="margin:5px 0 0 0; opacity:0.9; font-size:14px;">Submit your application with confidence</p>
          </div>
        </div>
      </div>

      <!-- Modal Body -->
      <div id="resumePreview" style="padding:30px; max-height:calc(85vh - 180px); overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:center; padding:40px;">
          <div class="loading-spinner" style="width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #648EB5; border-radius:50%; animation:spin 1s linear infinite;"></div>
          <p style="margin-left:15px; color:#666; font-style:italic;">Loading your profile...</p>
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding:20px 30px; background:#f8f9fa; border-top:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
        <div style="color:#666; font-size:14px;">
          <i class="fas fa-shield-alt" style="color:#648EB5; margin-right:5px;"></i>
          Your data is secure
        </div>
        <div style="display:flex; gap:12px;">
          <button onclick="closeApplyModal()" style="padding:10px 20px; border:1px solid #ddd; border-radius:8px; background:white; cursor:pointer; font-size:14px; font-weight:500; color:#666; transition:all 0.2s;">
            Cancel
          </button>
          <button id="confirmApplyBtn" style="padding:10px 24px; border:none; border-radius:8px; background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); color:white; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s; box-shadow:0 4px 12px rgba(100,142,181,0.3); display:flex; align-items:center; gap:8px; white-space:nowrap;">
            <i class="fas fa-paper-plane"></i>
            <span>Submit Application</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    @keyframes modalSlideIn {
      from { transform:translateY(-30px); opacity:0; }
      to { transform:translateY(0); opacity:1; }
    }
    
    @keyframes spin {
      0% { transform:rotate(0deg); }
      100% { transform:rotate(360deg); }
    }

    .apply-modal-container button:hover {
      transform:translateY(-2px);
      box-shadow:0 6px 16px rgba(0,0,0,0.15);
    }

    #resumePreview::-webkit-scrollbar {
      width:8px;
    }

    #resumePreview::-webkit-scrollbar-track {
      background:#f1f1f1;
      border-radius:4px;
    }

    #resumePreview::-webkit-scrollbar-thumb {
      background:#648EB5;
      border-radius:4px;
    }

    #resumePreview::-webkit-scrollbar-thumb:hover {
      background:#4E8EA2;
    }

    .job-info-card {
      background:#f8f9fa;
      border-radius:12px;
      padding:20px;
      margin-bottom:20px;
      border-left:4px solid #648EB5;
    }

    .profile-section {
      background:white;
      border-radius:12px;
      padding:20px;
      margin-bottom:15px;
      border:1px solid #e0e0e0;
      transition:all 0.2s;
    }

    .profile-section:hover {
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
      transform:translateY(-2px);
    }

    .profile-section h4 {
      color:#648EB5;
      font-size:16px;
      margin:0 0 12px 0;
      font-weight:600;
      display:flex;
      align-items:center;
      gap:8px;
    }

    .profile-section h4 i {
      font-size:18px;
    }

    .info-row {
      display:flex;
      align-items:flex-start;
      margin-bottom:8px;
      font-size:14px;
      color:#333;
    }

    .info-label {
      font-weight:600;
      min-width:120px;
      color:#666;
    }

    .info-value {
      flex:1;
      color:#333;
    }

    .experience-item, .education-item {
      background:#f8f9fa;
      padding:12px;
      border-radius:8px;
      margin-bottom:10px;
      border-left:3px solid #648EB5;
    }

    .experience-item strong, .education-item strong {
      color:#648EB5;
      font-size:15px;
    }

    .date-range {
      font-size:13px;
      color:#666;
      font-style:italic;
      margin-top:4px;
    }
  </style>

  <script>
  function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

  let currentJobData = null;
  let currentResumeSnapshot = null;

  function openApplyModal(button) {
    const card = button.closest('.job-card');
    currentJobData = {
      id: card.dataset.jobId || null,
      title: card.dataset.title || '',
      location: card.dataset.location || '',
      type: card.dataset.type || '',
      salary: card.dataset.salary || '',
      description: card.dataset.description || '',
      skills: card.dataset.skills ? JSON.parse(card.dataset.skills) : []
    };

    document.getElementById('applyJobTitle').textContent = currentJobData.title || 'Job Position';
    document.getElementById('resumePreview').innerHTML = '<div style="display:flex; align-items:center; justify-content:center; padding:40px;"><div class="loading-spinner" style="width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #648EB5; border-radius:50%; animation:spin 1s linear infinite;"></div><p style="margin-left:15px; color:#666; font-style:italic;">Loading your profile...</p></div>';
    document.getElementById('applyOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  
  // Fetch profile resume snapshot
  fetch("{{ route('profile.resume') }}", { headers: { 'X-CSRF-TOKEN': getCsrfToken() }})
    .then(r => r.json())
    .then(profile => {
      currentResumeSnapshot = profile;
      const html = [];
      
      // Job Info Card
      html.push('<div class="job-info-card">');
      html.push('<h4 style="margin:0 0 10px 0; color:#648EB5; font-weight:600;"><i class="fas fa-briefcase"></i> Job Details</h4>');
      html.push(`<div class="info-row"><span class="info-label">Position:</span><span class="info-value">${currentJobData.title}</span></div>`);
      html.push(`<div class="info-row"><span class="info-label">Location:</span><span class="info-value">${currentJobData.location || 'Not specified'}</span></div>`);
      html.push(`<div class="info-row"><span class="info-label">Type:</span><span class="info-value">${currentJobData.type || 'Full-time'}</span></div>`);
      html.push(`<div class="info-row"><span class="info-label">Salary:</span><span class="info-value">${currentJobData.salary || 'Negotiable'}</span></div>`);
      html.push('</div>');

      // Personal Info Section
      html.push('<div class="profile-section">');
      html.push('<h4><i class="fas fa-user"></i> Personal Information</h4>');
      html.push(`<div class="info-row"><span class="info-label">Name:</span><span class="info-value">${profile.first_name} ${profile.last_name}</span></div>`);
      html.push(`<div class="info-row"><span class="info-label">Email:</span><span class="info-value">${profile.email}</span></div>`);
      if (profile.phone_number) html.push(`<div class="info-row"><span class="info-label">Phone:</span><span class="info-value">${profile.phone_number}</span></div>`);
      if (profile.birthday) {
        const birthday = new Date(profile.birthday).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        html.push(`<div class="info-row"><span class="info-label">Birthday:</span><span class="info-value">${birthday}</span></div>`);
      }
      if (profile.location) html.push(`<div class="info-row"><span class="info-label">Location:</span><span class="info-value">${profile.location}</span></div>`);
      
      // Employment Status
      if (profile.employment_status === 'employed') {
        html.push('<div style="background:#fff3cd; border-left:3px solid #ffc107; padding:10px; margin-top:10px; border-radius:6px;">');
        html.push('<strong style="color:#856404;"><i class="fas fa-exclamation-triangle"></i> Employment Status:</strong> ');
        html.push('<span style="color:#856404;">Currently Employed</span>');
        if (profile.hired_by_company) {
          html.push(`<br><small style="color:#856404;">Company: ${profile.hired_by_company}</small>`);
        }
        html.push('</div>');
      } else {
        html.push('<div class="info-row"><span class="info-label">Status:</span><span class="info-value" style="color:#28a745;"><i class="fas fa-search"></i> Seeking Employment</span></div>');
      }
      html.push('</div>');

      // Professional Summary
      if (profile.summary) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-file-alt"></i> Professional Summary</h4>');
        html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.summary}</p>`);
        html.push('</div>');
      }

      // Education
      if (profile.education && profile.education.length) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-graduation-cap"></i> Education</h4>');
        profile.education.forEach(e => {
          html.push('<div class="education-item">');
          html.push(`<strong>${e.degree || 'Degree'}</strong>`);
          html.push(`<div style="color:#666; margin-top:4px;">${e.school || ''} ${e.year ? '• Class of ' + e.year : ''}</div>`);
          html.push('</div>');
        });
        html.push('</div>');
      }

      // Work Experience
      if (profile.experiences && profile.experiences.length) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-briefcase"></i> Work Experience</h4>');
        profile.experiences.forEach(ex => {
          html.push('<div class="experience-item">');
          html.push(`<strong>${ex.position || 'Position'}</strong>`);
          html.push(`<div style="color:#666; margin-top:2px;">${ex.company || 'Company'}</div>`);
          if (ex.start_date || ex.end_date) {
            html.push(`<div class="date-range">${ex.start_date || 'Start'} - ${ex.end_date || 'Present'}</div>`);
          }
          if (ex.responsibilities) {
            html.push(`<div style="margin-top:8px; color:#555; font-size:13px; line-height:1.5;">${ex.responsibilities}</div>`);
          }
          html.push('</div>');
        });
        html.push('</div>');
      }

      // Skills
      if (profile.skills) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-code"></i> Skills</h4>');
        html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.skills}</p>`);
        html.push('</div>');
      }

      // Languages
      if (profile.languages) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-language"></i> Languages</h4>');
        html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.languages}</p>`);
        html.push('</div>');
      }

      // Portfolio
      if (profile.portfolio_links) {
        html.push('<div class="profile-section">');
        html.push('<h4><i class="fas fa-link"></i> Portfolio & Links</h4>');
        html.push(`<p style="color:#648EB5; line-height:1.6; margin:0; word-break:break-all;">${profile.portfolio_links}</p>`);
        html.push('</div>');
      }

      document.getElementById('resumePreview').innerHTML = html.join('\n');
      
      // Disable apply button if user is employed
      const applyBtn = document.getElementById('confirmApplyBtn');
      if (profile.employment_status === 'employed') {
        applyBtn.disabled = true;
        applyBtn.style.background = '#6c757d';
        applyBtn.style.cursor = 'not-allowed';
        applyBtn.innerHTML = '<i class="fas fa-ban"></i><span>Cannot Apply - Currently Employed</span>';
      } else {
        applyBtn.disabled = false;
        applyBtn.style.background = '#648EB5';
        applyBtn.style.cursor = 'pointer';
        applyBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Submit Application</span>';
      }
    })
    .catch(() => { 
      document.getElementById('resumePreview').innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-exclamation-circle" style="font-size:48px; color:#f44336; margin-bottom:15px;"></i><p style="color:#c00; font-size:16px; margin:0;">Failed to load profile.</p><p style="color:#666; font-size:14px; margin-top:8px;">Please update your profile in Settings before applying.</p></div>'; 
    });
  }

  function closeApplyModal() {
    document.getElementById('applyOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentJobData = null;
    currentResumeSnapshot = null;
  }

  document.getElementById('confirmApplyBtn')?.addEventListener('click', function(){
    if (!currentJobData) {
      showMessage('No job selected', 'error');
      return;
    }
    
    const submitBtn = this;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Submitting...</span>';
    
    const payload = {
      job_title: currentJobData.title,
      job_posting_id: currentJobData.id || null,
      job_data: currentJobData,
      resume_snapshot: currentResumeSnapshot || {}
    };

    fetch("{{ route('job.apply') }}", {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'X-CSRF-TOKEN': getCsrfToken() 
      },
      body: JSON.stringify(payload)
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      
      if (ok && data.success) {
        closeApplyModal();
        showMessage(data.message || 'Application submitted successfully!', 'success');
      } else {
        showMessage(data.message || 'Failed to submit application', 'error');
      }
    })
    .catch(error => {
      console.error('Error submitting application:', error);
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      showMessage('Network error. Please try again.', 'error');
    });
  });

  function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = message;
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.padding = '12px 24px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.zIndex = '10000';
    messageDiv.style.color = 'white';
    messageDiv.style.transform = 'translateY(-20px)';
    messageDiv.style.opacity = '0';
    messageDiv.style.transition = 'all 0.3s ease';
    messageDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    switch(type) { 
      case 'success': messageDiv.style.backgroundColor = '#4CAF50'; break; 
      case 'info': messageDiv.style.backgroundColor = '#2196F3'; break; 
      case 'error': messageDiv.style.backgroundColor = '#f44336'; break; 
      default: messageDiv.style.backgroundColor = '#2196F3'; 
    }
    document.body.appendChild(messageDiv);
    setTimeout(() => { messageDiv.style.transform = 'translateY(0)'; messageDiv.style.opacity = '1'; }, 10);
    setTimeout(() => { messageDiv.style.transform = 'translateY(-20px)'; messageDiv.style.opacity = '0'; setTimeout(() => messageDiv.remove(), 300); }, 2700);
  }
  </script>
  @stack('scripts')
</body>
</html>

