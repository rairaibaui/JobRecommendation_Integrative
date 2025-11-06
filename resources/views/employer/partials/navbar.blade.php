{{-- 
  Unified Employer Navbar Component
  Include this on every employer page: @include('employer.partials.navbar')
  Pass $pageTitle variable to customize the navbar title
--}}

<div class="top-navbar">
  <div class="navbar-left">
    <span>{{ $pageTitle ?? 'EMPLOYER PORTAL' }}</span>
  </div>
  <div style="display:flex; align-items:center; gap:16px;">
    @include('partials.notifications')
  </div>
</div>
