{{-- 
  Unified Employer Design System
  Include this at the top of every employer page: @include('employer.partials.unified-styles')
  Version: 2.0 (Matches Dashboard & Job Postings)
--}}

<style>
/* ========================================
   UNIFIED EMPLOYER DESIGN SYSTEM v2.0
   Base Styles & Reset
  /* ========================================
     SHARED STYLES: sections, pills, profile header
     ======================================== */
  /* Base page layout to account for fixed top navbar and prevent horizontal scroll */
  html, body { height: 100%; }
  body {
    padding: 88px 20px 20px 20px; /* top = navbar(68px) + 20px gap */
    margin: 0;
    background: #f5f7fb;
    overflow-x: hidden;
    font-family: 'Roboto', sans-serif;
  }
  .section { margin-top: 16px; }

  .sec-title {
    margin: 0 0 10px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
    color: #334A5E;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    background: #e8f4ff;
    color: #2f5d8a;
    font-size: 12px;
    font-weight: 600;
    margin: 4px 6px 0 0;
  }

  .pill-primary {
    background: #648EB5;
    color: #fff;
  }

  .profile-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
  }

  .profile-header .left {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex: 1;
    min-width: 0;
  }

  .profile-header .right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
  }
  


/* ========================================
   SIDEBAR NAVIGATION
   Modern design with gradient active states
   ======================================== */
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
  gap: 6px;
  z-index: 100;
}

.sidebar .profile-ellipse {
  align-self: center;
}

