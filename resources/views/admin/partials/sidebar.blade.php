{{--
  Admin Sidebar - Pixel Perfect Replica of Job Seeker Sidebar
  Same layout, spacing, fonts, icon sizes, colors, active states
  Include this on every admin page: @include('admin.partials.sidebar')
--}}

<style>
/* Admin Sidebar - Exact replica of Job Seeker sidebar spacing and sizing */
.sidebar {
    width: 250px;
    background-color: #fff;
    color: #000;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    gap: 6px;
    border-right: 2px solid #648EB5;
}

/* Navigation menu container with consistent spacing */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
    align-items: stretch;
}

.profile-ellipse {
    width: 62px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(180deg, rgba(73, 118, 159, 0.44) 48.29%, rgba(78, 142, 162, 0.44) 86%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.profile-icon {
    width: 62px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border-radius: 50%;
}

.profile-icon img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: none;
    outline: none;
    box-shadow: none;
    display: block;
}

.profile-icon i {
    font-size: 30px;
    color: #fff;
}

.profile-name {
    align-self: center;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #000;
    margin-bottom: 8px;
}

/* Modern Sidebar Button Styles (matching Job Seeker sidebar) */
.sidebar-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    height: 44px;
    padding: 0 14px;
    border-radius: 10px;
    background: transparent;
    box-shadow: none;
    color: #334A5E;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    width: 100%;
}

.sidebar-btn::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 3px;
    background: #648EB5;
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.sidebar-btn:hover {
    background: linear-gradient(90deg, #e8f4fd 0%, #f0f7fc 100%);
    color: #2B4053;
    transform: translateX(4px);
}

.sidebar-btn:hover::before {
    transform: scaleY(1);
}

.sidebar-btn.active {
    background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
    box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
    color: #FFF;
    font-weight: 600;
}

.sidebar-btn.active::before {
    display: none;
}

.sidebar-btn.active:hover {
    transform: translateX(0);
    box-shadow: 0 6px 16px rgba(100, 142, 181, 0.4);
}

.sidebar-btn-icon {
    font-size: 18px;
    min-width: 20px;
    text-align: center;
    transition: transform 0.3s ease;
}

.sidebar-btn:hover .sidebar-btn-icon {
    transform: scale(1.1);
}

.sidebar-btn.active .sidebar-btn-icon {
    transform: scale(1.05);
}
</style>

<div class="sidebar">
    @php
        $viewUser = isset($user) ? $user : auth()->user();
        $profilePic = optional($viewUser)->profile_picture;
        $firstName = optional($viewUser)->first_name;
        $lastName = optional($viewUser)->last_name;
        $currentRoute = Route::currentRouteName();
    @endphp

    <div class="profile-ellipse">
        <div class="profile-icon">
            @if($profilePic)
                <img src="{{ asset('storage/' . $profilePic) }}" alt="Profile Picture">
            @else
                <i class="fas fa-user"></i>
            @endif
        </div>
    </div>

    <div class="profile-name">System Admin</div>

    <div class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-btn {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
            <i class="fas fa-home sidebar-btn-icon"></i> Dashboard
        </a>
        
        <a href="{{ route('admin.analytics.index') }}"
           class="sidebar-btn {{ $currentRoute === 'admin.analytics.index' ? 'active' : '' }}">
            <i class="fas fa-chart-bar sidebar-btn-icon"></i> Analytics
        </a>
        
        <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}"
           class="sidebar-btn {{ in_array($currentRoute, ['admin.verifications.unified', 'admin.verifications.index', 'admin.verifications.show', 'admin.verifications.resume-detail']) ? 'active' : '' }}">
            <i class="fas fa-check-circle sidebar-btn-icon"></i> Verifications
        </a>
        
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-btn {{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">
            <i class="fas fa-users sidebar-btn-icon"></i> Users
        </a>
        
        <a href="{{ route('admin.audit.index') }}"
           class="sidebar-btn {{ $currentRoute === 'admin.audit.index' ? 'active' : '' }}">
            <i class="fas fa-clipboard-list sidebar-btn-icon"></i> Audit Logs
        </a>
    </div>

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