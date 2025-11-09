@extends('jobseeker.layouts.base')

@section('title', 'Settings - Job Portal Mandaluyong')

@php
    $pageTitle = 'JOB SEEKER SETTINGS';
@endphp

@section('content')
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-cog"></i>
        Account Settings
    </h1>
    <p class="page-subtitle">Manage your profile and preferences</p>
</div>

@if(session('success'))
    <div class="alert alert-success flash-message">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger flash-message">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
    </div>
@endif

<!-- Account Settings Card -->
<div class="card">
    <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
        <h2 class="card-title" style="margin:0;">Account Settings</h2>
        @if(Auth::user()->user_type === 'job_seeker')
            @if(Auth::user()->employment_status === 'employed')
                @php
                    $employmentTitle = 'Employed';
                    if (Auth::user()->hired_by_company) {
                        $employmentTitle .= ' at ' . Auth::user()->hired_by_company;
                    }
                    if (Auth::user()->hired_date) {
                        try { $employmentTitle .= ' since ' . Auth::user()->hired_date->format('M d, Y'); } catch (\Throwable $e) {}
                    }
                @endphp
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge badge-success" title="{{ $employmentTitle }}" style="padding:6px 10px; border-radius:14px; display:inline-flex; align-items:center; gap:6px; font-size:12px;">
                        <i class="fas fa-briefcase"></i>
                        Employed
                    </span>
                    <button type="button" onclick="openResignModal()" class="btn btn-danger btn-sm" title="Submit resignation" style="white-space:nowrap;">
                        <i class="fas fa-door-open"></i> Resign
                    </button>
                </div>
            @else
                <span class="badge badge-info" title="Actively seeking opportunities" style="padding:6px 10px; border-radius:14px; display:inline-flex; align-items:center; gap:6px; font-size:12px;">
                    <i class="fas fa-search"></i>
                    Actively Seeking
                </span>
            @endif
        @endif
    </div>
    
    <div class="card-body" style="padding: 0;">
        <div style="padding: 20px; border-bottom: 1px solid #E0E6EB; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-envelope" style="color: #648EB5; font-size: 20px;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 4px; color: #1E3A5F; font-size: 15px;">Email Address</strong>
                    <p style="margin: 0; color: #666; font-size: 14px;">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="openChangeEmailModal()" style="white-space: nowrap;">
                <i class="fas fa-edit"></i> Change Email
            </button>
        </div>
        
        <div style="padding: 20px; border-bottom: 1px solid #E0E6EB; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-lock" style="color: #648EB5; font-size: 20px;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 4px; color: #1E3A5F; font-size: 15px;">Password</strong>
                    <p style="margin: 0; color: #666; font-size: 14px;">Change your password to keep your account secure</p>
                </div>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="openChangePasswordModal()" style="white-space: nowrap;">
                <i class="fas fa-key"></i> Change Password
            </button>
        </div>
        
        <div style="padding: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-user-times" style="color: #dc3545; font-size: 20px;"></i>
                <div>
                    <strong style="display: block; margin-bottom: 4px; color: #1E3A5F; font-size: 15px;">Delete Account</strong>
                    <p style="margin: 0; color: #666; font-size: 14px;">Permanently delete your account and all data</p>
                </div>
            </div>
            <button class="btn btn-danger btn-sm" onclick="openDeleteAccountModal()" style="white-space: nowrap;">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </div>
    </div>
</div>

<!-- Employment Status Card (if job seeker) -->
{{-- Employment Status card removed: status now shown as a badge in Account Settings header --}}

