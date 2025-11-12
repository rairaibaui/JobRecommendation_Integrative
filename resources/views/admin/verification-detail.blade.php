<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <title>Verification Detail - Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      font-size: 16px;
    }

    body {
      width: 100%;
      min-height: 100vh;
      display: flex;
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
      padding: 0;
      margin: 0;
    }

    /* Sidebar - same as other admin pages */
    .sidebar {
      position: fixed;
      left: 20px;
      top: 20px;
      width: 250px;
      height: calc(100vh - 40px);
      border-radius: 8px;
      background: #FFF;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .profile-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-bottom: 20px;
    }

    .profile-ellipse {
      width: 62px;
      height: 62px;
      border-radius: 50%;
      background: linear-gradient(180deg, rgba(73,118,159,0.44) 48.29%, rgba(78,142,162,0.44) 86%);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-bottom: 12px;
    }

    .profile-ellipse img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-info {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }

    .profile-name {
      font-family: 'Poppins', sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: #2B4053;
      text-align: center;
    }

    .admin-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 14px;
      font-weight: 600;
      padding: 5px 12px;
      border-radius: 20px;
      background: #648EB5;
      color: white;
    }

    .sidebar-menu {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    /* Sidebar button styles (match job seeker layout) */
    .menu-item {
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

    .menu-item::before {
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

    .menu-item:hover {
      background: linear-gradient(90deg, #e8f4fd 0%, #f0f7fc 100%);
      color: #2B4053;
      transform: translateX(4px);
    }

    .menu-item:hover::before {
      transform: scaleY(1);
    }

    .menu-item.active {
      background: linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%);
      box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
      color: #FFF;
      font-weight: 600;
    }

    .menu-item.active::before {
      display: none;
    }

    .menu-item.active:hover {
      transform: translateX(0);
      box-shadow: 0 6px 16px rgba(100, 142, 181, 0.4);
    }

    .menu-item i {
      font-size: 18px;
      min-width: 20px;
      text-align: center;
      transition: transform 0.3s ease;
    }

    .menu-item:hover i {
      transform: scale(1.1);
    }

    .notification-badge {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: #EF4444;
      color: white;
      font-size: 11px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 12px;
      min-width: 20px;
      text-align: center;
      line-height: 1.4;
    }

    .menu-item.active .notification-badge {
      background: #DC2626;
    }

    /* Main Content */
    .main-content {
      margin-left: 290px;
      flex: 1;
      padding: 20px;
    }

    .admin-container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .admin-header {
      background: white;
      border-radius: 8px;
      padding: 24px 28px;
      margin-bottom: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .admin-header h1 {
      color: #2B4053;
      font-family: 'Poppins', sans-serif;
      font-size: 28px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .admin-header h1 i {
      font-size: 32px;
      color: #648EB5;
    }

    .admin-actions {
      display: flex;
      gap: 15px;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-primary {
      background: #648EB5;
      color: white;
    }

    .btn-primary:hover {
      background: #506B81;
      transform: translateY(-1px);
    }

    .btn-secondary {
      background: #F3F4F6;
      color: #506B81;
    }

    .btn-secondary:hover {
      background: #E5E7EB;
      transform: translateY(-1px);
    }

    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.06);
      padding: 20px;
      margin-bottom: 16px;
    }

    .grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap:12px;
    }

    .label {
      font-size:12px;
      color:#6c757d;
      text-transform:uppercase;
      letter-spacing:0.4px;
      margin-bottom:4px;
      font-weight:600;
    }

    .value {
      font-size:15px;
      color:#212529;
    }

    .badge {
      display:inline-block;
      padding:4px 10px;
      border-radius:999px;
      font-size:12px;
      font-weight:600;
    }

    .badge.approved {
      background:#d4edda;
      color:#155724;
      border:1px solid #c3e6cb;
    }

    .badge.pending {
      background:#fff3cd;
      color:#856404;
      border:1px solid #ffeaa7;
    }

    .badge.rejected {
      background:#f8d7da;
      color:#721c24;
      border:1px solid #f5c6cb;
    }

    table {
      width:100%;
      border-collapse: collapse;
    }

    th, td {
      text-align:left;
      padding:10px 12px;
      border-bottom:1px solid #eee;
    }

    thead th {
      background:#f8f9fa;
      font-size:12px;
      color:#6c757d;
      text-transform:uppercase;
      letter-spacing:0.3px;
    }

    .empty {
      text-align:center;
      color:#6c757d;
      padding:24px;
    }

    .title {
      margin:0;
      color:#2B4053;
      font-size:22px;
      font-weight:700;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }

      .sidebar.mobile-open {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="profile-section">
      <div class="profile-ellipse">
        @if(Auth::user()->profile_picture)
          <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Admin">
        @else
          <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:24px;font-weight:600;color:white;">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
          </div>
        @endif
      </div>
      <div class="profile-info">
        <div class="profile-name">{{ Auth::user()->name }}</div>
        <div class="admin-badge">System Admin</div>
      </div>
    </div>

    <nav class="sidebar-menu">
      <a href="{{ route('admin.dashboard') }}" class="menu-item">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('admin.analytics.index') }}" class="menu-item">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
      <a href="{{ route('admin.verifications.unified', ['tab' => 'resumes']) }}" class="menu-item active">
        <i class="fas fa-check-circle"></i>
        <span>Verifications</span>
        @include('admin.partials.notification-badge')
      </a>
      <a href="{{ route('admin.users.index') }}" class="menu-item">
        <i class="fas fa-users"></i>
        <span>Users</span>
      </a>
      <a href="{{ route('admin.audit.index') }}" class="menu-item">
        <i class="fas fa-history"></i>
        <span>Audit Logs</span>
      </a>
    </nav>

    <div style="margin-top:auto;padding-top:20px;">
      <form method="POST" action="{{ route('logout') }}" onsubmit="return showLogoutModal(this);">
        @csrf
        <button type="submit" class="menu-item" style="width: 100%; background: #648EB5; color: white; border: none; cursor: pointer; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600;">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="admin-container">
      <!-- Header -->
      <div class="admin-header">
        <h1>
          <i class="fas fa-file-certificate"></i>
          Verification Detail
        </h1>
        <div class="admin-actions">
          <a href="{{ route('admin.verifications.unified') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Verifications
          </a>
        </div>
      </div>

      <!-- Verification Details Card -->
      <div class="card">
        <h2 class="title" style="font-size:20px; margin-bottom:20px;">Document Information</h2>
        <div class="grid">
          <div>
            <div class="label">Company</div>
            <div class="value">{{ $validation->user->company_name ?? 'N/A' }}</div>
          </div>
          <div>
            <div class="label">Email</div>
            <div class="value">{{ $validation->user->email }}</div>
          </div>
          <div>
            <div class="label">Status</div>
            <div class="value">
              @php $status = $validation->validation_status; @endphp
              <span class="badge {{ $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'pending') }}">
                {{ ucfirst(str_replace('_',' ', $status)) }}
              </span>
            </div>
          </div>
          <div>
            <div class="label">Confidence</div>
            <div class="value">{{ $validation->confidence_score ?? 0 }}%</div>
          </div>
          <div>
            <div class="label">Permit Number</div>
            <div class="value">{{ $validation->permit_number ?? '—' }}</div>
          </div>
          <div>
            <div class="label">Expiry Date</div>
            @php
              $exp = $validation->permit_expiry_date;
              $expStr = $exp instanceof \DateTimeInterface ? $exp->format('M d, Y') : (is_string($exp) ? $exp : null);
            @endphp
            <div class="value">{{ $expStr ?? '—' }}</div>
          </div>
        </div>
        @if($validation->reason)
          <div style="margin-top:20px;">
            <div class="label">Notes</div>
            <div class="value">{{ $validation->reason }}</div>
          </div>
        @endif
        <div style="margin-top:20px;">
          <a href="{{ route('admin.verifications.file', $validation->id) }}" class="btn btn-primary">
            <i class="fas fa-eye"></i> View Original File
          </a>
        </div>
      </div>

      <!-- Audit Trail Card -->
      <div class="card">
        <h2 class="title" style="font-size:20px; margin-bottom:20px;">
          <i class="fas fa-history"></i> Audit Trail
        </h2>
        @if(isset($auditTrails) && $auditTrails->count())
          <table>
            <thead>
              <tr>
                <th>When</th>
                <th>Action</th>
                <th>Admin</th>
                <th>Notes</th>
                <th>IP</th>
                <th>User Agent</th>
              </tr>
            </thead>
            <tbody>
              @foreach($auditTrails as $entry)
                <tr>
                  <td>{{ $entry->created_at->format('M d, Y h:i A') }}</td>
                  <td>{{ ucfirst(str_replace('_',' ', $entry->action)) }}</td>
                  <td>{{ $entry->admin_email ?? '—' }}</td>
                  <td>{{ $entry->notes ?? '—' }}</td>
                  <td>{{ $entry->ip_address ?? '—' }}</td>
                  <td style="max-width:420px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $entry->user_agent ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="empty">No audit trail entries yet.</div>
        @endif
      </div>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  @include('partials.logout-confirm')

  
</body>
</html>
