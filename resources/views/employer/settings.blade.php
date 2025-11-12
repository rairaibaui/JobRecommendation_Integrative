<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Employer Settings - Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  
  .page-header { margin-bottom: 0; }
  .page-title { font-family: 'Poppins', sans-serif; font-size: 32px; font-weight: 600; color: #FFF; margin: 0 0 8px 0; display: flex; align-items: center; gap: 14px; letter-spacing: 0.5px; }
  .page-title i { color: #FFF; font-size: 28px; margin-right: 2px; }
  .page-subtitle { color: rgba(255,255,255,0.85); font-family: 'Roboto', sans-serif; font-size: 16px; margin-bottom: 18px; margin-top: -2px; }
  .page-subtitle { margin: 0; color: rgba(255, 255, 255, 0.85); font-size: 14px; }
  
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .card-header { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb; }
  .card-title { font-size: 20px; font-weight: 600; color: #2B4053; margin: 0; }
  .card-body { }
  
  .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
  .alert i { font-size: 18px; }
  .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  
  .flash-message { transition: opacity 0.3s; }
  
  .form-group { margin-bottom: 20px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .form-label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px; }
  .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.2s; }
  .form-control:focus { outline: none; border-color: #648EB5; box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1); }
  .form-control:disabled, .form-control:read-only { background: #f8f9fa; cursor: not-allowed; }
  .form-help { display: block; margin-top: 6px; font-size: 12px; color: #6c757d; }
  
  .text-danger { color: #dc3545; }
  
  .d-flex { display: flex; }
  .gap-2 { gap: 8px; }
  .justify-content-end { justify-content: flex-end; }
  .align-items-center { align-items: center; }
  
  .btn { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
  .btn-primary { background: #648EB5; color: white; box-shadow: 0 2px 4px rgba(100, 142, 181, 0.3); }
  .btn-primary:hover { background: #567a9c; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(100, 142, 181, 0.4); }
  .btn-secondary { background: #6c757d; color: white; box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3); }
  .btn-secondary:hover { background: #5a6268; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4); }
  .btn-danger { background: #dc3545; color: white; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3); }
  .btn-danger:hover { background: #c82333; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4); }
  .btn-sm { padding: 6px 12px; font-size: 12px; }
  
  .notice { padding: 12px 16px; border-radius: 6px; margin-top: 12px; display: flex; gap: 10px; }
  .notice-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
  .notice i { flex-shrink: 0; margin-top: 2px; }
  
  .mt-4 { margin-top: 24px; }
  
  @media (max-width: 768px) {
    body { padding: 88px 12px 20px 12px; }
    .main { margin-left: 0; }
    .form-row { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <div class="page-header">
    <h1 class="page-title">
      <i class="fas fa-cog"></i>
      Company Settings
    </h1>
    <div class="page-subtitle">Manage your company profile and business permit</div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Company Profile</h2>
    </div>
    
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success flash-message">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
      @endif
      
      <form method="POST" action="{{ route('profile.updateEmployer') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
          <label class="form-label">
            Company Name <span class="text-danger">*</span>
          </label>
          <input type="text" 
                 name="company_name" 
                 class="form-control" 
                 required 
                 value="{{ old('company_name', Auth::user()->company_name) }}">
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Contact First Name</label>
            <input type="text" 
                   name="first_name" 
                   class="form-control" 
                   value="{{ old('first_name', Auth::user()->first_name) }}">
          </div>
          
          <div class="form-group">
            <label class="form-label">Contact Last Name</label>
            <input type="text" 
                   name="last_name" 
                   class="form-control" 
                   value="{{ old('last_name', Auth::user()->last_name) }}">
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Job Title</label>
          <input type="text" 
                 name="job_title" 
                 class="form-control" 
                 value="{{ old('job_title', Auth::user()->job_title) }}" 
                 placeholder="e.g., HR Manager">
        </div>
        
        <div class="form-row">
          <div class="form-group" style="flex:1; min-width:260px;">
            <label class="form-label">Email (login)</label>
            <div class="d-flex" style="align-items:flex-start; gap:8px;">
              <input type="email" 
                     id="email_display_input"
                     class="form-control" 
                     value="{{ Auth::user()->email }}" 
                     readonly 
                     style="background: #f8f9fa; cursor: not-allowed; flex:1;">

              @if(Auth::user()->hasVerifiedEmail())
                <div style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; background:#648EB5; border:1px solid #567a9c; color:#ffffff; border-radius:6px; font-weight:600;">
                  <i class="fas fa-check-circle" style="color:#ffffff; font-size:14px;"></i>
                  Verified
                </div>
              @else
                <button type="button" 
                        id="sendEmailVerifyBtn"
                        onclick="confirmAndSendVerification()" 
                        class="btn btn-primary btn-sm"
                        style="white-space:nowrap;">
                  <i class="fas fa-envelope"></i> Verify
                </button>
              @endif
            </div>
            @if(Auth::user()->hasVerifiedEmail())
              <small id="emailVerifyHelp" class="form-help" style="display:block; margin-top:6px; color:#155724;">
                <i class="fas fa-info-circle"></i> This email was verified on {{ optional(Auth::user()->email_verified_at)->format('M j, Y g:ia') }}.
              </small>
            @else
              <small id="emailVerifyHelp" class="form-help" style="display:block; margin-top:6px;">
                <i class="fas fa-info-circle"></i> Click "Verify" to send a verification email so you can confirm your address.
              </small>
            @endif
            <div id="emailVerifyMessage" style="margin-top:8px; display:none;" class="alert"></div>

            <!-- Hidden fallback form in case JS fetch cannot be used or server redirects non-AJAX -->
            <!-- (Moved outside main <form> to avoid nested-form issues that can break file uploads) -->

            <!-- Custom confirm modal for sending verification email -->
            <div id="emailConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:12000; align-items:center; justify-content:center;">
              <div style="background:#fff; max-width:520px; width:92%; padding:18px; border-radius:8px; box-shadow:0 10px 40px rgba(2,6,23,0.2);">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                  <h4 style="margin:0; font-size:16px;">Send verification email?</h4>
                  <button type="button" id="emailConfirmClose" style="background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
                </div>
                <p id="emailConfirmMessage" style="color:#444; margin-top:12px;">We will send a verification link to <strong>{{ Auth::user()->email }}</strong>. This link will expire in a few minutes.</p>
                <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:14px;">
                  <button type="button" id="emailConfirmCancel" class="btn btn-secondary" style="background:#edf2f7;border:1px solid #cbd5e1;padding:8px 12px;">Cancel</button>
                  <button type="button" id="emailConfirmOk" class="btn btn-primary" style="padding:8px 12px;">Send Email</button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              Contact Number <span class="text-danger">*</span>
            </label>
            <div class="d-flex gap-2" style="align-items:flex-start;">
              <input type="text" 
                     id="phone_number_input" 
                     name="phone_number" 
                     class="form-control" 
                     required 
                     value="{{ old('phone_number', Auth::user()->phone_number) }}" 
                     placeholder="e.g., 0917 123 4567" 
                     style="flex:1;">
              <button type="button" 
                      onclick="openPhoneVerificationModal()" 
                      class="btn-secondary"
                      style="white-space:nowrap;">
                <i class="fas fa-shield-alt"></i> Verify
              </button>
            </div>
            <small class="form-help">
              <i class="fas fa-info-circle"></i> Click "Verify" to change your phone number securely
            </small>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">
            Company Address <span class="text-danger">*</span>
          </label>
          <input type="text" 
                 name="address" 
                 class="form-control" 
                 required 
                 value="{{ old('address', Auth::user()->address) }}" 
                 placeholder="Building, Street, Barangay, Mandaluyong City">
          <small class="form-help">
            <i class="fas fa-info-circle"></i> Required for verification - Full address will be shown to job seekers
          </small>
        </div>
        
        <div class="form-group">
          <label class="form-label">Company Description</label>
          <textarea name="company_description" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Describe your company, what you do, company culture, etc...">{{ old('company_description', Auth::user()->company_description) }}</textarea>
          <small class="form-help">
            <i class="fas fa-info-circle"></i> This will be displayed on your job postings
          </small>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Company Logo / Photo</label>
            <input type="file" 
                   name="profile_picture" 
                   class="form-control" 
                   accept="image/*"
                   style="padding: 8px;">
            @if(Auth::user()->profile_picture)
              <div style="margin-top:12px; display:flex; align-items:center; gap:12px;">
                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                     width="88" 
                     height="88" 
                     style="border-radius:50%; object-fit:cover; border:3px solid #648EB5;">
                <label style="font-weight:500; display:flex; align-items:center; gap:8px; cursor:pointer;">
                  <input type="checkbox" name="remove_picture" value="1">
                  Remove current picture
                </label>
              </div>
            @endif
          </div>
          
          <div class="form-group">
            <label class="form-label">
              Business Permit (PDF only)
              @if(Auth::user()->business_permit_path)
                <span style="color: #28a745; font-size: 14px; margin-left: 8px;">
                  <i class="fas fa-check-circle"></i> File uploaded
                </span>
              @endif
            </label>
            
            {{-- Personal Email Verification Notice Component --}}
            <x-personal-email-notice :compact="true" />
            
      @php
        $hasFile = (bool) Auth::user()->business_permit_path;
        $latestValidation = null;
        try {
          $latestValidation = \App\Models\DocumentValidation::where('user_id', Auth::id())
            ->where('document_type', 'business_permit')
            ->orderByDesc('created_at')
            ->first();
        } catch (\Throwable $e) {
          $latestValidation = null;
        }

  // Red when no file or last validation was rejected/blocked (needs a new valid upload)
  $needsUpload = ! $hasFile || ($latestValidation && in_array($latestValidation->validation_status, ['rejected','blocked']));
      @endphp

      @if($needsUpload)
    <div style="background: #f8d7da; border: 1px solid #dc3545; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-exclamation-circle" style="color: #dc3545; font-size: 20px;"></i>
          <div style="flex: 1;">
            <div style="font-weight: 700; color: #721c24; font-size: 14px;">Business permit required</div>
            <div style="font-size: 12px; color: #721c24;">Please upload a valid Barangay Clearance or Mayor's Permit (PDF only). The upload box is highlighted to indicate action is required.</div>
          </div>
        </div>
      @elseif($hasFile)
        <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-check-circle" style="color: #28a745; font-size: 20px;"></i>
          <div style="flex: 1;">
            <div style="font-weight: 600; color: #155724; font-size: 14px;">Business permit already uploaded</div>
            <div style="font-size: 12px; color: #155724;">You can upload a new file to replace the current one</div>
          </div>
        </div>
      @endif

      @if(session('permit_removed'))
        <div style="background: #fff3cd; border: 1px solid #ffeeba; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; display:flex; align-items:center; gap:10px;">
          <i class="fas fa-info-circle" style="color:#856404; font-size:18px;"></i>
          <div style="color:#856404; font-weight:600;">Business permit removed</div>
          <div style="color:#856404; margin-left:6px;">Your previously uploaded business permit has been deleted and your account no longer has a registered permit.</div>
        </div>
            @endif
            
            <div style="position: relative;">
                @if(Auth::user()->business_permit_path)
                    <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #28a745; font-size: 18px; pointer-events: none; z-index: 1;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                @endif
      <input type="file" 
        name="business_permit" 
        class="form-control" 
        accept=".pdf"
        style="padding: 8px; @if($needsUpload) border-color: #dc3545; background-color: #fff5f5; @elseif(Auth::user()->business_permit_path) border-color: #28a745; background-color: #f0fff4; @endif">
            </div>
            @if(Auth::user()->business_permit_path)
              <div style="margin-top:8px;">
                <a href="{{ asset('storage/' . Auth::user()->business_permit_path) }}" 
                   target="_blank"
                   class="btn-sm btn-secondary"
                   style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fas fa-file-pdf"></i> View Current File
                </a>
                <label style="font-weight:500; display:inline-flex; align-items:center; gap:8px; cursor:pointer; margin-left:10px;">
                  <input type="checkbox" name="remove_business_permit" value="1" id="remove_business_permit_checkbox">
                  Remove current permit
                </label>
              </div>
            @endif
            
            {{-- One-Permit-Per-Account Policy Notice --}}
            <div class="notice notice-info" style="margin-top: 12px;">
              <i class="fas fa-info-circle"></i>
              <div>
                <strong>Policy:</strong> Each account is tied to <strong>one verified business permit only</strong>. 
                The business name on your permit must match your registered company name. 
                To operate multiple businesses, register <strong>separate employer accounts</strong> with separate permits.
              </div>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-end gap-2 mt-4" style="flex-wrap: wrap; gap: 12px;">
          <button type="button" onclick="openChangePasswordModal()" class="btn btn-secondary">
            <i class="fas fa-lock"></i> Change Password
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
          </button>
        </div>
      </form>

      <!-- Hidden fallback form (moved outside main form to avoid nested forms) -->
      <form id="emailVerifyFallbackForm" method="POST" action="{{ route('verification.resend') }}" style="display:none;">
        @csrf
      </form>

      <script>
        // Confirm removal of business permit when checkbox is checked and form is submitted
        document.addEventListener('DOMContentLoaded', function() {
          const form = document.querySelector('form[action="{{ route('profile.updateEmployer') }}"]');
          const removeCheckbox = document.getElementById('remove_business_permit_checkbox');

          if (!form || !removeCheckbox) return;

          form.addEventListener('submit', async function(e) {
            const businessInput = document.querySelector('input[name="business_permit"]');
            const hasNewFile = businessInput && businessInput.files && businessInput.files.length > 0;
            if (removeCheckbox.checked && !hasNewFile) {
              // ask for confirmation
              const confirmed = await window.systemConfirm('Remove Business Permit', 'Are you sure you want to permanently remove your current business permit file? This action cannot be undone.');
              if (!confirmed) {
                e.preventDefault();
                // uncheck to avoid accidental submits
                removeCheckbox.checked = false;
              }
            }
          });
        });
      </script>
    </div>
  </div>

  <!-- Danger Zone: Delete Account -->
  <div class="card" style="margin-top: 24px; border: 2px solid #dc3545;">
    <div class="card-header" style="background: #fff5f5; border-bottom: 2px solid #dc3545;">
      <h2 class="card-title" style="color: #dc3545; margin: 0;">
        <i class="fas fa-exclamation-triangle"></i>
        Danger Zone
      </h2>
    </div>
    
    <div class="card-body">
      <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
          <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #212529;">
            Delete Your Account
          </h3>
          <p style="margin: 0; color: #6c757d; font-size: 14px; line-height: 1.5;">
            Once you delete your account, there is no going back. This will permanently delete:
          </p>
          <ul style="margin: 8px 0 0 20px; color: #6c757d; font-size: 14px; line-height: 1.6;">
            <li>Your company profile and all business information</li>
            <li>All job postings you've created</li>
            <li>All applications received for your jobs</li>
            <li>Your business permit and verification status</li>
          </ul>
        </div>
        <button type="button" 
                onclick="openDeleteAccountModal()" 
                class="btn btn-danger">
          <i class="fas fa-trash-alt"></i> Delete Account
        </button>
      </div>
    </div>
  </div>

  <!-- Delete Account Confirmation Modal -->
  <div id="deleteAccountModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
    <div class="card" style="max-width:500px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3); margin: 0; border-radius: 16px;">
      <div class="card-header" style="background: #fff; border-bottom: none; position: relative;">
      <h3 class="card-title" style="color: #dc3545; margin: 0; font-weight: 600; display: flex; align-items: center; gap: 12px; padding-right: 50px;">
          <i class="fas fa-exclamation-triangle" style="color: #dc3545; font-size: 24px;"></i>
          Confirm Account Deletion
      </h3>  
      <button onclick="closeDeleteAccountModal()" class ="close-btn"  style="background: transparent; color: #999; width: 40px; height: 40px; border-radius: 50%; font-size: 20px; border: none; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#333';" onmouseout="this.style.background='transparent'; this.style.color='#999';">&times;</button>
    
      </div>
      
      <div class="card-body" style="padding: 32px;">
        <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: 12px;">
          <i class="fas fa-exclamation-circle"></i>
          <strong>Warning:</strong> This action cannot be undone!
        </div>

        <p style="margin: 0 0 16px 0; color: #333; font-size: 15px; line-height: 1.6;">
          You are about to permanently delete your employer account for
          <strong>{{ Auth::user()->company_name ?? 'your company' }}</strong>.
        </p>

        <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">
          This will immediately and permanently remove:
        </p>

        <ul style="margin: 0 0 24px 20px; color: #666; font-size: 14px; line-height: 1.8;">
          <li><strong>{{ \App\Models\JobPosting::where('employer_id', Auth::id())->count() }} job posting(s)</strong></li>
          <li><strong>{{ \App\Models\Application::whereHas('jobPosting', function($q) { $q->where('employer_id', Auth::id()); })->count() }} application(s)</strong> from job seekers</li>
          <li>Your company profile and business permit</li>
          <li>All associated data and history</li>
        </ul>

        <form id="deleteAccountForm" method="POST" action="{{ route('account.delete') }}">
          @csrf
          @method('DELETE')
          
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="form-label" style="font-weight: 600; color: #333; display: block; margin-bottom: 8px; font-size: 14px;">
              Type <strong style="color: #dc3545;">DELETE</strong> to confirm:
            </label>
            <input type="text"
                   id="deleteConfirmInput"
                   class="form-control"
                   placeholder="Type DELETE in capital letters"
                   required
                   autocomplete="off"
                   style="width: 100%; padding: 14px 16px; border: 2px solid #dc3545; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;">
            <small style="color: #6c757d; display: block; margin-top: 8px; font-size: 13px;">
              This verification step ensures you understand the consequences.
            </small>
          </div>

          <div style="display:flex; gap:12px; justify-content: flex-end; margin-top: 30px; flex-wrap: wrap;">
            <button type="button"
                    onclick="closeDeleteAccountModal()"
                    class="btn btn-secondary"
                    style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s;">
              Cancel
            </button>
            <button type="submit"
                    id="confirmDeleteBtn"
                    class="btn btn-danger"
                    disabled
                    style="padding: 14px 26px; background: #dc3545; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3); opacity: 0.6;">
              <i class="fas fa-trash-alt"></i> Permanently Delete Account
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div id="changePasswordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
    <div class="card" style="max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3); margin: 0; border-radius: 16px;">
      <div class="card-header" style="background: #fff; border-bottom: none; position: relative;">
      <h3 class="card-title" style="color: #2C3E50; margin: 0; font-weight: 600; display: flex; align-items: center; gap: 12px; padding-right: 50px;">
          <i class="fas fa-lock" style="color: #5B9BD5; font-size: 24px;"></i>
          Change Password
      </h3>
        <button onclick="closeChangePasswordModal()" class ="close-btn" style="background: transparent; color: #999; width: 40px; height: 40px; border-radius: 50%; font-size: 20px; border: none; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#333';" onmouseout="this.style.background='transparent'; this.style.color='#999';">&times;</button>
      </div>

      <div class="card-body">
        <form method="POST" action="{{ route('change.password.submit') }}">
          @csrf

          <div style="margin-bottom: 18px;">
            <label for="current_password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Current Password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" required
                   style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;">
          </div>

          <div style="margin-bottom: 18px;">
            <label for="password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">New Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required
                   style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;">
          </div>

          <div style="margin-bottom: 24px;">
            <label for="password_confirmation" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required
                   style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;">
          </div>

          <div style="display:flex; gap:12px; justify-content: flex-end; margin-top: 30px;">
            <button type="button" onclick="closeChangePasswordModal()" class="btn btn-secondary" style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s;">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" style="padding: 14px 26px; background: #5B9BD5; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(91, 155, 213, 0.3);">
              Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Phone Verification Modal -->
  <div id="phoneVerificationModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
    <div class="card" style="max-width:450px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.3); margin: 0;">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-mobile-alt"></i> Verify Phone Number
        </h3>
      </div>
      
      <div class="card-body">
        <div id="otpStep1">
          <p style="margin:0 0 15px 0; color:#666; font-size:14px;">
            Enter your new phone number to receive a verification code via email.
          </p>
          <div class="form-group">
            <label class="form-label">New Phone Number</label>
            <input type="text" 
                   id="new_phone_number" 
                   class="form-control" 
                   placeholder="e.g., 0917 123 4567">
          </div>
          <div style="display:flex; gap:12px; margin-top:20px;">
            <button type="button" 
                    onclick="closePhoneVerificationModal()" 
                    class="btn btn-secondary" 
                    style="flex:1;">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" 
                    onclick="sendPhoneOTP()" 
                    id="sendOtpBtn" 
                    class="btn btn-primary" 
                    style="flex:1;">
              <i class="fas fa-paper-plane"></i> Send Code
            </button>
          </div>
        </div>

        <div id="otpStep2" style="display:none;">
          <p style="margin:0 0 15px 0; color:#666; font-size:14px;">
            We sent a 6-digit code to your email. Enter it below:
          </p>
          <div class="form-group">
            <label class="form-label">Verification Code</label>
            <input type="text" 
                   id="otp_code" 
                   class="form-control" 
                   maxlength="6" 
                   placeholder="123456" 
                   style="text-align:center; font-size:24px; letter-spacing:8px; font-weight:700;">
          </div>
          <div style="display:flex; gap:12px; margin-top:20px;">
            <button type="button" 
                    onclick="backToStep1()" 
                    class="btn btn-secondary" 
                    style="flex:1;">
              <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="button" 
                    onclick="verifyPhoneOTP()" 
                    id="verifyOtpBtn" 
                    class="btn btn-success" 
                    style="flex:1;">
              <i class="fas fa-check-circle"></i> Verify
            </button>
          </div>
        </div>

        <div id="otpMessage" class="alert" style="margin-top:15px; display:none;"></div>
      </div>
    </div>
  </div>

  <!-- System-style Confirm Modal -->
  <div id="systemConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:12000; align-items:center; justify-content:center;">
    <div style="background:#fff; max-width:600px; width:92%; padding:16px 20px; border-radius:10px; box-shadow:0 12px 48px rgba(0,0,0,0.35);">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
        <div style="display:flex; gap:12px; align-items:center;">
          <div style="width:44px; height:44px; border-radius:50%; background:#f0f4f7; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-exclamation-triangle" style="color:#2b6cb0; font-size:18px;"></i>
          </div>
          <div>
            <h4 id="systemConfirmTitle" style="margin:0; font-size:16px;">Confirm</h4>
            <div id="systemConfirmMessage" style="color:#444; margin-top:8px; font-size:14px;"></div>
          </div>
        </div>
        <div style="display:flex; gap:10px;">
          <button id="systemConfirmCancel" class="btn btn-secondary" style="background:#edf2f7;border:1px solid #cbd5e1;padding:10px 14px;">Cancel</button>
          <button id="systemConfirmOk" class="btn btn-primary" style="background:#2b6cb0;border-color:#2b6cb0;padding:10px 14px;color:#fff;">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- System-style Alert Modal -->
  <div id="systemAlertModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:12000; align-items:center; justify-content:center;">
    <div style="background:#fff; max-width:520px; width:90%; padding:16px 18px; border-radius:10px; box-shadow:0 12px 48px rgba(0,0,0,0.35);">
      <div style="display:flex; gap:12px; align-items:flex-start;">
        <div style="width:44px; height:44px; border-radius:50%; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
          <i id="systemAlertIcon" class="fas fa-info-circle" style="color:#2b6cb0; font-size:18px;"></i>
        </div>
        <div style="flex:1;">
          <h4 id="systemAlertTitle" style="margin:0; font-size:16px;">Notice</h4>
          <div id="systemAlertMessage" style="color:#444; margin-top:8px; font-size:14px;"></div>
        </div>
        <div style="display:flex; align-items:flex-start;">
          <button id="systemAlertOk" class="btn btn-primary" style="background:#2b6cb0;border-color:#2b6cb0;padding:8px 12px;color:#fff;">OK</button>
        </div>
      </div>
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

    // Format phone number as user types
    const phoneInput = document.querySelector('input[name="phone_number"]');
    if (phoneInput) {
      // Format existing value on page load
      formatPhoneNumber(phoneInput);

      // Format as user types
      phoneInput.addEventListener('input', function(e) {
        formatPhoneNumber(e.target);
      });
    }

    function formatPhoneNumber(input) {
      let value = input.value.replace(/\D/g, ''); // Remove non-digits
      let formatted = '';

      if (value.length > 0) {
        // Philippine format: 0917 123 4567 or +63 917 123 4567
        if (value.startsWith('63')) {
          // International format
          formatted = '+63 ';
          value = value.substring(2);
          if (value.length > 0) formatted += value.substring(0, 3);
          if (value.length > 3) formatted += ' ' + value.substring(3, 6);
          if (value.length > 6) formatted += ' ' + value.substring(6, 10);
        } else {
          // Local format
          if (value.length > 0) formatted = value.substring(0, 4);
          if (value.length > 4) formatted += ' ' + value.substring(4, 7);
          if (value.length > 7) formatted += ' ' + value.substring(7, 11);
        }
      }

      // Email verification: allow user to manually request a verification email
      window.confirmAndSendVerification = function() {
        // Prefer our custom modal; fallback to native confirm if modal not found
        const modal = document.getElementById('emailConfirmModal');
        if (!modal) {
          if (!confirm('Send verification email to {{ Auth::user()->email }}?')) return;
          window.sendVerificationEmail();
          return;
        }

        modal.style.display = 'flex';

        // Wire up buttons once (idempotent)
        const ok = document.getElementById('emailConfirmOk');
        const cancel = document.getElementById('emailConfirmCancel');
        const closeX = document.getElementById('emailConfirmClose');

        function hideModal() { modal.style.display = 'none'; }

        if (ok && !ok._bound) {
          ok.addEventListener('click', function() {
            hideModal();
            window.sendVerificationEmail();
          });
          ok._bound = true;
        }

        if (cancel && !cancel._bound) {
          cancel.addEventListener('click', function() { hideModal(); });
          cancel._bound = true;
        }

        if (closeX && !closeX._bound) {
          closeX.addEventListener('click', function() { hideModal(); });
          closeX._bound = true;
        }
      }

      window.sendVerificationEmail = async function() {
        const btn = document.getElementById('sendEmailVerifyBtn');
        const msgDiv = document.getElementById('emailVerifyMessage');
        btn.disabled = true;
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        try {
          const response = await fetch('{{ route('verification.resend') }}', {
            method: 'POST',
            credentials: 'same-origin', // include cookies so server can authenticate the user
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          });

          // Try to parse JSON if the server returns it, otherwise treat a 200/302 as success
          let data = {};
          const contentType = response.headers.get('content-type') || '';
          if (contentType.indexOf('application/json') !== -1) {
            data = await response.json().catch(() => ({}));
          }

          if (response.ok) {
            // ok (200) - assume verification was queued/sent
            showEmailVerifyMessage(data.message || 'Verification email sent. Check your inbox.', 'success');
          } else if (response.status === 419) {
            // CSRF/session issue - fallback to form submit
            showEmailVerifyMessage('Session expired. Submitting fallback request...', 'danger');
            // submit the hidden form so Laravel handles the redirect/flash
            document.getElementById('emailVerifyFallbackForm').submit();
          } else if (response.status === 401) {
            // Not authenticated - fall back to form submit which will redirect to login
            showEmailVerifyMessage('You must be signed in to request a verification email. Redirecting...', 'danger');
            document.getElementById('emailVerifyFallbackForm').submit();
          } else if (response.status >= 300 && response.status < 400) {
            // Redirect - treat as success
            showEmailVerifyMessage('Verification email sent. Check your inbox.', 'success');
          } else {
            showEmailVerifyMessage(data.message || 'Failed to send verification email. Try again later.', 'danger');
          }
        } catch (err) {
          showEmailVerifyMessage('Network error sending verification. Please try again.', 'danger');
        } finally {
          btn.disabled = false;
          btn.innerHTML = original;
        }
      }

      window.showEmailVerifyMessage = function(message, type) {
        const msgDiv = document.getElementById('emailVerifyMessage');
        if (!msgDiv) return;
        msgDiv.style.display = 'block';
        msgDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger');
        msgDiv.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
        setTimeout(() => {
          msgDiv.style.opacity = '0';
          setTimeout(() => { msgDiv.style.display = 'none'; msgDiv.style.opacity = '1'; }, 400);
        }, 4000);
      }

      input.value = formatted;
    }
  });
  
  // System-style confirm/alert helpers
  window.systemConfirm = function(title, message) {
    return new Promise(resolve => {
      const modal = document.getElementById('systemConfirmModal');
      const t = document.getElementById('systemConfirmTitle');
      const m = document.getElementById('systemConfirmMessage');
      const ok = document.getElementById('systemConfirmOk');
      const cancel = document.getElementById('systemConfirmCancel');
      if (!modal || !ok || !cancel || !t || !m) {
        // fallback to native confirm
        resolve(confirm(message));
        return;
      }

      t.textContent = title || 'Confirm';
      m.textContent = message || '';
      modal.style.display = 'flex';

      function cleanup() {
        modal.style.display = 'none';
        ok.removeEventListener('click', onOk);
        cancel.removeEventListener('click', onCancel);
      }

      function onOk() { cleanup(); resolve(true); }
      function onCancel() { cleanup(); resolve(false); }

      ok.addEventListener('click', onOk);
      cancel.addEventListener('click', onCancel);
    });
  }

  window.systemAlert = function(message, level) {
    return new Promise(resolve => {
      const modal = document.getElementById('systemAlertModal');
      const title = document.getElementById('systemAlertTitle');
      const msg = document.getElementById('systemAlertMessage');
      const ok = document.getElementById('systemAlertOk');
      const icon = document.getElementById('systemAlertIcon');
      if (!modal || !ok || !msg) {
        alert(message);
        resolve();
        return;
      }

      // level can be 'warning'|'error'|'info' - adjust icon/color
      if (level === 'warning') {
        title.textContent = 'Warning';
        icon.className = 'fas fa-exclamation-circle';
        icon.style.color = '#d97706';
      } else if (level === 'danger' || level === 'error') {
        title.textContent = 'Error';
        icon.className = 'fas fa-times-circle';
        icon.style.color = '#b91c1c';
      } else {
        title.textContent = 'Notice';
        icon.className = 'fas fa-info-circle';
        icon.style.color = '#2b6cb0';
      }

      msg.textContent = message || '';
      modal.style.display = 'flex';

      function cleanup() {
        modal.style.display = 'none';
        ok.removeEventListener('click', onOk);
      }

      function onOk() { cleanup(); resolve(); }
      ok.addEventListener('click', onOk);
    });
  }
  
  function openPhoneVerificationModal() {
    const currentPhone = document.getElementById('phone_number_input').value;
    document.getElementById('new_phone_number').value = currentPhone;
    document.getElementById('phoneVerificationModal').style.display = 'flex';
    document.getElementById('otpStep1').style.display = 'block';
    document.getElementById('otpStep2').style.display = 'none';
    document.getElementById('otpMessage').style.display = 'none';
  }

  function closePhoneVerificationModal() {
    document.getElementById('phoneVerificationModal').style.display = 'none';
  }

  function backToStep1() {
    document.getElementById('otpStep1').style.display = 'block';
    document.getElementById('otpStep2').style.display = 'none';
    document.getElementById('otpMessage').style.display = 'none';
  }

  async function sendPhoneOTP() {
    const phoneNumber = document.getElementById('new_phone_number').value.trim();
    if (!phoneNumber) {
      showOtpMessage('Please enter a phone number', 'danger');
      return;
    }

    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
      const response = await fetch('{{ route('profile.sendPhoneOTP') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ new_phone_number: phoneNumber })
      });

      const data = await response.json();

      if (data.success) {
        document.getElementById('otpStep1').style.display = 'none';
        document.getElementById('otpStep2').style.display = 'block';
        showOtpMessage(data.message, 'success');
      } else {
        showOtpMessage(data.message, 'danger');
      }
    } catch (error) {
      showOtpMessage('Failed to send verification code. Please try again.', 'danger');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Code';
    }
  }

  async function verifyPhoneOTP() {
    const phoneNumber = document.getElementById('new_phone_number').value.trim();
    const otpCode = document.getElementById('otp_code').value.trim();

    if (!otpCode || otpCode.length !== 6) {
      showOtpMessage('Please enter the 6-digit verification code', 'danger');
      return;
    }

    const btn = document.getElementById('verifyOtpBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    try {
      const response = await fetch('{{ route('profile.verifyPhoneOTP') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
          new_phone_number: phoneNumber,
          otp_code: otpCode 
        })
      });

      const data = await response.json();

      if (data.success) {
        showOtpMessage(data.message, 'success');
        // Update the phone number field
        document.getElementById('phone_number_input').value = phoneNumber;
        // Close modal after 2 seconds
        setTimeout(() => {
          closePhoneVerificationModal();
          location.reload(); // Refresh to show updated number
        }, 2000);
      } else {
        showOtpMessage(data.message, 'danger');
      }
    } catch (error) {
      showOtpMessage('Verification failed. Please try again.', 'danger');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-check-circle"></i> Verify';
    }
  }

  function showOtpMessage(message, type) {
    const msgDiv = document.getElementById('otpMessage');
    msgDiv.style.display = 'block';
    msgDiv.textContent = message;
    msgDiv.className = 'alert alert-' + type;
  }

  // Change Password Modal Functions
  function openChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
  }

  function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
  }

  // Delete Account Modal Functions
  function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').style.display = 'flex';
    document.getElementById('deleteConfirmInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
  }

  function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').style.display = 'none';
    document.getElementById('deleteConfirmInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
  }

  // Enable delete button only when "DELETE" is typed
  document.addEventListener('DOMContentLoaded', function() {
    const deleteInput = document.getElementById('deleteConfirmInput');
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (deleteInput && deleteBtn) {
      deleteInput.addEventListener('input', function() {
        if (this.value === 'DELETE') {
          deleteBtn.disabled = false;
          deleteBtn.style.opacity = '1';
        } else {
          deleteBtn.disabled = true;
          deleteBtn.style.opacity = '0.6';
        }
      });
    }

    // Confirmation before submitting
    const deleteForm = document.getElementById('deleteAccountForm');
    if (deleteForm) {
      deleteForm.addEventListener('submit', async function(e) {
        // prevent default initially; we'll submit programmatically when confirmed
        e.preventDefault();

        if (deleteInput.value !== 'DELETE') {
          await window.systemAlert('Please type DELETE to confirm account deletion.', 'warning');
          return false;
        }

        const confirmed = await window.systemConfirm('Confirm Account Deletion', 'Are you absolutely sure? This action CANNOT be undone. All your data will be permanently deleted.');
        if (!confirmed) return false;

        // proceed with the original form submission
        deleteForm.submit();
      });
    }

    // Close modal on outside click
    const deleteModal = document.getElementById('deleteAccountModal');
    if (deleteModal) {
      deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
          closeDeleteAccountModal();
        }
      });
    }

    const changePasswordModal = document.getElementById('changePasswordModal');
    if (changePasswordModal) {
      changePasswordModal.addEventListener('click', function(e) {
        if (e.target === changePasswordModal) {
          closeChangePasswordModal();
        }
      });
    }
  });
</script>

@include('partials.logout-confirm')

</body>
</html>