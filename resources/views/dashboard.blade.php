<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

  .view-details, .save-job {
    padding: 10px 15px;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    cursor: pointer;
  }

  .view-details {
    background: #FFF;
    border: 1px solid #648EB5;
    color: #648EB5;
  }

  .save-job {
    background: #648EB5;
    color: #FFF;
  }

</style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile-ellipse">
      <div class="profile-icon">
        <i class="fas fa-user"></i>
      </div>
    </div>
    <div class="profile-name">Raiza Palles</div>

    <a href="{{ route('dashboard') }}" class="sidebar-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="text-decoration: none;">
      <i class="fas fa-home sidebar-btn-icon"></i>
      Dashboard
    </a>
    <a href="{{ route('recommendation') }}" class="sidebar-btn" style="text-decoration: none;">
      <i class="fas fa-suitcase sidebar-btn-icon"></i>
      Recommendation
    </a>
    <a href="{{ route('bookmarks') }}" class="sidebar-btn" style="text-decoration: none;">
      <i class="fas fa-bookmark sidebar-btn-icon"></i>
      Bookmarks
    </a>
    <div class="sidebar-btn">
      <i class="fas fa-cog sidebar-btn-icon"></i>
      Settings
    </div>
    <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
      @csrf
      <button type="submit" class="sidebar-btn" style="border: none; background: #648EB5; color: #FFF; font-size: 20px; font-weight: 600; cursor: pointer; width: 100%; text-align: center; padding: 0 10px; height: 39px; display: flex; align-items: center; justify-content: center; gap: 10px;">
        <i class="fas fa-sign-out-alt sidebar-btn-icon"></i>
        Logout
      </button>
    </form>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="top-navbar">
      <i class="fas fa-bars hamburger"></i>
      Job Portal - Mandaluyong
    </div>

    <div class="welcome">Welcome, Raiza!</div>

    <!-- Recommended & Bookmarked Jobs -->
    <div class="cards">
      <div class="card-medium">
        <div class="card-icon">
          <i class="fas fa-star"></i>
        </div>
        <div class="card-title">Recommended Jobs</div>
        <div class="view-all">View All Recommendations</div>
      </div>

      <div class="card-medium">
        <div class="card-icon">
          <i class="fas fa-bookmark"></i>
        </div>
        <div class="card-title">Bookmarked Jobs</div>
        <div class="view-all">View All Bookmarks</div>
      </div>
    </div>

    <!-- Top Job Recommendations -->
    <div class="card-large">
      <div class="recommendation-header">
        <h3>Top Job Recommendations</h3>
        <p>This is based on the skills that you have</p>
      </div>
      <div class="job-card">
        <div class="job-title">Cashier</div>
        <div class="job-location"><i class="fas fa-map-marker-alt"></i> Mandaluyong City</div>
        <div class="job-type"><i class="fas fa-briefcase"></i> Full-time</div>
        <div class="job-salary"><i class="fas fa-money-bill-wave"></i> Php 645/day</div>
        <div class="job-description">Handle cash transactions, provide customer service, and maintain a clean and organized checkout area.</div>
        <div class="skills-header"><strong>Skills Required:</strong></div>
        <div class="job-skills">
          <div class="skill">Customer Service</div>
          <div class="skill">Cash Handling</div>
          <div class="skill">Basic Math</div>
        </div>
        <div class="job-actions">
          <button class="view-details">View Details</button>
          <button class="save-job">Save Job</button>
        </div>
      </div>

      <div class="job-card">
        <div class="job-title">Sales Associate</div>
        <div class="job-location"><i class="fas fa-map-marker-alt"></i> Mandaluyong City</div>
        <div class="job-type"><i class="fas fa-briefcase"></i> Full-time</div>
        <div class="job-salary"><i class="fas fa-money-bill-wave"></i> Php 645/day</div>
        <div class="job-description">Assist customers, process sales transactions, and maintain store appearance.</div>
        <div class="skills-header"><strong>Skills Required:</strong></div>
        <div class="job-skills">
          <div class="skill">Customer Service</div>
          <div class="skill">Cash Handling</div>
          <div class="skill">Sales</div>
        </div>
        <div class="job-actions">
          <button class="view-details">View Details</button>
          <button class="save-job">Save Job</button>
        </div>
      </div>

    </div>
  </div>

</body>
</html>

