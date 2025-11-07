{{-- 
  Unified Job Seeker Sidebar Component
  Include this on every job seeker page: @include('jobseeker.partials.sidebar')
  Automatically highlights the active page
--}}

<div class="sidebar">
  <div class="profile-ellipse">
    <div class="profile-icon">
      @if($user->profile_picture ?? auth()->user()->profile_picture)
        <img src="{{ asset('storage/' . ($user->profile_picture ?? auth()->user()->profile_picture)) }}" 
             alt="Profile Picture" 
             style="cursor:pointer;" 
             onclick="showProfilePictureModal()">
      @else
        <i class="fas fa-user" style="cursor:pointer;" onclick="showProfilePictureModal()"></i>
      @endif
    </div>
  </div>
  
  <div class="profile-name">
    {{ ($user->first_name ?? auth()->user()->first_name) }} {{ ($user->last_name ?? auth()->user()->last_name) }}
    @if((($user->resume_verification_status ?? auth()->user()->resume_verification_status) === 'verified'))
      <i class="fas fa-check-circle" title="Resume Verified" style="color: #648EB5; margin-left: 6px; font-size: 14px;"></i>
    @endif
  </div>
  
  <script>
  function showProfilePictureModal() {
    const oldModal = document.getElementById('profilePicModal');
    if (oldModal) oldModal.remove();
    const picUrl = @json(($user->profile_picture ?? auth()->user()->profile_picture) ? asset('storage/' . ($user->profile_picture ?? auth()->user()->profile_picture)) : null);
    const name = @json((($user->first_name ?? auth()->user()->first_name) . ' ' . ($user->last_name ?? auth()->user()->last_name)));
    const modal = document.createElement('div');
    modal.id = 'profilePicModal';
    modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10001; display:flex; align-items:center; justify-content:center;';
    modal.innerHTML = `
      <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 10px 40px rgba(0,0,0,0.3); display:flex; flex-direction:column; align-items:center; max-width:350px; width:90%; position:relative;">
        <button onclick="document.getElementById('profilePicModal').remove();" style="position:absolute; top:15px; right:15px; background:rgba(0,0,0,0.1); border:none; width:32px; height:32px; border-radius:50%; font-size:18px; cursor:pointer; color:#333;">&times;</button>
        <h3 style="margin-bottom:18px; color:#648EB5; font-size:20px; font-weight:600;">Your Profile</h3>
        ${picUrl ? `<img src='${picUrl}' alt='Profile Picture' style='width:120px; height:120px; object-fit:cover; border-radius:50%; border:4px solid #648EB5; margin-bottom:12px;'>` : `<div style='width:120px; height:120px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:48px; color:#aaa; margin-bottom:12px;'><i class='fas fa-user'></i></div>`}
        <div style="font-size:16px; color:#333; font-weight:500;">${name}</div>
        <button onclick="document.getElementById('profilePicModal').remove();" style="margin-top:22px; background:#6c757d; color:white; border:none; padding:8px 22px; border-radius:8px; cursor:pointer; font-size:14px;">Close</button>
      </div>
    `;
    document.body.appendChild(modal);
  }
  </script>
  
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
  
  @if(($user->user_type ?? auth()->user()->user_type) === 'job_seeker')
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