<!-- Personal Information Card -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Personal Information</h2>
    </div>
    
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="first_name" 
                           class="form-control" 
                           required 
                           value="{{ old('first_name', Auth::user()->first_name) }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="last_name" 
                           class="form-control" 
                           required 
                           value="{{ old('last_name', Auth::user()->last_name) }}">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email (login)</label>
                    <input type="email" 
                           class="form-control" 
                           value="{{ Auth::user()->email }}" 
                           readonly 
                           style="background: #f8f9fa; cursor: not-allowed;">
                    <div style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                        @if(method_exists(Auth::user(), 'hasVerifiedEmail') && Auth::user()->hasVerifiedEmail())
                            <span style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:14px; background:#d1fae5; color:#065f46; font-weight:600; font-size:13px;">
                                <i class="fas fa-check-circle"></i>
                                Verified
                            </span>
                        @else
                            <span style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:14px; background:#fff4e5; color:#92400e; font-weight:600; font-size:13px;">
                                <i class="fas fa-exclamation-circle"></i>
                                Email not verified
                            </span>

                            <button type="button" class="btn btn-sm btn-secondary" style="padding:6px 10px; font-size:13px; margin-left:8px;" onclick="resendVerification()">Resend verification email</button>
                        @endif
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" 
                           name="phone_number" 
                           class="form-control" 
                           value="{{ old('phone_number', Auth::user()->phone_number) }}" 
                           placeholder="e.g., 0917 123 4567">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Date of Birth</label>
                <input type="date" 
                       name="birthday" 
                       class="form-control" 
                       value="{{ old('birthday', Auth::user()->date_of_birth ? \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('Y-m-d') : '') }}">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Barangay in Mandaluyong City</label>
                    <select name="location" class="form-control">
                        <option value="">Select your barangay</option>
                        @php
                            $barangays = ['Addition Hills', 'Bagong Silang', 'Barangka Drive', 'Barangka Ibaba', 'Barangka Ilaya', 'Barangka Itaas', 'Buayang Bato', 'Burol', 'Daang Bakal', 'Hagdang Bato Itaas', 'Hagdang Bato Libis', 'Harapin Ang Bukas', 'Highway Hills', 'Hulo', 'Mabini-J. Rizal', 'Malamig', 'Mauway', 'Namayan', 'New Zañiga', 'Old Zañiga', 'Pag-asa', 'Plainview', 'Pleasant Hills', 'Poblacion', 'San Jose', 'Vergara', 'Wack-Wack Greenhills'];
                        @endphp
                        @foreach($barangays as $brgy)
                            <option value="{{ $brgy }}" {{ Auth::user()->location == $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Complete Address</label>
                    <input type="text" 
                           name="address" 
                           class="form-control" 
                           value="{{ old('address', Auth::user()->address) }}" 
                           placeholder="House/Unit No., Street Name">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Profile Picture</label>
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
                <label class="form-label">Professional Summary</label>
                <textarea name="summary" 
                          class="form-control" 
                          rows="4" 
                          placeholder="Brief overview of your professional background and career goals...">{{ old('summary', Auth::user()->summary) }}</textarea>
                <small class="form-help">
                    <i class="fas fa-info-circle"></i> This will be shown to employers when you apply
                </small>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Education Level</label>
                    <input type="text" 
                           name="education_level" 
                           class="form-control" 
                           value="{{ old('education_level', Auth::user()->education_level) }}"
                           placeholder="e.g., Bachelor's Degree">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Years of Experience</label>
                    <input type="number" 
                           name="years_of_experience" 
                           class="form-control" 
                           value="{{ old('years_of_experience', Auth::user()->years_of_experience) }}" 
                           min="0"
                           placeholder="0">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Skills</label>
                <input type="text" 
                       name="skills" 
                       class="form-control" 
                       value="{{ old('skills', Auth::user()->skills) }}"
                       placeholder="e.g., Communication, Microsoft Office, Customer Service">
                <small class="form-help">
                    <i class="fas fa-info-circle"></i> Separate skills with commas
                </small>
            </div>
            
            <div class="form-group">
                <label class="form-label">
                    Resume (PDF)
                    @if(Auth::user()->resume_file)
                        <span style="color: #28a745; font-size: 14px; margin-left: 8px;">
                            <i class="fas fa-check-circle"></i> File uploaded
                        </span>
                    @endif
                </label>
                
                @if(Auth::user()->resume_file)
                    <div style="background: #d4edda; border: 1px solid #28a745; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-check-circle" style="color: #28a745; font-size: 20px;"></i>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #155724; font-size: 14px;">Resume already uploaded</div>
                            <div style="font-size: 12px; color: #155724;">You can upload a new file to replace the current one</div>
                        </div>
                    </div>
                @endif
                
                @if($errors->has('resume_file'))
                    <div style="background: #f8d7da; border: 1px solid #dc3545; border-radius: 6px; padding: 10px 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-exclamation-circle" style="color: #dc3545; font-size: 20px;"></i>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #721c24; font-size: 14px;">Upload Failed</div>
                            <div style="font-size: 13px; color: #721c24;">{{ $errors->first('resume_file') }}</div>
                        </div>
                    </div>
                @endif
                
                <div style="position: relative;">
                    @if(Auth::user()->resume_file)
                        <div style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #28a745; font-size: 18px; pointer-events: none; z-index: 1;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    @endif
                    <input type="file" 
                           name="resume_file" 
                           class="form-control" 
                           accept=".pdf"
                           style="padding: 8px; @if(Auth::user()->resume_file) border-color: #28a745; background-color: #f0fff4; @endif">
                </div>
                @if(Auth::user()->resume_file)
                    <div style="margin-top:8px;">
                        <a href="{{ asset('storage/' . Auth::user()->resume_file) }}" 
                           target="_blank"
                           class="btn-sm btn-secondary"
                           style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-file-pdf"></i> View Current Resume
                        </a>
                        <label style="margin-left:12px; display: inline-flex; align-items: center; gap:8px; font-weight:500; cursor: pointer;">
                            <input type="checkbox" name="remove_resume" value="1">
                            Remove current resume
                        </label>
                    </div>
                @endif
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4" style="flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 18px; background:#5B9BD5; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; box-shadow: 0 2px 0 rgba(0,0,0,0.06);">
                    <i class="fas fa-save"></i>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Change Email Modal -->
<div id="changeEmailModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 420px; border-radius: 16px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <button onclick="closeChangeEmailModal()" class="close-btn" style="background: transparent; color: #999; width: 40px; height: 40px; border-radius: 50%; font-size: 20px; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#333';" onmouseout="this.style.background='transparent'; this.style.color='#999';">&times;</button>
        <h2 style="color: #2C3E50; margin-bottom: 25px; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-envelope" style="color: #5B9BD5; font-size: 24px;"></i>
            Change Email Address
        </h2>
        <form id="changeEmailForm" method="POST" action="{{ route('profile.changeEmail') }}">
            @csrf
            <div style="margin-bottom: 24px;">
                <label for="new_email" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">New Email Address</label>
                <input type="email"
                       id="new_email"
                       name="new_email"
                       value="{{ old('new_email', '') }}"
                       required
                       placeholder="Enter the new email to receive a verification code"
                       style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s; focus:ring-2 focus:ring-blue-500 focus:border-blue-500;">
                <div id="changeEmailError" style="color:#dc3545; margin-top:8px; display:none;"></div>
            </div>
            
            <div class="button-group" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeChangeEmailModal()" class="btn-cancel" style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; hover:bg-gray-50 hover:border-gray-300;">
                    Cancel
                </button>
                <button type="submit" id="changeEmailSubmit" class="btn-primary" style="padding: 14px 26px; background: #5B9BD5; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(91, 155, 213, 0.3);">
                    Send Code
                </button>
            </div>
        </form>

        <!-- Verify OTP Modal (shows after sending code) -->
        <div id="verifyEmailOtpModal" style="display:none; margin-top:18px;">
            <div style="padding: 16px; border-radius:8px; background:#f8fafc; border:1px solid #e6eef6;">
                <h3 style="margin-top:0; font-size:16px;">Enter Verification Code</h3>
                <p style="margin:0 0 10px 0; color:#555; font-size:13px;">We sent a 6-digit code to <span id="verifyEmailAddress" style="font-weight:600;"></span>. Enter it below to confirm your new email.</p>
                <input type="hidden" id="verify_new_email" name="verify_new_email" value="">
                <div style="display:flex; gap:8px; align-items:center;">
                    <input type="text" id="otp_code" name="otp_code" placeholder="Enter 6-digit code" maxlength="6" style="flex:1; padding:10px; border:2px solid #E0E6EB; border-radius:8px; font-size:14px;">
                    <button id="verifyOtpBtn" class="btn-primary" style="padding:10px 14px; background:#5B9BD5; color:#fff; border-radius:8px; border:none;">Verify</button>
                </div>
                <div id="verifyOtpError" style="color:#dc3545; margin-top:8px; display:none;"></div>
                <div style="margin-top:10px; font-size:13px; color:#666;">
                    Didn't receive a code? <button id="resendOtpBtn" type="button" class="btn-link" style="background:none; border:none; color:#5B9BD5; cursor:pointer; padding:0;">Resend</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 420px;">
        <button onclick="closeChangePasswordModal()" class="close-btn">&times;</button>
        <h2 style="color: #2C3E50; margin-bottom: 25px; font-size: 22px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <i class="fas fa-lock" style="color: #5B9BD5; font-size: 24px;"></i>
            Change Password
        </h2>
        <form method="POST" action="{{ route('change.password.submit') }}">
            @csrf
            <div style="margin-bottom: 18px;">
                <label for="current_password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Current Password</label>
                <input type="password" 
                       id="current_password" 
                       name="current_password" 
                       autocomplete="current-password" 
                       required
                       style="width: 100%; padding: 12px 15px; border: 2px solid #E0E6EB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.3s;">
            </div>

            <div style="margin-bottom: 18px;">
                <label for="password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">New Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       autocomplete="new-password" 
                       required
                       style="width: 100%; padding: 12px 15px; border: 2px solid #E0E6EB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.3s;">
            </div>

            <div style="margin-bottom: 18px;">
                <label for="password_confirmation" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Confirm New Password</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       autocomplete="new-password" 
                       required
                       style="width: 100%; padding: 12px 15px; border: 2px solid #E0E6EB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.3s;">
            </div>

            <div class="button-group" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeChangePasswordModal()" class="btn-cancel" style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; hover:bg-gray-50 hover:border-gray-300;">
                    Cancel
                </button>
                <button type="submit" class="btn-primary" style="padding: 14px 26px; background: #5B9BD5; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(91, 155, 213, 0.3);">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 500px;">
        <button onclick="closeDeleteAccountModal()" class="close-btn">&times;</button>
        <h2 style="color: #dc3545; margin-bottom: 20px; font-size: 22px; font-weight: 600;">
            <i class="fas fa-exclamation-triangle"></i> Confirm Account Deletion
        </h2>
        
        <div class="alert alert-danger" style="margin-bottom: 20px; padding: 12px; border-radius: 8px; background: #f8d7da; border: 1px solid #f5c2c7; color: #842029;">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Warning:</strong> This action cannot be undone!
        </div>

        <p style="color: #333; margin-bottom: 15px; line-height: 1.6; font-size: 15px;">
            You are about to permanently delete your account for 
            <strong>{{ Auth::user()->name }}</strong>.
        </p>

        <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
            This will immediately and permanently remove:
        </p>

        <ul style="margin: 0 0 20px 20px; color: #666; font-size: 14px; line-height: 1.8;">
            <li><strong>{{ \App\Models\Application::where('user_id', Auth::id())->count() }} job application(s)</strong></li>
            <li>Your resume and profile information</li>
            <li>All bookmarks and saved jobs</li>
            <li>All associated data and history</li>
        </ul>

        <form id="deleteAccountForm" method="POST" action="{{ route('account.delete') }}">
            @csrf
            @method('DELETE')
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="font-weight: 600; color: #333; display: block; margin-bottom: 8px;">
                    Type <strong style="color: #dc3545;">DELETE</strong> to confirm:
                </label>
                <input type="text" 
                       id="deleteConfirmInputJobSeeker" 
                       class="form-control" 
                       placeholder="Type DELETE in capital letters"
                       required
                       autocomplete="off"
                       style="border: 2px solid #dc3545; padding: 10px; font-size: 14px; border-radius: 8px;">
                <small style="color: #6c757d; display: block; margin-top: 6px; font-size: 13px;">
                    This verification step ensures you understand the consequences.
                </small>
            </div>

            <div class="button-group" style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeDeleteAccountModal()" class="btn-cancel" style="padding: 14px 26px; background: transparent; color: #64748b; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; hover:bg-gray-50 hover:border-gray-300;">
                    Cancel
                </button>
                <button type="submit"
                        id="confirmDeleteBtnJobSeeker"
                        class="btn-danger"
                        disabled
                        style="padding: 14px 26px; background: #dc3545; color: white; border: none; border-radius: 12px; cursor: pointer; font-size: 14px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3); opacity: 0.6;">
                    <i class="fas fa-trash-alt"></i> Permanently Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Resign Modal -->
<div id="resignModal" class="modal" style="display:none;">
    <div class="modal-content" style="max-width: 480px;">
        <button onclick="closeResignModal()" class="close-btn">&times;</button>
        <h2 style="color: #2C3E50; margin-bottom: 14px; font-size: 22px; font-weight: 600; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-door-open" style="color:#dc3545;"></i> Confirm Resignation
        </h2>
        <p style="color:#555; margin-bottom:16px; font-size:14px; line-height:1.5;">
            This will update your employment status to <strong>Actively Seeking</strong>. Your employer may be notified. You can optionally include a brief reason.
        </p>
        <form method="POST" action="{{ route('profile.resign') }}" onsubmit="return handleResignSubmit(event)">
            @csrf
            <div style="margin-bottom: 14px;">
                <label for="resign_reason" style="display:block; font-weight:600; color:#2C3E50; margin-bottom:8px; font-size:14px;">Reason (optional)</label>
                <textarea id="resign_reason" name="reason" rows="4" placeholder="e.g., Moving to a new city, focusing on studies, personal reasons..." style="width:100%; padding:12px 14px; border:2px solid #E0E6EB; border-radius:8px; font-size:14px; box-sizing:border-box;"></textarea>
            </div>
            <div class="button-group" style="display:flex; gap:10px; justify-content:flex-end; margin-top: 10px;">
                <button type="button" onclick="closeResignModal()" class="btn-cancel" style="padding: 10px 20px; background:#E4E9EE; color:#555; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600;">Cancel</button>
                <button type="submit" id="resignSubmitBtn" class="btn-danger" style="padding: 10px 20px; background:#dc3545; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600;">
                    <i class="fas fa-check"></i> <span id="resignBtnText">Confirm Resignation</span>
                </button>
            </div>
        </form>
    </div>
    
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; backdrop-filter: blur(3px);"></div>

<style>
/* Modal styling */
.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
}

.modal-content {
    background: #fff;
    border-radius: 16px;
    padding: 32px;
    width: 90%;
    max-width: 420px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border-radius: 50%;
    line-height: 1;
}

.close-btn:hover {
    background: rgba(0,0,0,0.2);
    color: #333;
}

.modal input[type="password"]:focus,
.modal input[type="email"]:focus {
    outline: none;
    border-color: #5B9BD5;
    box-shadow: 0 0 0 3px rgba(91, 155, 213, 0.15);
}

.btn-cancel:hover {
    background: #D0D7DD !important;
}

.btn-primary:hover {
    background: #4A8BC2 !important;
}

.btn-danger:hover {
    background: #c82333 !important;
}

@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 25px 20px;
    }
}
</style>

