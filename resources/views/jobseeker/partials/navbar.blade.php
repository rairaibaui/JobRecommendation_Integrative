{{-- 
  Unified Job Seeker Navbar Component
  Include this on every job seeker page: @include('jobseeker.partials.navbar')
  Pass $pageTitle variable to customize the navbar title
--}}

<div class="top-navbar">
  <div class="navbar-left">
    <span>{{ $pageTitle ?? 'JOB SEEKER PORTAL' }}</span>
  </div>
  <div style="display:flex; align-items:center; gap:16px;">
    @include('partials.notifications')
  </div>
</div>
