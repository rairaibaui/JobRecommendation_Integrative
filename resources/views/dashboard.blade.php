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
    padding: 0 20px;
    color: #FFF;
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 800;
    z-index: 1000;
  }

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

  /* Bookmark icon button */
  .bookmark-btn {
    background: transparent;
    border: 1px solid rgba(100,142,181,0.15);
    color: #648EB5;
    width: 44px;
    height: 44px;
    border-radius: 10px;
    font-size: 18px;
    padding: 6px;
  }

  .bookmark-btn i { transition: color 160ms ease, transform 160ms ease; }
  .bookmark-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.08); }
  .bookmark-btn i.fas { color: #FFD166; /* gold for bookmarked */ transform: scale(1.06); }
  .bookmark-btn i.far { color: #648EB5; }

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
      Job Portal - Mandaluyong
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
      <div class="recommendation-header">
        <h3>Top Job Recommendations</h3>
        <p>This is based on the skills that you have</p>
      </div>
      @foreach($jobs as $job)
      <div class="job-card" data-title="{{ $job['title'] }}" data-location="{{ $job['location'] ?? '' }}" data-type="{{ $job['type'] ?? '' }}" data-salary="{{ $job['salary'] ?? '' }}" data-description="{{ $job['description'] ?? '' }}" data-skills='@json($job['skills'] ?? [])'>
        <div class="job-title">{{ $job['title'] }}</div>
        <div class="job-preview">
          <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job['location'] ?? '' }}</div>
          <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job['type'] ?? '' }}</div>
          <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job['salary'] ?? '' }}</div>
        </div>
        
        <div class="job-details">
          <div class="job-description">
            <h4>Job Description</h4>
            {{ $job['description'] ?? '' }}
          </div>
          
          <div class="skills-section">
            <h4>Required Skills</h4>
            <div class="job-skills">
              @if(!empty($job['skills']))
                @foreach($job['skills'] as $skill)
                  <div class="skill">{{ $skill }}</div>
                @endforeach
              @endif
            </div>
          </div>
        </div>

        <div class="job-actions">
          <button class="view-details" onclick="toggleJobDetails(this)">
            <i class="fas fa-chevron-down"></i> View Details
          </button>
          <button class="bookmark-btn" onclick="toggleBookmark(this)" title="{{ in_array($job['title'], $bookmarkedTitles ?? []) ? 'Unbookmark this job' : 'Bookmark this job' }}" data-title="{{ $job['title'] }}">
            <i class="{{ in_array($job['title'], $bookmarkedTitles ?? []) ? 'fas' : 'far' }} fa-bookmark"></i>
          </button>
        </div>
      </div>
      @endforeach

    </div>
  </div>

  <!-- Include job details JavaScript -->
  <script src="{{ asset('js/job-details.js') }}"></script>

  <script>
  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  }

  function toggleBookmark(button) {
    const icon = button.querySelector('i');
    const jobCard = button.closest('.job-card');
    const title = button.dataset.title || jobCard.dataset.title;
    const job = {
      title: title,
      location: jobCard.dataset.location,
      type: jobCard.dataset.type,
      salary: jobCard.dataset.salary,
      description: jobCard.dataset.description,
      skills: JSON.parse(jobCard.dataset.skills || '[]')
    };

    const isBookmarking = icon.classList.contains('far');
    const url = isBookmarking ? "{{ route('bookmark.add') }}" : "{{ route('bookmark.remove') }}";

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ job: job })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      if (!ok) throw data;

      if (isBookmarking) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        button.title = 'Unbookmark this job';
        showMessage('Bookmarked — visible in Bookmarks page', 'success');
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        button.title = 'Bookmark this job';
        showMessage('Removed from bookmarks', 'info');
      }
    })
    .catch(err => {
      console.error(err);
      showMessage('Failed to update bookmark', 'error');
    });
  }

  function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = message;
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.padding = '10px 20px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.zIndex = '1000';
    messageDiv.style.color = 'white';
    switch(type) {
      case 'success': messageDiv.style.backgroundColor = '#4CAF50'; break;
      case 'info': messageDiv.style.backgroundColor = '#2196F3'; break;
      case 'error': messageDiv.style.backgroundColor = '#f44336'; break;
      default: messageDiv.style.backgroundColor = '#2196F3';
    }
    document.body.appendChild(messageDiv);
    setTimeout(() => messageDiv.remove(), 3000);
  }
  </script>

  </body>
  </html>