.profile-ellipse {
  width: 62px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
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

.profile-icon i {
  font-size: 30px;
  color: #FFF;
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

.profile-name {
  align-self: center;
  font-family: 'Poppins', sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #000;
  margin-bottom: 8px;
}

.company-name {
  align-self: center;
  font-family: 'Roboto', sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #506B81;
  background: #eaf2fb;
  border: 1px solid #cddff2;
  border-radius: 999px;
  padding: 5px 12px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  letter-spacing: 0.3px;
  margin-bottom: 4px;
}

.company-badge {
  align-self: center;
  font-family: 'Roboto', sans-serif;
  font-size: 16px;
  font-weight: 700;
  color: #2B4053;
  display: inline-flex;
  align-items: center;
  text-transform: uppercase;
  margin-bottom: 20px;
}

.sidebar .sidebar-btn {
  align-self: flex-start;
}

/* Modern Sidebar Button Styles (v2.0) */
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

/* ========================================
   TOP NAVBAR
   ======================================== */
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
  justify-content: space-between;
}

.navbar-left {
  display: flex;
  align-items: center;
}

.hamburger {
  margin-right: 20px;
  color: #FFF;
  cursor: pointer;
  display: none;
}

.logout-btn {
  background: transparent;
  border: 1px solid #FFF;
  color: #FFF;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #FFF;
  color: #2B4053;
}

/* ========================================
   MAIN CONTENT AREA
   Using Flexbox for responsive layout
   Sidebar width (250px) + left position (20px) - tighter gap = 260px
   ======================================== */
.main {
  margin-left: 260px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.main-content {
  margin-left: 260px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.content-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
}

/* Page Header */
.page-header {
  margin-bottom: 24px;
}

.page-title {
  font-family: 'Poppins', sans-serif;
  font-size: 28px;
  font-weight: 600;
  color: #FFF;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.page-title i {
  font-size: 26px;
}

.page-subtitle {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.85);
  margin-top: 8px;
  font-weight: 400;
}

/* ========================================
   CARDS & CONTAINERS
   Modern design with refined shadows
   ======================================== */
.card {
  background: #FFF;
  border-radius: 12px;
  padding: 28px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(100, 142, 181, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover {
  box-shadow: 0 4px 16px rgba(100, 142, 181, 0.12);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #F3F4F6;
}

.card-title {
  font-family: 'Poppins', sans-serif;
  font-size: 20px;
  font-weight: 600;
  color: #334A5E;
  margin: 0;
}

.card-body {
  color: #4B5563;
  font-size: 14px;
  line-height: 1.6;
}

/* ========================================
   BUTTONS
   Gradient backgrounds with hover effects
   ======================================== */
.btn {
  padding: 12px 24px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-size: 15px;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.btn:hover {
  transform: translateY(-2px);
}

.btn-primary {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.25);
}

.btn-primary:hover {
  box-shadow: 0 6px 20px rgba(100, 142, 181, 0.35);
}

.btn-secondary {
  background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(108, 117, 125, 0.25);
}

.btn-secondary:hover {
  box-shadow: 0 6px 20px rgba(108, 117, 125, 0.35);
}

.btn-success {
  background: linear-gradient(135deg, #28a745 0%, #218838 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.25);
}

.btn-success:hover {
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.35);
}

.btn-danger {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.25);
}

.btn-danger:hover {
  box-shadow: 0 6px 20px rgba(220, 53, 69, 0.35);
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
  color: #000;
  box-shadow: 0 2px 8px rgba(255, 193, 7, 0.25);
}

.btn-warning:hover {
  box-shadow: 0 6px 20px rgba(255, 193, 7, 0.35);
}

.btn-sm {
  padding: 8px 16px;
  font-size: 13px;
  border-radius: 8px;
}

.btn-icon {
  width: 36px;
  height: 36px;
  padding: 0;
  border-radius: 8px;
  background: #F3F4F6;
  color: #6B7280;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.btn-icon:hover {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #FFF;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ========================================
   FORMS & INPUTS
   Consistent styling across all forms
   ======================================== */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.form-label {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 14px;
  color: #334A5E;
  display: flex;
  align-items: center;
  gap: 6px;
}

.form-label i {
  font-size: 14px;
  color: #648EB5;
}

.form-control,
.form-input,
.form-textarea,
.form-select {
  padding: 12px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 10px;
  font-size: 14px;
  font-family: 'Roboto', sans-serif;
  color: #334A5E;
  background: #FFF;
  transition: all 0.3s ease;
  width: 100%;
}

.form-control:focus,
.form-input:focus,
.form-textarea:focus,
.form-select:focus {
  outline: none;
  border-color: #648EB5;
  box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
}

.form-control:disabled,
.form-input:disabled {
  background: #F3F4F6;
  cursor: not-allowed;
}

.form-textarea {
  min-height: 120px;
  resize: vertical;
}

.form-select {
  cursor: pointer;
}

.form-help {
  font-size: 13px;
  color: #6B7280;
  margin-top: 4px;
}

.form-error {
  font-size: 13px;
  color: #dc3545;
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.form-error i {
  font-size: 14px;
}

/* ========================================
   TABLES
   Modern table design with hover effects
   ======================================== */
.table-container {
  overflow-x: auto;
  border-radius: 12px;
  border: 1px solid rgba(100, 142, 181, 0.1);
}

.table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.table thead {
  background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
}

.table th {
  padding: 16px;
  text-align: left;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 13px;
  color: #334A5E;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #E5E7EB;
}

.table td {
  padding: 16px;
  border-bottom: 1px solid #F3F4F6;
  color: #4B5563;
  vertical-align: middle;
}

.table tbody tr {
  transition: all 0.2s ease;
}

.table tbody tr:hover {
  background: linear-gradient(90deg, #f0f7fc 0%, #e8f4fd 100%);
}

.table tbody tr:last-child td {
  border-bottom: none;
}

/* ========================================
   BADGES & STATUS INDICATORS
   Gradient backgrounds with borders
   ======================================== */
.badge {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.badge i {
  font-size: 12px;
}

.badge-success,
.badge-approved,
.badge-active,
.badge-accepted {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  color: #0f5132;
  border: 1px solid #c3e6cb;
}

.badge-warning,
.badge-pending,
.badge-for_interview,
.badge-interviewed {
  background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
  color: #856404;
  border: 1px solid #ffeaa7;
}

.badge-danger,
.badge-rejected,
.badge-closed {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  color: #842029;
  border: 1px solid #f5c6cb;
}

.badge-info,
.badge-reviewing {
  background: linear-gradient(135deg, #cfe2ff 0%, #b6d4fe 100%);
  color: #084298;
  border: 1px solid #b6d4fe;
}

.badge-secondary,
.badge-draft {
  background: linear-gradient(135deg, #e2e3e5 0%, #d3d4d5 100%);
  color: #383d41;
  border: 1px solid #d3d4d5;
}

/* ========================================
   NOTICES & ALERTS
   Informational boxes with left accent
   ======================================== */
.notice,
.alert {
  padding: 16px 20px;
  border-radius: 10px;
  margin-bottom: 20px;
  display: flex;
  align-items: start;
  gap: 12px;
  font-size: 14px;
  line-height: 1.6;
  border-left: 4px solid;
}

.notice i,
.alert i {
  font-size: 20px;
  margin-top: 2px;
}

.notice-info,
.alert-info {
  background: #e8f4fd;
  color: #2B4053;
  border-left-color: #648EB5;
}

.notice-success,
.alert-success {
  background: #d4edda;
  color: #155724;
  border-left-color: #28a745;
}

.notice-warning,
.alert-warning {
  background: #fff3cd;
  color: #856404;
  border-left-color: #ffc107;
}

.notice-danger,
.alert-danger {
  background: #f8d7da;
  color: #721c24;
  border-left-color: #dc3545;
}

/* ========================================
   STATISTICS & METRICS
   Info boxes and stat cards
   ======================================== */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat-box {
  background: #FFF;
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid #648EB5;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.stat-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(100, 142, 181, 0.15);
}

.stat-box h3 {
  margin: 0 0 8px 0;
  font-size: 28px;
  font-weight: 700;
  color: #334A5E;
  font-family: 'Poppins', sans-serif;
}

.stat-box p {
  margin: 0;
  font-size: 13px;
  color: #6B7280;
  font-weight: 500;
}

/* ========================================
   FILTERS & SEARCH
   Modern filter buttons and search inputs
   ======================================== */
.filters {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  align-items: center;
}

.filters-container {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  align-items: center;
  margin-bottom: 20px;
}

.filter-btn {
  padding: 8px 18px;
  border-radius: 20px;
  border: 2px solid #648EB5;
  background: #fff;
  color: #648EB5;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.filter-btn:hover {
  background: #e8f4fd;
  transform: translateY(-1px);
}

.filter-btn.active {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #fff;
  border-color: #648EB5;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.3);
}

/* ========================================
   STATS GRID & CARDS
   Statistics display with modern cards
   ======================================== */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.stat {
  background: #fff;
  padding: 20px 16px;
  border-radius: 12px;
  border-left: 4px solid #648EB5;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
}

.stat:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 16px rgba(100, 142, 181, 0.15);
}

.stat h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 32px;
  font-weight: 700;
  color: #334A5E;
  margin: 0 0 8px 0;
  line-height: 1;
}

.stat p {
  font-size: 13px;
  color: #6B7280;
  font-weight: 500;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 250px;
}

.search-box input {
  width: 100%;
  padding: 10px 40px 10px 16px;
  border: 2px solid #E5E7EB;
  border-radius: 10px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #648EB5;
  box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
}

.search-box i {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #6B7280;
  pointer-events: none;
}

/* ========================================
   JOB CARDS & APPLICANT CARDS
   Expandable cards for applicants page
   ======================================== */
.job-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  border: 1px solid rgba(100, 142, 181, 0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.job-card:hover {
  box-shadow: 0 4px 16px rgba(100, 142, 181, 0.12);
  border-color: #648EB5;
}

.job-header {
  padding: 20px;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e5e7eb;
}

.job-title {
  font-family: 'Poppins', sans-serif;
  font-size: 18px;
  font-weight: 600;
  color: #334A5E;
  margin-bottom: 12px;
}

.job-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  font-size: 13px;
  color: #6B7280;
}

.job-preview > div {
  display: flex;
  align-items: center;
  gap: 6px;
}

.job-preview i {
  color: #648EB5;
  font-size: 12px;
}

.job-details {
  padding: 20px;
  display: none;
}

.job-card.expanded .job-details {
  display: block;
}

.job-card.expanded .job-header i.fa-chevron-down {
  transform: rotate(180deg);
}

.applicants-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
}

.app-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  padding: 16px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
  transition: all 0.2s ease;
  overflow: hidden;
  width: 100%;
  box-sizing: border-box;
}

.app-card:hover {
  background: #fff;
  border-color: #648EB5;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.1);
}

.app-card .applicant-info {
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.app-card .actions {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 6px;
  flex-shrink: 0;
  align-self: flex-start;
  margin-left: 12px;
}

.btn-view-profile {
  background: #e9f2fb;
  color: #2f5d8a;
  border: 1px solid #cfe1f5;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  text-decoration: none;
}

.btn-view-profile:hover {
  background: #dfeefe;
  box-shadow: 0 2px 8px rgba(79, 141, 190, 0.15);
}

.btn-view-profile i { color: #4E8EA2; }

.applicant-full-details { 
  display: none; 
  margin-top: 12px; 
  padding-top: 12px; 
  border-top: 1px solid #e9ecef; 
}
.applicant-full-details.expanded { display: block; }

.app-card .actions button,
.app-card .actions form button {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.app-card .actions button:hover,
.app-card .actions form button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.app-card .actions button.review,
.app-card .actions form button[title*="Reviewing"] {
  background: #648EB5;
}

.app-card .actions button.interview,
.app-card .actions form button[title*="Interview"] {
  background: #17a2b8;
}

.app-card .actions button.interviewed-btn,
.app-card .actions form button[title*="Interviewed"] {
  background: #ffc107;
  color: #333;
}

.app-card .actions button.accept,
.app-card .actions form button[title*="Accept"],
.app-card .actions form button[title*="Hire"] {
  background: #28a745;
}

.app-card .actions button.reject,
.app-card .actions form button[title*="Reject"] {
  background: #dc3545;
}

.app-card .actions button.btn-delete,
.app-card .actions form button[title*="Delete"] {
  background: #6c757d;
}

/* ========================================
   RESPONSIVE DESIGN
   Mobile & Tablet breakpoints
   ======================================== */

/* Tablet (768px - 1024px) */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

/* Mobile (< 768px) */
@media (max-width: 768px) {
  body {
    padding: 68px 12px 12px 12px;
    gap: 12px;
  }

  .sidebar {
    left: -270px;
    transition: left 0.3s ease;
    z-index: 999;
  }

  .sidebar.mobile-open {
    left: 12px;
  }

  .main, .main-content {
    margin-left: 0;
    max-width: 100%;
  }
  
  .stat-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .stat h3 {
    font-size: 24px;
  }
  
  .page-title {
    font-size: 22px;
  }

  .top-navbar {
    font-size: 18px;
    padding: 0 12px;
  }

  .hamburger {
    display: block;
  }

  .card {
    padding: 20px;
  }

  .card-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .stats-grid, .stat-grid {
    grid-template-columns: 1fr;
  }
  
  .stat h3 {
    font-size: 28px;
  }

  .filters-container, .filters {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-btn {
    justify-content: center;
  }

  .search-box {
    min-width: 100%;
  }

  .table-container {
    border-radius: 8px;
  }

  .table th,
  .table td {
    padding: 12px 8px;
    font-size: 13px;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .btn-sm {
    width: auto;
  }
}

/* Small Mobile (< 480px) */
@media (max-width: 480px) {
  .top-navbar {
    font-size: 16px;
  }

  .card-title {
    font-size: 18px;
  }

  .stat-box h3 {
    font-size: 24px;
  }

  .table th {
    font-size: 12px;
  }

  .table td {
    font-size: 12px;
  }
}

/* ========================================
   UTILITY CLASSES
   Spacing, text, and layout helpers
   ======================================== */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.mt-0 { margin-top: 0 !important; }
.mt-1 { margin-top: 8px !important; }
.mt-2 { margin-top: 16px !important; }
.mt-3 { margin-top: 24px !important; }
.mb-0 { margin-bottom: 0 !important; }
.mb-1 { margin-bottom: 8px !important; }
.mb-2 { margin-bottom: 16px !important; }
.mb-3 { margin-bottom: 24px !important; }

.p-0 { padding: 0 !important; }
.p-1 { padding: 8px !important; }
.p-2 { padding: 16px !important; }
.p-3 { padding: 24px !important; }

.d-flex { display: flex !important; }
.flex-column { flex-direction: column !important; }
.flex-wrap { flex-wrap: wrap !important; }
.align-items-center { align-items: center !important; }
.justify-content-between { justify-content: space-between !important; }
.justify-content-center { justify-content: center !important; }
.gap-1 { gap: 8px !important; }
.gap-2 { gap: 16px !important; }
.gap-3 { gap: 24px !important; }

.text-muted { color: #6B7280 !important; }
.text-primary { color: #648EB5 !important; }
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }

.font-weight-normal { font-weight: 400 !important; }
.font-weight-medium { font-weight: 500 !important; }
.font-weight-semibold { font-weight: 600 !important; }
.font-weight-bold { font-weight: 700 !important; }

.w-100 { width: 100% !important; }
.h-100 { height: 100% !important; }

/* Additional Utility Classes for Employer Pages */

/* Flexbox utilities */
.d-flex { display: flex !important; }
.flex-column { flex-direction: column !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }

/* Spacing utilities */
.gap-1 { gap: 8px !important; }
.gap-2 { gap: 12px !important; }
.gap-3 { gap: 16px !important; }
.mb-2 { margin-bottom: 12px !important; }
.mb-3 { margin-bottom: 16px !important; }
.mb-4 { margin-bottom: 24px !important; }
.mt-4 { margin-top: 24px !important; }
.mt-5 { margin-top: 30px !important; }
.justify-content-end { justify-content: flex-end !important; }

/* Section titles */
.section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 22px;
  color: #334A5E;
  font-weight: 600;
  margin: 0;
}

/* Subsection titles */
.subsection-title {
  font-family: 'Poppins', sans-serif;
  font-size: 16px;
  color: #334A5E;
  font-weight: 600;
  margin-bottom: 8px;
}

/* Job description text */
.job-description {
  color: #555;
  line-height: 1.6;
  white-space: pre-wrap;
  margin: 0;
}

/* Skills tags */
.skills-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.skill-tag {
  background: linear-gradient(135deg, #e8f0f7 0%, #d4e5f3 100%);
  color: #334A5E;
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid #c5d9ed;
  transition: all 0.2s ease;
}

.skill-tag:hover {
  background: linear-gradient(135deg, #d4e5f3 0%, #c5d9ed 100%);
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(100, 142, 181, 0.2);
}

/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
  margin-bottom: 16px;
}

/* Search input */
.search-input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 14px;
  font-family: 'Roboto', sans-serif;
  transition: all 0.3s;
  background: #fff;
}

.search-input:focus {
  outline: none;
  border-color: #648EB5;
  box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1);
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
  font-size: 16px;
  pointer-events: none;
}

/* Employee cards and job posting cards - consistent styling */
.employee-card, .job-posting-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 16px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.employee-card:hover, .job-posting-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  border-color: #648EB5;
}

/* Timeline for history */
.timeline-item {
  position: relative;
  padding-left: 40px;
  padding-bottom: 24px;
  border-left: 2px solid #e5e7eb;
}

.timeline-item:last-child {
  border-left: 2px solid transparent;
}

.timeline-icon {
  position: absolute;
  left: -12px;
  top: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  background: #fff;
  border: 2px solid;
}

.timeline-icon.hired {
  color: #28a745;
  border-color: #28a745;
}

.timeline-icon.rejected {
  color: #dc3545;
  border-color: #dc3545;
}

.timeline-icon.terminated {
  color: #6c757d;
  border-color: #6c757d;
}

.timeline-icon.resigned {
  color: #ffc107;
  border-color: #ffc107;
}

/* Chart containers */
.chart-container {
  position: relative;
  height: 300px;
  margin-top: 20px;
}

/* Progress bars in analytics */
.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.3s ease;
}

/* Stat display in cards */
.stat-display {
  background: #fff;
  border-radius: 10px;
  padding: 12px 16px;
  border-left: 4px solid;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat-display .value {
  font-size: 24px;
  color: #334A5E;
  font-weight: 700;
  margin: 0;
}

.stat-display .label {
  font-size: 12px;
  color: #666;
  margin: 4px 0 0 0;
}

/* Metric labels and values for analytics */
.metric-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.metric-value {
  font-size: 14px;
  font-weight: 600;
  color: #334A5E;
}

/* Conversion metrics */
.conversion-metrics {
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #e9ecef;
}

.conversion-row {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
  margin-bottom: 8px;
}

.conversion-row:last-child {
  margin-bottom: 0;
}

.conversion-label {
  color: #666;
}

.conversion-rate {
  font-weight: 700;
}

.conversion-rate.success {
  color: #28a745;
}

/* Charts grid */
.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

/* Chart wrapper */
.chart-wrapper {
  position: relative;
  height: 300px;
  margin-top: 15px;
}

/* Retention display for analytics */
.retention-display {
  text-align: center;
  padding: 20px;
}

.retention-rate {
  font-size: 48px;
  font-weight: 700;
  color: #648EB5;
  font-family: 'Poppins', sans-serif;
  margin: 0;
}

.retention-label {
  font-size: 14px;
  color: #666;
  margin-top: 10px;
}

.retention-stats {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 10px;
  margin-top: 30px;
}

.retention-stat-value {
  font-size: 24px;
  font-weight: 600;
  margin: 0;
}

.retention-stat-value.success {
  color: #28a745;
}

.retention-stat-value.danger {
  color: #dc3545;
}

.retention-stat-value.warning {
  color: #ffc107;
}

.retention-stat-label {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

/* Responsive adjustments */
@media (max-width: 968px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .section-title {
    font-size: 18px;
  }
  
  .chart-container, .chart-wrapper {
    height: 250px;
  }
  
  .retention-rate {
    font-size: 36px;
  }
  
  .retention-stats {
    gap: 8px;
    margin-top: 20px;
  }
  
  .retention-stat-value {
    font-size: 20px;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .d-flex.justify-content-between {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 12px;
  }
}

</style>

<script>
// Mobile Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
  const hamburger = document.querySelector('.hamburger');
  const sidebar = document.querySelector('.sidebar');
  
  if (hamburger && sidebar) {
    hamburger.addEventListener('click', function() {
      sidebar.classList.toggle('mobile-open');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
          sidebar.classList.remove('mobile-open');
        }
      }
    });
  }
});
</script>
