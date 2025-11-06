{{-- 
  Unified Job Seeker Design System
  Include this at the top of every job seeker page: @include('jobseeker.partials.unified-styles')
  Version: 2.0 (Matches Employer Design)
--}}

<style>
/* ========================================
   UNIFIED JOB SEEKER DESIGN SYSTEM v2.0
   Base Styles & Reset
   ======================================== */
* { 
  box-sizing: border-box; 
  margin: 0; 
  padding: 0; 
}

html {
  overflow-x: hidden;
  max-width: 100%;
}

body {
  width: 100%;
  max-width: 100vw;
  height: 100vh;
  display: flex;
  font-family: 'Roboto', sans-serif;
  background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
  padding: 88px 20px 20px 20px;
  gap: 20px;
  overflow-x: hidden;
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
  gap: 20px;
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
   Sidebar width is 250px; add a small 12px visual gap between sidebar and content
   ======================================== */
.main {
  margin-left: 262px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.main-content {
  margin-left: 262px;
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

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .page-title {
    font-size: 22px;
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

/* ========================================
   JOB SEEKER SPECIFIC STYLES
   Job cards, bookmarks, applications
   ======================================== */

/* Job Cards */
.job-card {
  background: #FFF;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(100, 142, 181, 0.1);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.job-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(180deg, #648EB5 0%, #4E8EA2 100%);
  transform: scaleY(0);
  transform-origin: top;
  transition: transform 0.3s ease;
}

.job-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(100, 142, 181, 0.18);
  border-color: rgba(100, 142, 181, 0.3);
}

.job-card:hover::before {
  transform: scaleY(1);
}

.job-title {
  font-family: 'Poppins', sans-serif;
  font-size: 20px;
  font-weight: 600;
  color: #334A5E;
  margin: 0 0 12px 0;
  line-height: 1.3;
}

.job-preview {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 16px;
}

.job-location, .job-type, .job-salary {
  font-size: 14px;
  color: #4B5563;
  display: flex;
  align-items: center;
  gap: 6px;
}

.job-location i, .job-type i, .job-salary i {
  color: #648EB5;
  font-size: 14px;
  width: 16px;
  text-align: center;
}

.job-description {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin: 12px 0;
}

.job-details {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s ease-out, opacity 0.3s ease;
  opacity: 0;
}

.job-details.expanded {
  max-height: 2000px;
  opacity: 1;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 2px solid #F3F4F6;
}

.skills-section {
  margin-top: 16px;
}

.skills-header {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 600;
  color: #334A5E;
  margin-bottom: 8px;
}

.job-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.skill {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #FFF;
  border-radius: 16px;
  padding: 6px 12px;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.skill.matching-skill {
  background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
  position: relative;
  box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
}

.skill.matching-skill::after {
  content: '';
  position: absolute;
  top: -3px;
  right: -3px;
  width: 10px;
  height: 10px;
  background: #FFD700;
  border-radius: 50%;
  border: 2px solid #4CAF50;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.8; }
}

.job-actions {
  display: flex;
  gap: 12px;
  margin-top: 16px;
  flex-wrap: wrap;
}

.view-details, .apply-btn {
  padding: 10px 20px;
  border-radius: 10px;
  border: none;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.view-details {
  background: #F3F4F6;
  color: #648EB5;
  border: 2px solid #E5E7EB;
}

.view-details:hover {
  background: #E5E7EB;
  border-color: #648EB5;
  transform: translateY(-2px);
}

.apply-btn {
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #FFF;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.25);
  flex: 1;
}

.apply-btn:hover {
  box-shadow: 0 6px 20px rgba(100, 142, 181, 0.35);
  transform: translateY(-2px);
}

/* Bookmark button */
.bookmark-btn {
  background: #FFF;
  border: 2px solid rgba(100, 142, 181, 0.2);
  color: #648EB5;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  font-size: 18px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.bookmark-btn i {
  transition: all 0.3s ease;
}

.bookmark-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  border-color: #648EB5;
}

.bookmark-btn i.fas {
  color: #FFD166; /* gold for bookmarked */
  transform: scale(1.1);
}

.bookmark-btn i.far {
  color: #648EB5;
}

/* Jobs Grid */
.jobs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

/* Recommendation Header */
.recommendation-header {
  margin-bottom: 20px;
}

.recommendation-header h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 24px;
  font-weight: 600;
  color: #334A5E;
  margin: 0 0 8px 0;
}

.recommendation-header p {
  font-size: 14px;
  color: #6B7280;
  margin: 0;
}

/* No Bookmarks/Results State */
.no-bookmarks, .no-results {
  text-align: center;
  padding: 60px 20px;
  background: #FFF;
  border-radius: 12px;
  border: 2px dashed #E5E7EB;
}

.no-bookmarks-icon, .no-results-icon {
  font-size: 64px;
  color: #D1D5DB;
  margin-bottom: 20px;
}

.no-bookmarks-title, .no-results-title {
  font-family: 'Poppins', sans-serif;
  font-size: 22px;
  font-weight: 600;
  color: #334A5E;
  margin: 0 0 10px 0;
}

.no-bookmarks-text, .no-results-text {
  font-size: 14px;
  color: #6B7280;
  margin: 0 0 24px 0;
}

.browse-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #FFF;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.25);
}

.browse-link:hover {
  box-shadow: 0 6px 20px rgba(100, 142, 181, 0.35);
  transform: translateY(-2px);
}

/* Card Large (for main content areas) */
.card-large {
  background: #FFF;
  border-radius: 12px;
  padding: 28px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(100, 142, 181, 0.1);
}

/* Welcome Message */
.welcome {
  font-family: 'Poppins', sans-serif;
  font-size: 32px;
  font-weight: 600;
  color: #FFF;
  margin-bottom: 20px;
}

/* Responsive Grid Adjustments */
@media (max-width: 1024px) {
  .jobs-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 768px) {
  .jobs-grid {
    grid-template-columns: 1fr;
  }
  
  .job-preview {
    flex-direction: column;
    gap: 8px;
  }
  
  .job-actions {
    flex-direction: column;
  }
  
  .apply-btn, .view-details {
    width: 100%;
    justify-content: center;
  }
  
  .welcome {
    font-size: 24px;
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
