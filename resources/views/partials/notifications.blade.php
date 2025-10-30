<div class="notif-wrapper" style="position:relative;">
  <div id="empNotifBell" class="notification-bell" onclick="toggleEmpNotifDropdown(event)" style="position:relative; cursor:pointer; padding:10px;">
    <i class="fas fa-bell"></i>
    <span id="empNotifBadge" class="badge" style="position:absolute; top:5px; right:5px; background:#ff4757; color:#fff; border-radius:50%; padding:2px 6px; font-size:10px; min-width:18px; height:18px; align-items:center; justify-content:center; display:none;">0</span>
  </div>
  <div id="empNotifDropdown" class="notif-dropdown" style="display:none; position:absolute; top:54px; right:0; width:360px; max-height:420px; overflow-y:auto; background:#fff; color:#333; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,0.18); padding:10px 0; z-index:1100; font-size:14px; line-height:1.35;">
    <div class="notif-header" style="padding:10px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eee; font-weight:600;">
      <span>Notifications</span>
      <div style="display:flex; gap:8px;">
        <button onclick="empMarkAllNotificationsRead(event)" style="background:#eee;color:#333;border:1px solid #ddd;border-radius:6px;padding:6px 10px;font-size:12px;cursor:pointer;">Mark all as read</button>
        <button onclick="empRefreshNotifications(event)" style="background:#4E8EA2; color:#fff; border:none; border-radius:8px; padding:6px 10px; cursor:pointer; font-size:12px;">Refresh</button>
      </div>
    </div>
    <ul id="empNotifList" class="notif-list" style="list-style:none; margin:0; padding:0;"></ul>
  </div>
</div>

<script>
  (function(){
    document.addEventListener('click', function(e){
      const dd = document.getElementById('empNotifDropdown');
      const bell = document.getElementById('empNotifBell');
      if (!dd || !bell) return;
      const visible = dd.style.display !== 'none';
      if (visible && !dd.contains(e.target) && !bell.contains(e.target)) {
        dd.style.display = 'none';
      }
    });
  })();

  function toggleEmpNotifDropdown(e){
    e.stopPropagation();
    const dd = document.getElementById('empNotifDropdown');
    if (!dd) return;
    const visible = dd.style.display !== 'none';
    dd.style.display = visible ? 'none' : 'block';
    if (!visible && dd.dataset.loaded !== '1') { loadEmpNotifications(); }
  }

  function loadEmpNotifications(){
    fetch("{{ route('notifications.list') }}")
      .then(r => r.json())
      .then(({success, unread, notifications}) => {
        const list = document.getElementById('empNotifList');
        const badge = document.getElementById('empNotifBadge');
        const dd = document.getElementById('empNotifDropdown');
        if (!list || !badge || !dd) return;
        if (unread > 0) { badge.style.display = 'flex'; badge.textContent = unread; } else { badge.style.display = 'none'; }
        dd.dataset.loaded = '1';
        if (!notifications.length){ list.innerHTML = '<li class="notif-empty" style="padding:20px; text-align:center; color:#777;">No notifications yet</li>'; return; }
        list.innerHTML = notifications.map(n => empRenderNotifItem(n)).join('');
      })
      .catch(() => {});
  }

  function empRenderNotifItem(n){
    const icon = (function(){
      switch(n.type){
        case 'interview_scheduled': return 'calendar-alt';
        case 'application_accepted': return 'check-circle';
        case 'application_rejected': return 'times-circle';
        case 'application_status_changed': return 'info-circle';
        case 'new_application': return 'inbox';
        case 'employment_terminated': return 'user-times';
        case 'employee_terminated': return 'user-minus';
        default: return 'bell';
      }
    })();
    const readClass = n.read ? '' : 'unread';
    const createdAt = n.created_at ? new Date(n.created_at).toLocaleString() : '';
    return `
      <li class="notif-item ${readClass}" style="padding:12px 16px; display:flex; gap:10px; border-bottom:1px solid #f3f3f3;">
        <i class="fas fa-${icon}" style="color:#648EB5; margin-top:3px;"></i>
        <div>
          <div style="font-weight:600;">${escapeHtml(n.title || '')}</div>
          <div style="color:#555;">${escapeHtml(n.message || '')}</div>
          <div class="meta" style="font-size:12px; color:#888; margin-top:4px;">${createdAt}</div>
        </div>
      </li>
    `;
  }

  function empMarkAllNotificationsRead(e){
    e.stopPropagation();
    fetch("{{ route('notifications.markAllRead') }}", { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }})
      .then(r => r.json())
      .then(() => { loadEmpNotifications(); })
      .catch(() => {});
  }

  function empRefreshNotifications(e){ 
    e.stopPropagation(); 
    window.location.reload(); 
  }

  function escapeHtml(str){
    return (str||'').replace(/[&<>"]+/g, function(s){
      const map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' }; return map[s] || s;
    });
  }

  // Load badge count on page load (single time, no flicker)
  (function(){
    let badgeLoaded = false;
    const loadBadgeOnce = function(){
      if (badgeLoaded) return;
      badgeLoaded = true;
      fetch("{{ route('notifications.count') }}")
        .then(r => r.json())
        .then(({unread}) => {
          const badge = document.getElementById('empNotifBadge');
          if (!badge) return;
          if (unread > 0) { 
            badge.textContent = unread; 
            badge.style.display = 'flex'; 
          } else { 
            badge.style.display = 'none'; 
          }
        })
        .catch(() => {});
    };
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', loadBadgeOnce);
    } else {
      loadBadgeOnce();
    }
  })();
</script>