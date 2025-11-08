{{-- 
  Unified Job Seeker Sidebar Component
  Include this on every job seeker page: @include('jobseeker.partials.sidebar')
  Automatically highlights the active page
--}}

<div class="sidebar">
  @php
    // Resolve the user context: prefer a passed $user variable, otherwise use the
    // currently authenticated user. Using a local $viewUser avoids undefined
    // variable notices when $user isn't provided by the caller.
    $viewUser = isset($user) ? $user : auth()->user();
    $profilePic = optional($viewUser)->profile_picture;
    $firstName = optional($viewUser)->first_name;
    $lastName = optional($viewUser)->last_name;
    $resumeStatus = optional($viewUser)->resume_verification_status;
  @endphp

  <div class="profile-ellipse">
    <div class="profile-icon">
      @if($profilePic)
        <img src="{{ asset('storage/' . $profilePic) }}" 
             alt="Profile Picture" 
             style="cursor:pointer;" 
             onclick="showProfilePictureModal()">
      @else
        <i class="fas fa-user" style="cursor:pointer;" onclick="showProfilePictureModal()"></i>
      @endif
    </div>
  </div>

  <div class="profile-name">
    {{ $firstName }} {{ $lastName }}
    @if($resumeStatus === 'verified')
      <i class="fas fa-check-circle" title="Resume Verified" style="color: #648EB5; margin-left: 6px; font-size: 14px;"></i>
    @endif
  </div>

  {{-- Profile modal component (renders modal DOM and scripts) --}}
  <x-profile-modal :pic-url="$profilePic ? asset('storage/' . $profilePic) : null" :name="trim(($firstName ?? '') . ' ' . ($lastName ?? ''))" />
  
  @php
    $currentRoute = Route::currentRouteName();
  @endphp
  
  <a href="{{ route('dashboard') }}" 
     class="sidebar-btn {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
    <i class="fas fa-home sidebar-btn-icon"></i> Dashboard
  </a>
  
  <a href="{{ route('recommendation') }}" 
     class="sidebar-btn {{ $currentRoute === 'recommendation' ? 'active' : '' }}">
    <i class="fas fa-suitcase sidebar-btn-icon"></i> Recommendation
  </a>
  
  <a href="{{ route('my-applications') }}" 
     class="sidebar-btn {{ $currentRoute === 'my-applications' ? 'active' : '' }}">
    <i class="fas fa-file-alt sidebar-btn-icon"></i> My Applications
  </a>
  
  @if(optional($viewUser)->user_type === 'job_seeker')
    <a href="{{ route('work-history') }}" 
       class="sidebar-btn {{ $currentRoute === 'work-history' ? 'active' : '' }}">
      <i class="fas fa-clock-rotate-left sidebar-btn-icon"></i> Work History
    </a>
  @endif
  
  <a href="{{ route('bookmarks') }}" 
     class="sidebar-btn {{ $currentRoute === 'bookmarks' ? 'active' : '' }}">
    <i class="fas fa-bookmark sidebar-btn-icon"></i> Bookmarks
  </a>
  
  <a href="{{ route('settings') }}" 
     class="sidebar-btn {{ $currentRoute === 'settings' ? 'active' : '' }}">
    <i class="fas fa-cog sidebar-btn-icon"></i> Settings
  </a>
  
  <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;" onsubmit="return showLogoutModal(this);">
    @csrf
    <button type="submit" class="sidebar-btn"
      style="border: none; background: #648EB5; color: #FFF; font-size: 15px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 14px; height: 44px; display: flex; align-items: center; justify-content: center; gap: 12px;">
      <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
      Logout
    </button>
  </form>
</div>

@include('partials.logout-confirm')
