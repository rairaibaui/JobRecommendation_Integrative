{{-- 
  Unified Admin Design System
  Include this at the top of every admin page: @include('admin.partials.unified-styles')
  Version: 2.0 (Matches Job Seeker Design)
--}}

<style>
/* ========================================
   UNIFIED ADMIN DESIGN SYSTEM v2.0
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
   System Admin with enhanced vertical spacing
   ======================================== */
.sidebar {
  position: fixed;
  left: 20px;
  top: 88px;
  width: 250px;
  height: calc(100vh - 108px);
  border-radius: 8px;
  background: #FFF;
  padding: 20px 20px 20px 20px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  z-index: 100;
}

/* Extra top spacing to match Employer sidebar */
.sidebar-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  gap: 20px;
  margin-top: 30px;
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

/* System Admin Button Styling */
.system-admin-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 40px;
  padding: 0 16px;
  border-radius: 20px;
  background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
  color: #fff;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  border: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(100, 142, 181, 0.3);
}

.system-admin-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(100, 142, 181, 0.4);
}

.system-admin-btn i {
  font-size: 16px;
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
   RESPONSIVE DESIGN
   Mobile & Tablet breakpoints
   ======================================== */

/* Tablet (768px - 1024px) */
@media (max-width: 1024px) {
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

  .page-title {
    font-size: 22px;
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