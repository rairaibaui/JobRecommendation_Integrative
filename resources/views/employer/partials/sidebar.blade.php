{{-- 
  Unified Employer Sidebar Component
  Include this on every employer page: @include('employer.partials.sidebar')
  Automatically highlights the active page
--}}

<div class="sidebar">
  <div class="profile-ellipse">
    <div class="profile-icon">
      @if($user->profile_picture ?? auth()->user()->profile_picture)
        <img src="{{ asset('storage/' . ($user->profile_picture ?? auth()->user()->profile_picture)) }}" 
             alt="Profile Picture" 
             style="cursor:pointer;" 
             onclick="showEmpProfilePictureModal()">
      @else
        <i class="fa fa-building" style="cursor:pointer;" onclick="showEmpProfilePictureModal()"></i>
      @endif
    </div>
  </div>
  
  <div class="company-name" title="{{ ($user->company_name ?? auth()->user()->company_name) }}">
    <i class="fas fa-building"></i> 
    {{ ($user->company_name ?? auth()->user()->company_name) ?? 'Company Name' }}
    @php
      use App\Models\DocumentValidation;
      use Illuminate\Support\Facades\Auth;
      
      $currentUserId = Auth::id();
      $validation = $currentUserId
        ? DocumentValidation::where('user_id', $currentUserId)
          ->where('document_type', 'business_permit')
          ->orderByDesc('created_at')
          ->first()
        : null;
    @endphp
    @if($validation && $validation->validation_status === 'approved')
      <i class="fas fa-check-circle" title="Business Permit Verified" style="color: #648EB5; margin-left: 6px; font-size: 14px;"></i>
    @endif
  </div>
  
  <script>
  function showEmpProfilePictureModal() {
    const oldModal = document.getElementById('empProfilePicModal');
    if (oldModal) oldModal.remove();
    const picUrl = @json(($user->profile_picture ?? auth()->user()->profile_picture) ? asset('storage/' . ($user->profile_picture ?? auth()->user()->profile_picture)) : null);
    const name = @json(($user->company_name ?? auth()->user()->company_name) ?? 'Company');
    const modal = document.createElement('div');
    modal.id = 'empProfilePicModal';
    modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10001; display:flex; align-items:center; justify-content:center;';
    modal.innerHTML = `
      <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 10px 40px rgba(0,0,0,0.3); display:flex; flex-direction:column; align-items:center; max-width:350px; width:90%; position:relative;">
        <button onclick="document.getElementById('empProfilePicModal').remove();" style="position:absolute; top:15px; right:15px; background:rgba(0,0,0,0.1); border:none; width:32px; height:32px; border-radius:50%; font-size:18px; cursor:pointer; color:#333;">&times;</button>
        <h3 style="margin-bottom:18px; color:#648EB5; font-size:20px; font-weight:600;">Company Profile</h3>
        ${picUrl ? `<img src='${picUrl}' alt='Profile Picture' style='width:120px; height:120px; object-fit:cover; border-radius:50%; border:4px solid #648EB5; margin-bottom:12px;'>` : `<div style='width:120px; height:120px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:48px; color:#aaa; margin-bottom:12px;'><i class='fas fa-building'></i></div>`}
        <div style="font-size:16px; color:#333; font-weight:500;">${name}</div>
        <button onclick="document.getElementById('empProfilePicModal').remove();" style="margin-top:22px; background:#6c757d; color:white; border:none; padding:8px 22px; border-radius:8px; cursor:pointer; font-size:14px;">Close</button>
      </div>
    `;
    document.body.appendChild(modal);
  }
  </script>
  
  @php
    $currentRoute = Route::currentRouteName();
  @endphp
  
  <a href="{{ route('employer.dashboard') }}" 
     class="sidebar-btn {{ $currentRoute === 'employer.dashboard' ? 'active' : '' }}">
    <i class="fa fa-home sidebar-btn-icon"></i> Dashboard
  </a>
  
  <a href="{{ route('employer.jobs') }}" 
     class="sidebar-btn {{ Str::startsWith($currentRoute, 'employer.jobs') ? 'active' : '' }}">
    <i class="fa fa-briefcase sidebar-btn-icon"></i> Job Postings
  </a>
  
  <a href="{{ route('employer.applicants') }}" 
     class="sidebar-btn {{ $currentRoute === 'employer.applicants' ? 'active' : '' }}">
    <i class="fa fa-users sidebar-btn-icon"></i> Applicants
  </a>
  
  <a href="{{ route('employer.history') }}" 
     class="sidebar-btn {{ $currentRoute === 'employer.history' ? 'active' : '' }}">
    <i class="fa fa-history sidebar-btn-icon"></i> History
  </a>
  
  <a href="{{ route('employer.employees') }}" 
     class="sidebar-btn {{ $currentRoute === 'employer.employees' ? 'active' : '' }}">
    <i class="fa fa-user-check sidebar-btn-icon"></i> Employees
  </a>
  
  <a href="{{ route('employer.analytics') }}" 
     class="sidebar-btn {{ $currentRoute === 'employer.analytics' ? 'active' : '' }}">
    <i class="fa fa-chart-bar sidebar-btn-icon"></i> Analytics
  </a>
  
  <a href="{{ route('settings') }}" 
     class="sidebar-btn {{ $currentRoute === 'settings' ? 'active' : '' }}">
    <i class="fa fa-cog sidebar-btn-icon"></i> Settings
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
