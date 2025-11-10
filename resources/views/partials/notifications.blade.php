<div class="notif-wrapper" style="position:relative;">
  <div id="empNotifBell" class="notification-bell" onclick="toggleEmpNotifDropdown(event)" style="position:relative; cursor:pointer; padding:10px;">
    <i class="fas fa-bell"></i>
    <span id="empNotifBadge" class="badge" style="position:absolute; top:5px; right:5px; background:#ff4757; color:#fff; border-radius:50%; padding:2px 6px; font-size:10px; min-width:18px; height:18px; align-items:center; justify-content:center; display:none;">0</span>
  </div>
  <div id="empNotifDropdown" class="notif-dropdown" style="display:none; position:absolute; top:54px; right:0; width:480px; max-height:600px; overflow-y:auto; overflow-x:hidden; background:#fff; color:#333; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,0.18); padding:16px; z-index:1100; font-size:14px; line-height:1.35;">
    <div class="notif-header" style="padding:20px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eef2f6; background:#ffffff;">
      <div style="display:flex; align-items:center; gap:8px; min-width:0;">
        <span style="font-size:16px; font-weight:700; color:#243b55; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">Notifications</span>
        <span style="font-size:12px; color:#6b7280; white-space:nowrap;">&middot; <span id="empNotifCountSummary" style="font-weight:600;">0</span> new</span>
      </div>
      <div style="display:flex; gap:6px; align-items:center;">
        <button onclick="empMarkAllNotificationsRead(event)" aria-label="Mark all as read" class="notif-btn notif-markall" style="background:transparent;color:#334155;border:1px solid #e6eef6;border-radius:999px;padding:6px 10px;font-size:13px;cursor:pointer;transition:all 0.12s;font-weight:600;display:flex;align-items:center;gap:8px;min-width:64px;">
          <i class="fas fa-check-double" style="color:#3b82f6;font-size:12px;"></i>
          <span class="btn-text">Mark all</span>
        </button>

        <button onclick="empRefreshNotifications(event)" aria-label="Refresh notifications" class="notif-btn notif-refresh" style="background:#3b82f6; color:#fff; border:none; border-radius:999px; padding:6px 10px; cursor:pointer; font-size:13px; font-weight:600; display:flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(59,130,246,0.12); transition:transform .08s ease; min-width:72px;">
          <i class="fas fa-sync-alt" style="font-size:12px; transform:rotate(0deg);"></i>
          <span class="btn-text">Refresh</span>
        </button>
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
        if (!notifications.length){ list.innerHTML = '<li class="notif-empty" style="padding:40px 20px; text-align:center; color:#94a3b8;"><i class="fas fa-bell-slash" style="font-size:48px; color:#cbd5e0; margin-bottom:12px; display:block;"></i><div style="font-size:16px; font-weight:500; color:#64748b;">No notifications yet</div><div style="font-size:13px; margin-top:4px;">You\'re all caught up!</div></li>'; return; }
        list.innerHTML = notifications.map(n => empRenderNotifItem(n)).join('');
      })
      .catch(() => {});
  }

  function empRenderNotifItem(n){
    const icon = (function(){
      switch(n.type){
        case 'success': return 'check-circle';
        case 'error': return 'times-circle';
        case 'warning': return 'exclamation-triangle';
        case 'info': return 'info-circle';
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
    
    const iconColor = (function(){
      switch(n.type){
        case 'success': return '#28a745';
        case 'error': return '#dc3545';
        case 'warning': return '#ffa500';
        case 'info': return '#17a2b8';
        case 'new_application': return '#5B9BD5';
        default: return '#648EB5';
      }
    })();
    const readClass = n.read ? '' : 'unread';
    const bgColor = n.read ? '#fff' : '#f0f7ff';
    const createdAt = n.created_at ? new Date(n.created_at).toLocaleString('en-US', { month: 'numeric', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true }) : '';
    const hasLink = n.link ? ' style="cursor:pointer;"' : '';
    const linkIndicator = n.link ? '<i class="fas fa-chevron-right" style="color:#cbd5e0; font-size:14px; margin-left:auto; flex-shrink:0;"></i>' : '';
    return `
      <li class="notif-item ${readClass}" onclick="showEmpNotificationDetail(${n.id})"${hasLink} style="padding:24px 20px; display:flex; align-items:flex-start; gap:20px; border-bottom:1px solid #f0f0f0; cursor:pointer; transition:all 0.2s; background:${bgColor}; margin-bottom:8px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${bgColor}'">
        <div style="width:48px; height:48px; background:${iconColor}15; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="fas fa-${icon}" style="color:${iconColor}; font-size:20px;"></i>
        </div>
        <div style="flex:1; min-width:0; overflow:hidden;">
          <div style="font-weight:700; font-size:14px; color:#2c3e50; margin-bottom:12px; line-height:1.3;">${escapeHtml(n.title || '')}</div>
          <div style="color:#64748b; font-size:13px; line-height:1.5; margin-bottom:14px; word-wrap:break-word; overflow-wrap:break-word;">${escapeHtml(n.message || '')}</div>
          <div class="meta" style="font-size:10px; color:#a0aec0;">${createdAt}</div>
        </div>
        ${linkIndicator}
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
        if (!notif) {
          console.error('Notification not found:', notifId);
          return;
        }
        
        console.log('Notification clicked:', notif);
        console.log('Has link?', notif.link);
        
        // If notification has a link, redirect to it
        if (notif.link && notif.link.trim() !== '') {
          console.log('Redirecting to:', notif.link);
          window.location.href = notif.link;
          return;
        }
        
        console.log('No link, showing modal');
        
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
          <div style="background:white; border-radius:16px; width:90%; max-width:700px; max-height:85vh; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3); animation:modalSlideIn 0.3s ease;">
            <div style="background:linear-gradient(135deg, #648EB5 0%, #4E8EA2 100%); padding:30px; color:white; position:relative;">
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
            <div style="padding:30px; max-height:calc(85vh - 150px); overflow-y:auto;">
              <h4 style="margin:0 0 10px 0; color:#333; font-size:18px;">${escapeHtml(notif.title || 'Notification')}</h4>
              <p style="color:#555; line-height:1.6; margin:0;">${escapeHtml(notif.message || '')}</p>
              ${additionalInfo}
            </div>
            <div style="padding:25px 30px; border-top:1px solid #eee; display:flex; justify-content:flex-end; gap:10px;">
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