@endsection

@push('scripts')
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
        formatPhoneNumber(phoneInput);

        phoneInput.addEventListener('input', function(e) {
            formatPhoneNumber(e.target);
        });
    }

    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        let formatted = '';

        if (value.length > 0) {
            if (value.startsWith('63')) {
                formatted = '+63 ';
                value = value.substring(2);
                if (value.length > 0) formatted += value.substring(0, 3);
                if (value.length > 3) formatted += ' ' + value.substring(3, 6);
                if (value.length > 6) formatted += ' ' + value.substring(6, 10);
            } else {
                if (value.length > 0) formatted = value.substring(0, 4);
                if (value.length > 4) formatted += ' ' + value.substring(4, 7);
                if (value.length > 7) formatted += ' ' + value.substring(7, 11);
            }
        }

        input.value = formatted;
    }
});

// Resend verification email (avoid nested forms inside the main profile form)
function resendVerification() {
    const url = "{{ route('verification.resend') }}";
    const token = '{{ csrf_token() }}';

    const button = event?.target || null;
    if (button) {
        button.disabled = true;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    }).then(resp => resp.json().then(body => ({status: resp.status, body}))).then(result => {
        if (result.status >= 200 && result.status < 300) {
            alert(result.body.message || 'Verification link sent.');
            // Optionally reload to show the flash message
            window.location.reload();
        } else {
            alert(result.body.message || 'Unable to send verification email.');
            if (button) button.disabled = false;
        }
    }).catch(err => {
        console.error('Resend verification failed', err);
        alert('Unable to send verification email right now. Please try again later.');
        if (button) button.disabled = false;
    });
}

