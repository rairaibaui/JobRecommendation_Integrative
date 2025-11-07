{{-- 
  Job Application Modal
  Include this on job seeker pages that need the apply functionality
  Usage: @include('jobseeker.partials.apply-modal')
--}}

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
    skills: card.dataset.skills ? JSON.parse(card.dataset.skills) : [],
    company: card.dataset.company || '',
    employer_name: card.dataset.employerName || '',
    employer_email: card.dataset.employerEmail || '',
    employer_phone: card.dataset.employerPhone || '',
    posted_date: card.dataset.postedDate || ''
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

      // Company Information Card
      if (currentJobData.company || currentJobData.employer_name || currentJobData.employer_email || currentJobData.employer_phone) {
        html.push('<div class="job-info-card" style="border-left-color:#4CAF50;">');
        html.push('<h4 style="margin:0 0 10px 0; color:#4CAF50; font-weight:600;"><i class="fas fa-building"></i> Company Information</h4>');
        if (currentJobData.company) {
          html.push(`<div class="info-row"><span class="info-label">Company:</span><span class="info-value">${currentJobData.company}</span></div>`);
        }
        if (currentJobData.employer_name) {
          html.push(`<div class="info-row"><span class="info-label">Contact Person:</span><span class="info-value">${currentJobData.employer_name}</span></div>`);
        }
        if (currentJobData.employer_email) {
          html.push(`<div class="info-row"><span class="info-label">Email:</span><span class="info-value"><a href="mailto:${currentJobData.employer_email}" style="color:#648EB5; text-decoration:none; font-weight:500;">${currentJobData.employer_email}</a></span></div>`);
        }
        if (currentJobData.employer_phone) {
          html.push(`<div class="info-row"><span class="info-label">Phone:</span><span class="info-value"><a href="tel:${currentJobData.employer_phone}" style="color:#648EB5; text-decoration:none; font-weight:500;">${currentJobData.employer_phone}</a></span></div>`);
        }
        if (currentJobData.posted_date) {
          html.push(`<div class="info-row"><span class="info-label">Posted:</span><span class="info-value">${currentJobData.posted_date}</span></div>`);
        }
        html.push('</div>');
      }

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

      // Resume File Section - SHOW UPLOADED RESUME
      if (profile.resume_file) {
        html.push('<div class="profile-section" style="border:2px solid #648EB5; background:#f0f7ff;">');
        html.push('<h4 style="color:#648EB5;"><i class="fas fa-file-pdf"></i> Resume/CV</h4>');
        
        // Resume verification status badge
        const verificationStatus = profile.resume_verification_status || 'pending';
        let statusBadge = '';
        switch(verificationStatus) {
          case 'verified':
            statusBadge = '<span style="background:#28a745; color:white; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; margin-left:8px;"><i class="fas fa-check-circle"></i> Verified</span>';
            break;
          case 'needs_review':
            statusBadge = '<span style="background:#ff9800; color:white; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; margin-left:8px;"><i class="fas fa-exclamation-triangle"></i> Under Review</span>';
            break;
          case 'rejected':
            statusBadge = '<span style="background:#dc3545; color:white; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; margin-left:8px;"><i class="fas fa-times-circle"></i> Rejected</span>';
            break;
          default:
            statusBadge = '<span style="background:#17a2b8; color:white; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:600; margin-left:8px;"><i class="fas fa-clock"></i> Pending</span>';
        }
        
        html.push(`<div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">`);
        html.push(`<div style="display:flex; align-items:center; gap:8px;">`);
        html.push(`<i class="fas fa-file-pdf" style="font-size:24px; color:#dc3545;"></i>`);
        html.push(`<div>`);
        html.push(`<div style="font-weight:600; color:#333;">Uploaded Resume ${statusBadge}</div>`);
        html.push(`<div style="font-size:12px; color:#666; margin-top:2px;">This resume will be submitted with your application</div>`);
        html.push(`</div>`);
        html.push(`</div>`);
        html.push(`<a href="{{ asset('storage/') }}/${profile.resume_file}" target="_blank" style="background:linear-gradient(135deg, #648EB5, #4E8EA2); color:white; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px; box-shadow:0 2px 8px rgba(100,142,181,0.3); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(100,142,181,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(100,142,181,0.3)';">`);
        html.push(`<i class="fas fa-eye"></i> View Resume`);
        html.push(`</a>`);
        html.push(`</div>`);
        html.push('</div>');
      } else {
        // No resume uploaded warning
        html.push('<div class="profile-section" style="border:2px solid #ffc107; background:#fff8e1;">');
        html.push('<h4 style="color:#ff9800;"><i class="fas fa-exclamation-triangle"></i> No Resume Uploaded</h4>');
        html.push('<p style="color:#856404; margin:0; font-size:14px;">You haven\'t uploaded a resume yet. We recommend uploading your resume in Settings before applying to jobs.</p>');
        html.push('</div>');
      }

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
        applyBtn.style.background = 'linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%)';
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
  messageDiv.style.borderRadius = '10px';
  messageDiv.style.zIndex = '10000';
  messageDiv.style.color = 'white';
  messageDiv.style.transform = 'translateY(-20px)';
  messageDiv.style.opacity = '0';
  messageDiv.style.transition = 'all 0.3s ease';
  messageDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
  messageDiv.style.fontWeight = '600';
  messageDiv.style.fontSize = '14px';
  
  switch(type) { 
    case 'success': messageDiv.style.backgroundColor = '#28a745'; break; 
    case 'info': messageDiv.style.backgroundColor = '#648EB5'; break; 
    case 'error': messageDiv.style.backgroundColor = '#dc3545'; break; 
    default: messageDiv.style.backgroundColor = '#648EB5'; 
  }
  
  document.body.appendChild(messageDiv);
  setTimeout(() => { messageDiv.style.transform = 'translateY(0)'; messageDiv.style.opacity = '1'; }, 10);
  setTimeout(() => { messageDiv.style.transform = 'translateY(-20px)'; messageDiv.style.opacity = '0'; setTimeout(() => messageDiv.remove(), 300); }, 3000);
}
</script>
