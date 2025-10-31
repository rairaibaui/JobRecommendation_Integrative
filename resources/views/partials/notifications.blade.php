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
        case 'employee_resigned': return 'user-slash';
        default: return 'bell';
      }
    })();
    const readClass = n.read ? '' : 'unread';
    const createdAt = n.created_at ? new Date(n.created_at).toLocaleString() : '';
    return `
      <li class="notif-item ${readClass}" onclick="showEmpNotificationDetail(${n.id})" style="padding:12px 16px; display:flex; gap:10px; border-bottom:1px solid #f3f3f3; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='${n.read ? '' : '#f7fbff'}'">
        <i class="fas fa-${icon}" style="color:#648EB5; margin-top:3px;"></i>
        <div style="flex:1;">
          <div style="font-weight:600;">${escapeHtml(n.title || '')}</div>
          <div style="color:#555; font-size:13px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${escapeHtml(n.message || '')}</div>
          <div class="meta" style="font-size:12px; color:#888; margin-top:4px;">${createdAt}</div>
        </div>
        <i class="fas fa-chevron-right" style="color:#ccc; font-size:12px; align-self:center;"></i>
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

  function showEmpNotificationDetail(notifId){
    fetch("{{ route('notifications.list') }}")
      .then(r => r.json())
      .then(({notifications}) => {
        const notif = notifications.find(n => n.id === notifId);
        if (!notif) return;
        
        const icon = (function(){
          switch(notif.type){
            case 'interview_scheduled': return 'calendar-alt';
            case 'application_accepted': return 'check-circle';
            case 'application_rejected': return 'times-circle';
            case 'application_status_changed': return 'info-circle';
            case 'new_application': return 'inbox';
            case 'employment_terminated': return 'user-times';
            case 'employee_terminated': return 'user-minus';
            case 'employee_resigned': return 'user-slash';
            default: return 'bell';
          }
        })();
        
        const createdAt = notif.created_at ? new Date(notif.created_at).toLocaleString('en-US', {
          year: 'numeric', month: 'long', day: 'numeric',
          hour: '2-digit', minute: '2-digit'
        }) : '';
        
        let additionalInfo = '';
        if (notif.data) {
          try {
            const data = typeof notif.data === 'string' ? JSON.parse(notif.data) : notif.data;
            additionalInfo = '<div style="background:#f8f9fa; padding:15px; border-radius:8px; margin-top:15px;">';
            additionalInfo += '<h4 style="margin:0 0 10px 0; color:#648EB5; font-size:14px;">Additional Details</h4>';
            for (const [key, value] of Object.entries(data)) {
              const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
              additionalInfo += `<div style="margin-bottom:8px;"><strong>${label}:</strong> ${escapeHtml(String(value))}</div>`;
            }
            additionalInfo += '</div>';
          } catch(e) {}
        }
        
        const modal = document.createElement('div');
        modal.id = 'empNotifDetailModal';
        modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10000; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);';
        modal.innerHTML = `
          <div style="background:white; border-radius:16px; width:90%; max-width:600px; max-height:85vh; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalSlideIn 0.3s ease;">
            <div style="background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); padding:25px; color:white; position:relative;">
              <button onclick="closeEmpNotifModal()" style="position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.2); border:none; width:36px; height:36px; border-radius:50%; font-size:20px; cursor:pointer; color:white; display:flex; align-items:center; justify-content:center; transition:all 0.2s;">&times;</button>
              <div style="display:flex; align-items:center; gap:15px;">
                <div style="width:50px; height:50px; background:rgba(255,255,255,0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                  <i class="fas fa-${icon}" style="font-size:24px;"></i>
                </div>
                <div>
                  <h3 style="margin:0; font-size:20px; font-weight:600;">Notification Details</h3>
                  <p style="margin:5px 0 0 0; opacity:0.9; font-size:13px;">${createdAt}</p>
                </div>
              </div>
            </div>
            <div style="padding:25px; max-height:calc(85vh - 150px); overflow-y:auto;">
              <h4 style="margin:0 0 10px 0; color:#333; font-size:18px;">${escapeHtml(notif.title || 'Notification')}</h4>
              <p style="color:#555; line-height:1.6; margin:0;">${escapeHtml(notif.message || '')}</p>
              ${additionalInfo}
            </div>
            <div style="padding:20px 25px; border-top:1px solid #eee; display:flex; justify-content:flex-end; gap:10px;">
              <button onclick="closeEmpNotifModal()" style="background:#6c757d; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:14px; transition:all 0.2s;">Close</button>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        // Mark this specific notification as read and update count immediately
        if (!notif.read) {
          fetch("{{ url('/notifications') }}/" + notifId + "/read", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
          }).then(r => r.json()).then(({success, unreadCount}) => {
            if (success) {
              // Update badge count immediately
              const badge = document.getElementById('empNotifBadge');
              if (badge) {
                if (unreadCount > 0) {
                  badge.textContent = unreadCount;
                  badge.style.display = 'flex';
                } else {
                  badge.style.display = 'none';
                }
              }
              // Refresh the notification list to show updated read status
              loadEmpNotifications();
            }
          });
        }
      });
  }

  function closeEmpNotifModal(){
    const modal = document.getElementById('empNotifDetailModal');
    if (modal) {
      modal.remove();
      document.body.style.overflow = 'auto';
    }
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

  // Auto-refresh notifications every 30 seconds
  (function(){
    let lastUnreadCount = 0;
    
    function checkForNewNotifications(){
      fetch("{{ route('notifications.count') }}")
        .then(r => r.json())
        .then(({unread}) => {
          const badge = document.getElementById('empNotifBadge');
          if (!badge) return;
          
          // Update badge
          if (unread > 0) { 
            badge.textContent = unread; 
            badge.style.display = 'flex'; 
          } else { 
            badge.style.display = 'none'; 
          }
          
          // Show visual indicator for new notifications
          if (unread > lastUnreadCount && lastUnreadCount !== 0) {
            const bell = document.getElementById('empNotifBell');
            if (bell) {
              bell.style.animation = 'bellRing 0.5s ease';
              setTimeout(() => { bell.style.animation = ''; }, 500);
            }
          }
          
          lastUnreadCount = unread;
          
          // If dropdown is open, refresh the list
          const dd = document.getElementById('empNotifDropdown');
          if (dd && dd.style.display !== 'none') {
            loadEmpNotifications();
          }
        })
        .catch(() => {});
    }
    
    // Check every 3 seconds for near real-time notifications
    setInterval(checkForNewNotifications, 3000);
  })();
</script>

<style>
  @keyframes bellRing {
    0%, 100% { transform: rotate(0deg); }
    10%, 30%, 50%, 70%, 90% { transform: rotate(-15deg); }
    20%, 40%, 60%, 80% { transform: rotate(15deg); }
  }
</style>