// Modal functions
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openChangeEmailModal() {
    showModal('changeEmailModal');
}

function closeChangeEmailModal() {
    hideModal('changeEmailModal');
}

function openChangePasswordModal() {
    showModal('changePasswordModal');
}

function closeChangePasswordModal() {
    hideModal('changePasswordModal');
}

function openDeleteAccountModal() {
    showModal('deleteAccountModal');
    // Reset the confirmation input
    const input = document.getElementById('deleteConfirmInputJobSeeker');
    const btn = document.getElementById('confirmDeleteBtnJobSeeker');
    if (input) input.value = '';
    if (btn) {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    }
}

function closeDeleteAccountModal() {
    hideModal('deleteAccountModal');
}

function openResignModal() {
    showModal('resignModal');
}

function closeResignModal() {
    hideModal('resignModal');
}

// Handle resign form submission with spinner
function handleResignSubmit(e) {
    const btn = document.getElementById('resignSubmitBtn');
    const btnText = document.getElementById('resignBtnText');
    
    if (btn.disabled) {
        e.preventDefault();
        return false;
    }
    
    btn.disabled = true;
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    return true;
}

// Enable delete button only when "DELETE" is typed (Job Seeker)
document.addEventListener('DOMContentLoaded', function() {
    const deleteInput = document.getElementById('deleteConfirmInputJobSeeker');
    const deleteBtn = document.getElementById('confirmDeleteBtnJobSeeker');
    
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
        deleteForm.addEventListener('submit', function(e) {
            if (deleteInput && deleteInput.value !== 'DELETE') {
                e.preventDefault();
                alert('Please type DELETE to confirm account deletion.');
                return false;
            }
            
            if (!confirm('Are you absolutely sure? This action CANNOT be undone. All your data will be permanently deleted.')) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Close modal when clicking overlay
document.addEventListener('click', function(e) {
    if (e.target.id === 'modalOverlay') {
        closeChangeEmailModal();
        closeChangePasswordModal();
        closeDeleteAccountModal();
    }
});

    // AJAX flow: submit new email, then show OTP entry
    const changeEmailForm = document.getElementById('changeEmailForm');
    const changeEmailError = document.getElementById('changeEmailError');
    const changeEmailSubmit = document.getElementById('changeEmailSubmit');
    const verifyModal = document.getElementById('verifyEmailOtpModal');
    const verifyEmailAddress = document.getElementById('verifyEmailAddress');
    const verifyNewEmailInput = document.getElementById('verify_new_email');
    const otpInput = document.getElementById('otp_code');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const verifyOtpError = document.getElementById('verifyOtpError');
    const resendOtpBtn = document.getElementById('resendOtpBtn');

    if (changeEmailForm) {
        changeEmailForm.addEventListener('submit', function(e) {
            e.preventDefault();
            changeEmailError.style.display = 'none';
            const newEmail = document.getElementById('new_email').value.trim();
            if (!newEmail) {
                changeEmailError.textContent = 'Please enter a valid email address.';
                changeEmailError.style.display = 'block';
                return;
            }

            changeEmailSubmit.disabled = true;
            changeEmailSubmit.textContent = 'Sending...';

            fetch("{{ route('profile.changeEmail') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ new_email: newEmail })
            }).then(r => r.json().then(b => ({status:r.status, body:b}))).then(result => {
                changeEmailSubmit.disabled = false;
                changeEmailSubmit.textContent = 'Send Code';
                if (result.status >= 200 && result.status < 300 && result.body.success) {
                    // Show verify OTP block
                    verifyEmailAddress.textContent = newEmail;
                    verifyNewEmailInput.value = newEmail;
                    verifyModal.style.display = 'block';
                } else {
                    changeEmailError.textContent = result.body.message || 'Unable to send verification code.';
                    changeEmailError.style.display = 'block';
                }
            }).catch(err => {
                changeEmailSubmit.disabled = false;
                changeEmailSubmit.textContent = 'Send Code';
                changeEmailError.textContent = 'Failed to send verification code. Please try again later.';
                changeEmailError.style.display = 'block';
            });
        });
    }

    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', function() {
            verifyOtpError.style.display = 'none';
            const code = otpInput.value.trim();
            const newEmailVal = verifyNewEmailInput.value;
            if (!/^[0-9]{6}$/.test(code)) {
                verifyOtpError.textContent = 'Enter a 6-digit code.';
                verifyOtpError.style.display = 'block';
                return;
            }

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.textContent = 'Verifying...';

            fetch("{{ route('profile.verifyEmailOTP') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ new_email: newEmailVal, otp_code: code })
            }).then(r => r.json().then(b => ({status:r.status, body:b}))).then(result => {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify';
                if (result.status >= 200 && result.status < 300 && result.body.success) {
                    alert(result.body.message || 'Email updated successfully.');
                    window.location.reload();
                } else {
                    verifyOtpError.textContent = result.body.message || 'Invalid code.';
                    verifyOtpError.style.display = 'block';
                }
            }).catch(err => {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify';
                verifyOtpError.textContent = 'Verification failed. Please try again later.';
                verifyOtpError.style.display = 'block';
            });
        });
    }

    if (resendOtpBtn) {
        resendOtpBtn.addEventListener('click', function() {
            const newEmailVal = verifyNewEmailInput.value;
            resendOtpBtn.disabled = true;
            resendOtpBtn.textContent = 'Resending...';

            fetch("{{ route('profile.changeEmail') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ new_email: newEmailVal })
            }).then(r => r.json()).then(body => {
                resendOtpBtn.disabled = false;
                resendOtpBtn.textContent = 'Resend';
                alert(body.message || 'Verification code resent.');
            }).catch(err => {
                resendOtpBtn.disabled = false;
                resendOtpBtn.textContent = 'Resend';
                alert('Unable to resend code right now.');
            });
        });
    }
</script>
@endpush
