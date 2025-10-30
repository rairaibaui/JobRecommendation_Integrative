<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard -                                                         Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
  * { box-sizing: border-box; margin:0; padding:0; }
  body {
    width: 100vw;
    height: 100vh;
    display: flex;
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
    padding: 88px 20px 20px 20px;
    gap: 20px;
  }

  /* Sidebar */
  .sidebar {
    position: fixed;
    left: 20px;
    top: 88px;
    width: 250px;
    height: calc(100vh - 108px);
    border-radius: 8px;
    background: #FFF;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .sidebar .profile-ellipse {
    align-self: center;
  }

  .profile-name {
    align-self: center;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #000;
    margin-bottom: 20px;
  }

  .sidebar .sidebar-btn {
    align-self: flex-start;
  }

  .profile-ellipse {
    width: 62px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-icon {
    width: 40px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .profile-icon i {
    font-size: 30px;
    color: #FFF;
  }

  .sidebar-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 39px;
    padding: 0 10px;
    border-radius: 8px;
    background: transparent;
    box-shadow: none;
    color: #000;
    font-size: 20px;
    font-weight: 400;
    cursor: pointer;
  }

  .sidebar-btn.active {
    background: #648EB5;
    box-shadow: 0 7px 4px rgba(0,0,0,0.25);
    color: #000;
    width: 100%;
  }

  .sidebar-btn-icon {
    margin-right: 10px;
  }

  /* Main content */
  .main {
    margin-left: 290px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .top-navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 68px;
    background: #2B4053;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 800;
    z-index: 1000;
  }

  .notification-bell {
    position: relative;
    cursor: pointer;
    padding: 10px;
    margin-right: 20px;
  }

  .notification-bell i {
    font-size: 24px;
    color: #FFF;
    transition: all 0.3s;
  }

  .notification-bell:hover i {
    transform: scale(1.1);
    color: #FFD700;
  }

  .notification-bell .badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ff4757;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Notification dropdown */
  .notif-wrapper { position: relative; }
  .notif-dropdown {
    position: absolute;
    top: 54px;
    right: 0;
    width: 360px;
    max-height: 420px;
    overflow-y: auto;
    background: #fff;
    color: #333;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(0,0,0,0.18);
    padding: 10px 0;
    z-index: 1100;
    font-size: 14px;
    line-height: 1.35;
  }
  .notif-header { padding: 10px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eee; font-weight:600; }
  .notif-list { list-style: none; margin: 0; padding: 0; }
  .notif-item { padding: 12px 16px; display:flex; gap:10px; border-bottom:1px solid #f3f3f3; }
  .notif-item.unread { background:#f7fbff; }
  .notif-item i { color:#648EB5; margin-top:3px; }
  .notif-item .meta { font-size:12px; color:#888; margin-top:4px; }
  .notif-empty { padding: 20px; text-align:center; color:#777; }
  .notif-actions { padding: 8px 12px; display:flex; justify-content:flex-end; }
  .notif-actions button { background:#648EB5; color:#fff; border:none; border-radius:8px; padding:8px 12px; cursor:pointer; font-size:12px; }

  .hamburger {
    margin-right: 20px;
    color: #FFF;
  }

  .welcome {
    font-family: 'Poppins', sans-serif;
    font-size: 32px;
    font-weight: 600;
    color: #FFF;
    margin-bottom: 10px;
  }

  /* Recommended & Bookmarked Jobs */
  .cards {
    display: flex;
    gap: 20px;
  }

  .card-medium {
    width: 100%;
    height: 138px;
    background: #FFF;
    border-radius: 8px;
    box-shadow: 0 8px 4px 0 #908D8D;
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    position: relative;
  }

  .card-medium .card-icon {
    width: 69px;
    height: 54px;
    border-radius: 8px;
    background: #648EB5;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card-medium .card-icon i {
    font-size: 34px;
    color: #FFF;
  }

  .card-medium .card-title {
    font-family: 'Roboto', sans-serif;
    font-size: 18px;
    font-weight: 400;
    color: #000;
  }

  .card-medium .view-all {
    position: absolute;
    right: 20px;
    bottom: 20px;
    font-family: 'Roboto', sans-serif;
    font-size: 18px;
    color: #648EB5;
    text-decoration: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .card-medium .view-all::after {
    content: '\f105';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    font-size: 16px;
  }

  /* Top Job Recommendations */
  .card-large {
    width: 100%;
    flex: 1;
    background: #FFF;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .recommendation-header {
    text-align: left;
    margin-bottom: 10px;
  }

  .recommendation-header h3 {
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 600;
    color: #000;
    margin: 0 0 5px 0;
  }

  .recommendation-header p {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #666;
    margin: 0;
  }

  .job-card {
    width: 100%;
    height: auto;
    border-radius: 8px;
    border: 1px solid #BBB1B1;
    background: #FFF;
    box-shadow: 0 10px 4px rgba(0,0,0,0.25);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: all 0.3s ease;
  }

  .job-preview {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }

  .job-details {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    opacity: 0;
    margin-top: 0;
    padding: 0 10px;
  }

  .job-details.expanded {
    max-height: 1000px;
    opacity: 1;
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 20px;
  }

  .job-details h4 {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    font-weight: 500;
    color: #333;
    margin-bottom: 10px;
  }

  .skills-section {
    margin-top: 20px;
  }

  .job-title {
    font-family: 'Roboto', sans-serif;
    font-size: 20px;
    font-weight: 500;
    color: #000;
  }

  .job-location, .job-type, .job-salary {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .job-location i, .job-type i, .job-salary i {
    color: #648EB5;
    width: 16px;
  }

  .job-description {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #666;
    margin-top: 8px;
    line-height: 1.4;
  }

  .skills-header {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #333;
    margin-top: 8px;
    font-weight: 600;
  }

  .job-skills {
    display: flex;
    gap: 10px;
    margin-top: 5px;
  }

  .skill {
    background: #648EB5;
    color: #FFF;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 14px;
  }

  .job-actions {
    display: flex;
    gap: 20px;
    margin-top: auto;
  }
  .view-details, .bookmark-btn {
    padding: 10px 15px;
    border-radius: 8px;
    border: none;
    font-size: 16px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 180ms ease;
  }

  .view-details {
    background: #FFF;
    border: 1px solid #648EB5;
    color: #648EB5;
  }

  /* Job Card Styles */
  .job-card {
    width: 100%;
    height: auto;
    border-radius: 8px;
    border: 1px solid #BBB1B1;
    background: #FFF;
    box-shadow: 0 10px 4px rgba(0,0,0,0.25);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: all 0.3s ease;
  }

  .job-title {
    font-family: 'Roboto', sans-serif;
    font-size: 20px;
    font-weight: 500;
    color: #000;
  }

  .job-preview {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }

  .job-location, .job-type, .job-salary {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .job-location i, .job-type i, .job-salary i {
    color: #648EB5;
    width: 16px;
  }

  .job-details {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    opacity: 0;
    margin-top: 0;
    padding: 0 10px;
  }

  .job-details.expanded {
    max-height: 1000px;
    opacity: 1;
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 20px;
  }

  .job-description {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #666;
    line-height: 1.4;
  }

  .skills-section {
    margin-top: 20px;
  }

  .job-skills {
    display: flex;
    gap: 10px;
    margin-top: 5px;
  }

  .skill {
    background: #648EB5;
    color: #FFF;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 14px;
  }

  .job-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
  }

  .view-details, .apply-btn, .bookmark-btn {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .view-details {
    background: #f8f9fa;
    color: #666;
    border: 1px solid #ddd;
  }

  .apply-btn {
    background: #648EB5;
    color: white;
    flex: 1;
  }

  .bookmark-btn {
    background: white;
    border: 1px solid #648EB5;
    color: #648EB5;
    width: 40px;
    height: 40px;
    padding: 0;
    justify-content: center;
  }

  .job-actions button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  /* Make job-actions align right when space allows */
  .job-actions { justify-self: end; align-items: center; }

</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="profile-ellipse">
    <div class="profile-icon">
      @if(Auth::user()->profile_picture)
        <!-- Kung may profile picture, ipakita ito -->
        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture"
          style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
      @else
        <!-- Default icon kung walang picture -->
        <i class="fas fa-user"></i>
      @endif
    </div>
  </div>

  <!-- Palitan ang hardcoded na name ng dynamic user name -->
  <div class="profile-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>

  <a href="{{ route('dashboard') }}" class="sidebar-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}"
    style="text-decoration: none;">
    <i class="fas fa-home sidebar-btn-icon"></i>
    Dashboard
  </a>
  <a href="{{ route('recommendation') }}" class="sidebar-btn {{ request()->routeIs('recommendation') ? 'active' : '' }}"
    style="text-decoration: none;">
    <i class="fas fa-suitcase sidebar-btn-icon"></i>
    Recommendation
  </a>
  <a href="{{ route('my-applications') }}" class="sidebar-btn {{ request()->routeIs('my-applications') ? 'active' : '' }}"
    style="text-decoration: none;">
    <i class="fas fa-file-alt sidebar-btn-icon"></i>
    My Applications
  </a>
  <a href="{{ route('bookmarks') }}" class="sidebar-btn {{ request()->routeIs('bookmarks') ? 'active' : '' }}"
    style="text-decoration: none;">
    <i class="fas fa-bookmark sidebar-btn-icon"></i>
    Bookmarks
  </a>
  <a href="{{ route('settings') }}" class="sidebar-btn {{ request()->routeIs('settings') ? 'active' : '' }}"
    style="text-decoration: none;">
    <i class="fas fa-cog sidebar-btn-icon"></i>
    Settings
  </a>
  <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
    @csrf
    <button type="submit" class="sidebar-btn"
      style="border: none; background: #648EB5; color: #FFF; font-size: 20px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 10px; height: 39px; display: flex; align-items: center; justify-content: center; gap: 10px;">
      <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
      Logout
    </button>
  </form>
</div>


  <!-- Main Content -->
  <div class="main">
    <div class="top-navbar">
      <div style="display: flex; align-items: center;">
        Job Portal - Mandaluyong
      </div>
      <div style="display: flex; align-items: center;" class="notif-wrapper">
        <div class="notification-bell" onclick="toggleNotifDropdown(event)">
          <i class="fas fa-bell"></i>
          @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
          @if($unreadCount > 0)
            <span class="badge" id="notifCount">{{ $unreadCount }}</span>
          @endif
        </div>
        <div id="notifDropdown" class="notif-dropdown" style="display:none;" data-loaded="0">
          <div class="notif-header">
            <span>Notifications</span>
            <button onclick="markAllNotificationsRead(event)" style="background:#eee;color:#333;border:1px solid #ddd;border-radius:6px;padding:6px 10px;font-size:12px;cursor:pointer;">Mark all as read</button>
          </div>
          <ul class="notif-list" id="notifList">
            <li class="notif-empty">Loading...</li>
          </ul>
          <div class="notif-actions">
            <button onclick="refreshNotifications(event)" style="background:#4E8EA2">Refresh</button>
          </div>
        </div>
      </div>
    </div>

    <div class="welcome">Welcome!</div>

    <!-- Recommended & Bookmarked Jobs -->
    <div class="cards">
      <div class="card-medium">
        <div class="card-icon">
          <i class="fas fa-star"></i>
        </div>
        <div class="card-title">Recommended Jobs</div>
  <a href="{{ route('recommendation') }}" class="view-all">View All Recommendations</a>
      </div>

      <div class="card-medium">
        <div class="card-icon">
          <i class="fas fa-bookmark"></i>
        </div>
        <div class="card-title">Bookmarked Jobs</div>
  <a href="{{ route('bookmarks') }}" class="view-all">View All Bookmarks</a>
      </div>
    </div>

    <!-- Top Job Recommendations -->
    <div class="card-large">
      <div class="recommendation-header" style="background: linear-gradient(180deg, #648EB5 0%, #334A5E 100%); color: #fff; padding: 20px; border-radius: 8px 8px 0 0;">
        <h3>Top Job Recommendations</h3>
        <p>This is based on the skills that you have</p>
      </div>
      <div style="padding: 20px;">
        <p style="font-family: 'Poppins', sans-serif; font-size: 18px; color: #333; margin-bottom: 20px;">
          Showing {{ count($jobs) }} recommended {{ Str::plural('job', count($jobs)) }}
        </p>
        @foreach($jobs as $job)
        <div class="job-card" 
             data-job-id="{{ $job['id'] ?? '' }}"
             data-title="{{ $job['title'] }}" 
             data-location="{{ $job['location'] ?? '' }}" 
             data-type="{{ $job['type'] ?? '' }}" 
             data-salary="{{ $job['salary'] ?? '' }}" 
             data-description="{{ $job['description'] ?? '' }}" 
             data-skills='@json($job['skills'] ?? [])'>
            <div class="job-title">{{ $job['title'] }}</div>

            <div class="job-preview">
                <div class="job-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $job['location'] ?? 'N/A' }}</span>
                </div>
                <div class="job-type">
                    <i class="fas fa-briefcase"></i>
                    <span>{{ $job['type'] ?? 'Full-time' }}</span>
                </div>
                <div class="job-salary">
                    <i class="fas fa-money-bill"></i>
                    <span>{{ $job['salary'] ?? 'Negotiable' }}</span>
                </div>
            </div>
            
            <div class="job-details">
                <!-- Company & Employer Info Section -->
                <div class="employer-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #648EB5;">
                    <h4 style="margin: 0 0 10px 0; color: #648EB5; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-building"></i> Company & Contact Information
                    </h4>
                    <div style="display: grid; gap: 8px; font-size: 14px;">
                        @if(!empty($job['company']))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-briefcase" style="color: #648EB5; width: 16px;"></i>
                                <strong>Company:</strong> {{ $job['company'] }}
                            </div>
                        @endif
                        @if(!empty($job['employer_name']))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-user-tie" style="color: #648EB5; width: 16px;"></i>
                                <strong>Contact Person:</strong> {{ $job['employer_name'] }}
                            </div>
                        @endif
                        @if(!empty($job['employer_email']))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-envelope" style="color: #648EB5; width: 16px;"></i>
                                <strong>Email:</strong> <a href="mailto:{{ $job['employer_email'] }}" style="color: #648EB5; text-decoration: none;">{{ $job['employer_email'] }}</a>
                            </div>
                        @endif
                        @if(!empty($job['employer_phone']))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-phone" style="color: #648EB5; width: 16px;"></i>
                                <strong>Phone:</strong> <a href="tel:{{ $job['employer_phone'] }}" style="color: #648EB5; text-decoration: none;">{{ $job['employer_phone'] }}</a>
                            </div>
                        @endif
                        @if(!empty($job['posted_date']))
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar" style="color: #648EB5; width: 16px;"></i>
                                <strong>Posted:</strong> {{ $job['posted_date'] }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="job-description">
                    {{ $job['description'] ?? '' }}
                </div>
                <div class="skills-section">
                    <div class="job-skills">
                        @if(!empty($job['skills']))
                            @foreach($job['skills'] as $skill)
                                <span class="skill">{{ $skill }}</span>
                            @endforeach
                        @else
                            <span class="skill">No specific skills listed</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="job-actions">
                <button class="view-details" onclick="toggleDetails(this)" data-job-id="{{ $job['id'] }}">
                    <i class="fas fa-chevron-down"></i>
                    View Details
                </button>
                <button class="apply-btn" onclick="openApplyModal(this)" title="Apply using your profile">
                    <i class="fas fa-paper-plane"></i>
                    Apply
                </button>
        <button class="bookmark-btn" data-job='@json($job)' onclick="toggleBookmark(this)">
          @php $isBookmarked = isset($bookmarkedTitles) && in_array($job['title'], $bookmarkedTitles); @endphp
          <i class="{{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
        </button>
            </div>
        </div>
        @endforeach
        </div>
      </div>
    </div>

    </div>
  </div>

  <!-- Include job details JavaScript -->
  <script src="{{ asset('js/job-details.js') }}"></script>

  <!-- Apply Modal -->
  <div id="applyOverlay" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="apply-modal-container" style="background:white; border-radius:16px; width:90%; max-width:800px; max-height:85vh; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalSlideIn 0.3s ease;">
      
      <!-- Modal Header -->
      <div style="background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); padding:30px; color:white; position:relative;">
        <button onclick="closeApplyModal()" style="position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.2); border:none; width:36px; height:36px; border-radius:50%; font-size:20px; cursor:pointer; color:white; display:flex; align-items:center; justify-content:center; transition:all 0.2s;">&times;</button>
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:10px;">
          <div style="background:rgba(255,255,255,0.2); width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-briefcase" style="font-size:24px;"></i>
          </div>
          <div>
            <h2 id="applyJobTitle" style="margin:0; font-size:24px; font-weight:600;">Apply for Position</h2>
            <p style="margin:5px 0 0 0; opacity:0.9; font-size:14px;">Submit your application with confidence</p>
          </div>
        </div>
      </div>

      <!-- Modal Body -->
      <div id="resumePreview" style="padding:30px; max-height:calc(85vh - 180px); overflow-y:auto;">
        <div style="display:flex; align-items:center; justify-content:center; padding:40px;">
          <div class="loading-spinner" style="width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #648EB5; border-radius:50%; animation:spin 1s linear infinite;"></div>
          <p style="margin-left:15px; color:#666; font-style:italic;">Loading your profile...</p>
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="padding:20px 30px; background:#f8f9fa; border-top:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
        <div style="color:#666; font-size:14px;">
          <i class="fas fa-shield-alt" style="color:#648EB5; margin-right:5px;"></i>
          Your data is secure
        </div>
        <div style="display:flex; gap:12px;">
          <button onclick="closeApplyModal()" style="padding:10px 20px; border:1px solid #ddd; border-radius:8px; background:white; cursor:pointer; font-size:14px; font-weight:500; color:#666; transition:all 0.2s;">
            Cancel
          </button>
          <button id="confirmApplyBtn" style="padding:10px 24px; border:none; border-radius:8px; background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); color:white; cursor:pointer; font-size:14px; font-weight:600; transition:all 0.2s; box-shadow:0 4px 12px rgba(100,142,181,0.3); display:flex; align-items:center; gap:8px; white-space:nowrap;">
            <i class="fas fa-paper-plane"></i>
            <span>Submit Application</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <style>
    @keyframes modalSlideIn {
      from { transform:translateY(-30px); opacity:0; }
      to { transform:translateY(0); opacity:1; }
    }
    
    @keyframes spin {
      0% { transform:rotate(0deg); }
      100% { transform:rotate(360deg); }
    }

    .apply-modal-container button:hover {
      transform:translateY(-2px);
      box-shadow:0 6px 16px rgba(0,0,0,0.15);
    }

    #resumePreview::-webkit-scrollbar {
      width:8px;
    }

    #resumePreview::-webkit-scrollbar-track {
      background:#f1f1f1;
      border-radius:4px;
    }

    #resumePreview::-webkit-scrollbar-thumb {
      background:#648EB5;
      border-radius:4px;
    }

    #resumePreview::-webkit-scrollbar-thumb:hover {
      background:#4E8EA2;
    }

    .job-info-card {
      background:#f8f9fa;
      border-radius:12px;
      padding:20px;
      margin-bottom:20px;
      border-left:4px solid #648EB5;
    }

    .profile-section {
      background:white;
      border-radius:12px;
      padding:20px;
      margin-bottom:15px;
      border:1px solid #e0e0e0;
      transition:all 0.2s;
    }

    .profile-section:hover {
      box-shadow:0 4px 12px rgba(0,0,0,0.08);
      transform:translateY(-2px);
    }

    .profile-section h4 {
      color:#648EB5;
      font-size:16px;
      margin:0 0 12px 0;
      font-weight:600;
      display:flex;
      align-items:center;
      gap:8px;
    }

    .profile-section h4 i {
      font-size:18px;
    }

    .info-row {
      display:flex;
      align-items:flex-start;
      margin-bottom:8px;
      font-size:14px;
      color:#333;
    }

    .info-label {
      font-weight:600;
      min-width:120px;
      color:#666;
    }

    .info-value {
      flex:1;
      color:#333;
    }

    .experience-item, .education-item {
      background:#f8f9fa;
      padding:12px;
      border-radius:8px;
      margin-bottom:10px;
      border-left:3px solid #648EB5;
    }

    .experience-item strong, .education-item strong {
      color:#648EB5;
      font-size:15px;
    }

    .date-range {
      font-size:13px;
      color:#666;
      font-style:italic;
      margin-top:4px;
    }
  </style>

  <script>
    function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

    let currentJobData = null;
    let currentResumeSnapshot = null;

    function openApplyModal(button) {
      const card = button.closest('.job-card');
      currentJobData = {
        id: card.dataset.jobId || null,
        title: card.dataset.title || '',
        location: card.dataset.location || '',
        type: card.dataset.type || '',
        salary: card.dataset.salary || '',
        description: card.dataset.description || '',
        skills: card.dataset.skills ? JSON.parse(card.dataset.skills) : []
      };

      document.getElementById('applyJobTitle').textContent = currentJobData.title || 'Job Position';
      document.getElementById('resumePreview').innerHTML = '<div style="display:flex; align-items:center; justify-content:center; padding:40px;"><div class="loading-spinner" style="width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #648EB5; border-radius:50%; animation:spin 1s linear infinite;"></div><p style="margin-left:15px; color:#666; font-style:italic;">Loading your profile...</p></div>';
      document.getElementById('applyOverlay').style.display = 'flex';
      document.body.style.overflow = 'hidden';

      // Fetch profile resume snapshot
      fetch("{{ route('profile.resume') }}", { headers: { 'X-CSRF-TOKEN': getCsrfToken() }})
        .then(r => r.json())
        .then(profile => {
          currentResumeSnapshot = profile;
          const html = [];
          
          // Job Info Card
          html.push('<div class="job-info-card">');
          html.push('<h4 style="margin:0 0 10px 0; color:#648EB5; font-weight:600;"><i class="fas fa-briefcase"></i> Job Details</h4>');
          html.push(`<div class="info-row"><span class="info-label">Position:</span><span class="info-value">${currentJobData.title}</span></div>`);
          html.push(`<div class="info-row"><span class="info-label">Location:</span><span class="info-value">${currentJobData.location || 'Not specified'}</span></div>`);
          html.push(`<div class="info-row"><span class="info-label">Type:</span><span class="info-value">${currentJobData.type || 'Full-time'}</span></div>`);
          html.push(`<div class="info-row"><span class="info-label">Salary:</span><span class="info-value">${currentJobData.salary || 'Negotiable'}</span></div>`);
          html.push('</div>');

          // Personal Info Section
          html.push('<div class="profile-section">');
          html.push('<h4><i class="fas fa-user"></i> Personal Information</h4>');
          html.push(`<div class="info-row"><span class="info-label">Name:</span><span class="info-value">${profile.first_name} ${profile.last_name}</span></div>`);
          html.push(`<div class="info-row"><span class="info-label">Email:</span><span class="info-value">${profile.email}</span></div>`);
          if (profile.phone_number) html.push(`<div class="info-row"><span class="info-label">Phone:</span><span class="info-value">${profile.phone_number}</span></div>`);
          if (profile.birthday) {
            const birthday = new Date(profile.birthday).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            html.push(`<div class="info-row"><span class="info-label">Birthday:</span><span class="info-value">${birthday}</span></div>`);
          }
          if (profile.location) html.push(`<div class="info-row"><span class="info-label">Location:</span><span class="info-value">${profile.location}</span></div>`);
          html.push('</div>');

          // Professional Summary
          if (profile.summary) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-file-alt"></i> Professional Summary</h4>');
            html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.summary}</p>`);
            html.push('</div>');
          }

          // Education
          if (profile.education && profile.education.length) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-graduation-cap"></i> Education</h4>');
            profile.education.forEach(e => {
              html.push('<div class="education-item">');
              html.push(`<strong>${e.degree || 'Degree'}</strong>`);
              html.push(`<div style="color:#666; margin-top:4px;">${e.school || ''} ${e.year ? '• Class of ' + e.year : ''}</div>`);
              html.push('</div>');
            });
            html.push('</div>');
          }

          // Work Experience
          if (profile.experiences && profile.experiences.length) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-briefcase"></i> Work Experience</h4>');
            profile.experiences.forEach(ex => {
              html.push('<div class="experience-item">');
              html.push(`<strong>${ex.position || 'Position'}</strong>`);
              html.push(`<div style="color:#666; margin-top:2px;">${ex.company || 'Company'}</div>`);
              if (ex.start_date || ex.end_date) {
                html.push(`<div class="date-range">${ex.start_date || 'Start'} - ${ex.end_date || 'Present'}</div>`);
              }
              if (ex.responsibilities) {
                html.push(`<div style="margin-top:8px; color:#555; font-size:13px; line-height:1.5;">${ex.responsibilities}</div>`);
              }
              html.push('</div>');
            });
            html.push('</div>');
          }

          // Skills
          if (profile.skills) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-code"></i> Skills</h4>');
            html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.skills}</p>`);
            html.push('</div>');
          }

          // Languages
          if (profile.languages) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-language"></i> Languages</h4>');
            html.push(`<p style="color:#333; line-height:1.6; margin:0;">${profile.languages}</p>`);
            html.push('</div>');
          }

          // Portfolio
          if (profile.portfolio_links) {
            html.push('<div class="profile-section">');
            html.push('<h4><i class="fas fa-link"></i> Portfolio & Links</h4>');
            html.push(`<p style="color:#648EB5; line-height:1.6; margin:0; word-break:break-all;">${profile.portfolio_links}</p>`);
            html.push('</div>');
          }

          document.getElementById('resumePreview').innerHTML = html.join('\n');
        })
        .catch(() => { 
          document.getElementById('resumePreview').innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-exclamation-circle" style="font-size:48px; color:#f44336; margin-bottom:15px;"></i><p style="color:#c00; font-size:16px; margin:0;">Failed to load profile.</p><p style="color:#666; font-size:14px; margin-top:8px;">Please update your profile in Settings before applying.</p></div>'; 
        });
    }

    function closeApplyModal() {
      document.getElementById('applyOverlay').style.display = 'none';
      document.body.style.overflow = 'auto';
      currentJobData = null;
      currentResumeSnapshot = null;
    }

    document.getElementById('confirmApplyBtn')?.addEventListener('click', function(){
      if (!currentJobData) {
        showMessage('No job selected', 'error');
        return;
      }
      
      const submitBtn = this;
      const originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';
      
      const payload = {
        job_title: currentJobData.title,
        job_posting_id: currentJobData.id || null,
        job_data: currentJobData,
        resume_snapshot: currentResumeSnapshot || {}
      };

      console.log('Submitting application:', payload);

      fetch("{{ route('job.apply') }}", {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json', 
          'X-CSRF-TOKEN': getCsrfToken() 
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json().then(data => ({ ok: response.ok, data })))
      .then(({ ok, data }) => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        
        if (ok && data.success) {
          console.log('Application submitted successfully:', data);
          closeApplyModal();
          showMessage(data.message || 'Application submitted successfully!', 'success');
        } else {
          console.error('Application failed:', data);
          showMessage(data.message || 'Failed to submit application', 'error');
        }
      })
      .catch(error => {
        console.error('Error submitting application:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        showMessage('Network error. Please try again.', 'error');
      });
    });

    function showMessage(message, type) {
      const messageDiv = document.createElement('div');
      messageDiv.textContent = message;
      messageDiv.style.position = 'fixed';
      messageDiv.style.top = '20px';
      messageDiv.style.right = '20px';
      messageDiv.style.padding = '12px 24px';
      messageDiv.style.borderRadius = '4px';
      messageDiv.style.zIndex = '10000';
      messageDiv.style.color = 'white';
      messageDiv.style.transform = 'translateY(-20px)';
      messageDiv.style.opacity = '0';
      messageDiv.style.transition = 'all 0.3s ease';
      messageDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
      switch(type) { 
        case 'success': messageDiv.style.backgroundColor = '#4CAF50'; break; 
        case 'info': messageDiv.style.backgroundColor = '#2196F3'; break; 
        case 'error': messageDiv.style.backgroundColor = '#f44336'; break; 
        default: messageDiv.style.backgroundColor = '#2196F3'; 
      }
      document.body.appendChild(messageDiv);
      setTimeout(() => { messageDiv.style.transform = 'translateY(0)'; messageDiv.style.opacity = '1'; }, 10);
      setTimeout(() => { messageDiv.style.transform = 'translateY(-20px)'; messageDiv.style.opacity = '0'; setTimeout(() => messageDiv.remove(), 300); }, 2700);
    }

    // Load expanded state from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
      const expandedJobs = JSON.parse(localStorage.getItem('expandedDashboardJobs') || '[]');
      
      document.querySelectorAll('.job-card').forEach((card, index) => {
        if (expandedJobs.includes(index)) {
          const details = card.querySelector('.job-details');
          const button = card.querySelector('.btn-details');
          const icon = button ? button.querySelector('i') : null;
          
          if (details) {
            details.classList.add('expanded');
            if (icon) {
              icon.classList.remove('fa-chevron-down');
              icon.classList.add('fa-chevron-up');
            }
          }
        }
      });
    });

    function toggleDetails(button) {
        const jobCard = button.closest('.job-card');
        const details = jobCard.querySelector('.job-details');
        const icon = button.querySelector('i');
        
        if (details.classList.contains('expanded')) {
            details.classList.remove('expanded');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        } else {
            details.classList.add('expanded');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
        
        // Save expanded state to localStorage
        const cards = Array.from(document.querySelectorAll('.job-card'));
        const expandedJobs = [];
        cards.forEach((card, index) => {
          const cardDetails = card.querySelector('.job-details');
          if (cardDetails && cardDetails.classList.contains('expanded')) {
            expandedJobs.push(index);
          }
        });
        localStorage.setItem('expandedDashboardJobs', JSON.stringify(expandedJobs));
    }

  function toggleBookmark(button) {
    // Read full job data from data-job attribute (JSON)
    const job = JSON.parse(button.getAttribute('data-job') || '{}');
    const icon = button.querySelector('i');
    const isBookmarked = icon.classList.contains('fas');
    const url = isBookmarked ? "{{ route('bookmark.remove') }}" : "{{ route('bookmark.add') }}";

    // disable button while request is in-flight
    button.disabled = true;

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ job: job })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
      if (!ok) {
        showMessage(data.message || 'Failed to update bookmark', 'error');
        return;
      }

      if (isBookmarked) {
        // It was bookmarked, now removed
        icon.classList.remove('fas');
        icon.classList.add('far');
        showMessage('Removed from bookmarks', 'info');
      } else {
        // It was not bookmarked, now added
        icon.classList.remove('far');
        icon.classList.add('fas');
        showMessage('Bookmarked — visible in Bookmarks page', 'success');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showMessage('Failed to update bookmark', 'error');
    })
    .finally(() => { button.disabled = false; });
  }

  // Notifications logic
  function toggleNotifDropdown(e){
    e.stopPropagation();
    const dd = document.getElementById('notifDropdown');
    const visible = dd.style.display === 'block';
    if (!visible && dd.dataset.loaded !== '1') {
      loadNotifications();
    }
    dd.style.display = visible ? 'none' : 'block';
  }

  document.addEventListener('click', function(e){
    const dd = document.getElementById('notifDropdown');
    if(!dd) return;
    if (dd.style.display === 'block' && !dd.contains(e.target)) {
      dd.style.display = 'none';
    }
  });

  function loadNotifications(){
    const list = document.getElementById('notifList');
    list.innerHTML = '<li class="notif-empty">Loading...</li>';
    fetch("{{ route('notifications.list') }}")
      .then(r => r.json())
      .then(({success, unread, notifications}) => {
        if(!success){ list.innerHTML = '<li class="notif-empty">Failed to load</li>'; return; }
        // update badge
        const badge = document.getElementById('notifCount');
        if (badge) badge.textContent = unread; else if (unread > 0) {
          const bell = document.querySelector('.notification-bell');
          const span = document.createElement('span');
          span.className = 'badge'; span.id = 'notifCount'; span.textContent = unread;
          bell.appendChild(span);
        }
        if(!notifications.length){ list.innerHTML = '<li class="notif-empty">No notifications yet</li>'; return; }
        list.innerHTML = notifications.map(n => renderNotifItem(n)).join('');
        document.getElementById('notifDropdown').dataset.loaded = '1';
      })
      .catch(() => list.innerHTML = '<li class="notif-empty">Network error</li>');
  }

  function renderNotifItem(n){
    const icon = n.type === 'application_status_changed' ? 'fa-clipboard-check' : 'fa-paper-plane';
    const isUnread = n.read ? '' : 'unread';
    const when = new Date(n.created_at).toLocaleString();
    return `<li class="notif-item ${isUnread}">
      <i class="fas ${icon}"></i>
      <div>
        <div style=\"font-weight:600; color:#333;\">${escapeHtml(n.title || 'Notification')}</div>
        <div style=\"color:#555; font-size:13px;\">${escapeHtml(n.message || '')}</div>
        <div class="meta">${when}</div>
      </div>
    </li>`;
  }

  function escapeHtml(str){
    return String(str).replace(/[&<>"]+/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]));
  }

  function markAllNotificationsRead(e){
    e.stopPropagation();
    fetch("{{ route('notifications.markAllRead') }}", {
      method:'POST',
      headers:{ 'X-CSRF-TOKEN': getCsrfToken() }
    }).then(r=>r.json()).then(({success})=>{
      if(success){
        const items = document.querySelectorAll('.notif-item');
        items.forEach(li => li.classList.remove('unread'));
        const badge = document.getElementById('notifCount');
        if (badge) badge.remove();
      }
    });
  }

  function refreshNotifications(e){ e.stopPropagation(); loadNotifications(); }
  </script>

  </body>
  </html>

