{{--
  Unified Admin Navbar Component
  Include this on every admin page: @include('admin.partials.navbar')
  Matches the Job Seeker navbar design
--}}

<div class="top-navbar">
  <div class="navbar-left">
    <div class="hamburger">
      <i class="fas fa-bars"></i>
    </div>
    {{ $pageTitle ?? 'ADMIN PANEL' }}
  </div>
  
  <div class="navbar-right">
    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to logout?');">
      @csrf
      <button type="submit" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        Logout
      </button>
    </form>
  </div>
</div>