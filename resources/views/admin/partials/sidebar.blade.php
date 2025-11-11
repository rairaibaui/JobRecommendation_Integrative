{{--
  Admin Sidebar - Pixel Perfect Replica of Job Seeker Sidebar
  Same layout, spacing, fonts, icon sizes, colors, active states
  Include this on every admin page: @include('admin.partials.sidebar')
--}}

<style>
/* Admin Sidebar - Exact replica of Job Seeker sidebar */
.sidebar {
    width: 220px;
    background-color: #fff;
    color: #000;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 20px;
    border-right: 2px solid #648EB5;
}

.profile-ellipse {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(180deg, rgba(73, 118, 159, 0.44) 48%, rgba(78, 142, 162, 0.44) 86%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.profile-icon img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid #fff;
}

.profile-icon i {
    font-size: 30px;
    color: #fff;
}

.sidebar h3 {
    font-size: 18px;
    margin-bottom: 30px;
    text-align: center;
}

.sidebar a {
    text-decoration: none;
    color: #000;
    width: 80%;
    padding: 10px;
    border-radius: 8px;
    text-align: left;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    border: none;
    border-top: none;
    border-bottom: none;
    box-shadow: none;
}

.sidebar a.active {
    background-color: #648EB5;
    color: #fff;
    box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
}

.sidebar .logout {
    margin-top: auto;
    margin-bottom: 20px;
    width: 80%;
    padding: 10px;
    background-color: #648EB5;
    color: #fff;
    text-align: center;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    border-top: none;
    box-shadow: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
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

    <h3>System Admin</h3>

    <a href="{{ route('admin.dashboard') }}" class="{{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
        <i>🏠</i> Dashboard
    </a>
    <a href="{{ route('admin.analytics.index') }}" class="{{ $currentRoute === 'admin.analytics.index' ? 'active' : '' }}">
        <i>📊</i> Analytics
    </a>
    <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="{{ in_array($currentRoute, ['admin.verifications.unified', 'admin.verifications.index', 'admin.verifications.show', 'admin.verifications.resume-detail']) ? 'active' : '' }}">
        <i>🛡️</i> Verifications
    </a>
    <a href="{{ route('admin.users.index') }}" class="{{ $currentRoute === 'admin.users.index' ? 'active' : '' }}">
        <i>👥</i> Users
    </a>
    <a href="{{ route('admin.audit.index') }}" class="{{ $currentRoute === 'admin.audit.index' ? 'active' : '' }}">
        <i>📋</i> Audit Logs
    </a>

    <form method="POST" action="{{ route('logout') }}" onsubmit="return showLogoutModal(this);">
        @csrf
        <button type="submit" class="logout">
            <i>↪</i> Logout
        </button>
    </form>
</div>

@include('partials.logout-confirm')