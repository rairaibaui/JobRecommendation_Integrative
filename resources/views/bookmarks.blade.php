@extends('layouts.recommendation')

@section('content')
    <div class="main">
        <div class="top-navbar" style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:12px;">
                <i class="fas fa-bars hamburger"></i>
                Job Portal - Mandaluyong
            </div>
            <div class="notif-wrapper" style="position:relative;">
                <div class="notification-bell" onclick="toggleNotifDropdown(event)" style="padding:8px; cursor:pointer;">
                    <i class="fas fa-bell"></i>
                    @php $unreadCount = Auth::user()->unreadNotifications()->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="badge" id="notifCount" style="position:absolute; top:0; right:0; background:#ff4757; color:#fff; border-radius:50%; padding:2px 6px; font-size:10px; font-weight:700;">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div id="notifDropdown" class="notif-dropdown" style="display:none; position:absolute; top:52px; right:0; width:360px; max-height:420px; overflow:auto; background:#fff; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,0.18); z-index:1100; font-size:14px; line-height:1.35;" data-loaded="0">
                    <div class="notif-header" style="padding:10px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eee; font-weight:600;">
                        <span>Notifications</span>
                        <button onclick="markAllNotificationsRead(event)" style="background:#eee;color:#333;border:1px solid #ddd;border-radius:6px;padding:6px 10px;font-size:12px;cursor:pointer;">Mark all as read</button>
                    </div>
                    <ul class="notif-list" id="notifList" style="list-style:none; margin:0; padding:0;">
                        <li class="notif-empty" style="padding:20px; text-align:center; color:#777;">Loading...</li>
                    </ul>
                    <div class="notif-actions" style="padding:8px 12px; display:flex; justify-content:flex-end;">
                        <button onclick="refreshNotifications(event)" style="background:#4E8EA2; color:#fff; border:none; border-radius:8px; padding:8px 12px; cursor:pointer; font-size:12px;">Refresh</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookmarked Jobs -->
        <div class="card-large" style="background: #FFF;">
            <div class="recommendation-header">
                <h3>Bookmarked Jobs</h3>
                <p>Jobs you've saved for later.</p>
            </div>

            <!-- Count of bookmarks -->
            <p style="font-family: 'Roboto', sans-serif; font-size: 20px; color: #333; margin-bottom: 10px;">
                Showing {{ count($bookmarks) }} saved {{ Str::plural('job', count($bookmarks)) }}
            </p>

            @if(empty($bookmarks) || count($bookmarks) === 0)
                <div class="no-bookmarks">
                    <i class="fas fa-bookmark no-bookmarks-icon"></i>
                    <h4 class="no-bookmarks-title">No Bookmarks</h4>
                    <p class="no-bookmarks-text">You haven't saved any jobs yet</p>
                    <div class="no-bookmarks-rectangle">
                        <a href="{{ route('recommendation') }}" class="browse-link">
                            Browse Job Recommendations
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach($bookmarks as $job)
                        <div class="job-card" data-title="{{ $job->title }}" data-location="{{ $job->location ?? '' }}" data-type="{{ $job->type ?? '' }}" data-salary="{{ $job->salary ?? '' }}" data-description="{{ $job->description ?? '' }}" data-skills='@json($job->skills ?? [])'>
                            <div class="job-title">{{ $job->title }}</div>

                            <div class="job-preview">
                                <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job->location ?? 'N/A' }}</div>
                                <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job->type ?? 'Full-time' }}</div>
                                <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job->salary ?? 'Negotiable' }}</div>
                            </div>

                            <div class="job-details">
                                <div class="job-description">
                                    <h4>Job Description</h4>
                                    {{ $job->description ?? '' }}
                                </div>

                                <div class="skills-section">
                                    <h4>Required Skills</h4>
                                    <div class="job-skills">
                                        @if(!empty($job->skills))
                                            @foreach($job->skills as $skill)
                                                <div class="skill">{{ $skill }}</div>
                                            @endforeach
                                        @else
                                            <div class="skill">No specific skills listed</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="job-actions">
                                <button class="view-details" onclick="toggleJobDetails(this)">
                                    <i class="fas fa-chevron-down"></i> View Details
                                </button>

                                <button class="apply-btn" onclick="openApplyModal(this)" title="Apply using your profile">
                                    <i class="fas fa-paper-plane"></i> Apply
                                </button>

                                <button class="bookmark-btn" onclick="removeBookmark(this)" title="Remove bookmark">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Job Card Styles */
        .job-card {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 200ms ease, box-shadow 200ms ease;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }

        .job-title {
            font-size: 20px;
            font-weight: 500;
            color: #333;
            margin-bottom: 15px;
        }

        .job-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
            color: #666;
        }

        .job-location, .job-type, .job-salary {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .job-preview i {
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
            margin-bottom: 20px;
        }

        .job-description h4 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .job-description p {
            color: #666;
            line-height: 1.6;
        }

        .skills-section h4 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .job-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .skill {
            background: #648EB5;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
        }

        .job-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .view-details, .apply-btn, .bookmark-btn {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
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
            width: 44px;
            height: 44px;
            padding: 0;
            justify-content: center;
        }

        .bookmark-btn i.fas {
            color: #FFD166;
        }

        .job-actions button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Empty state styling */
        .no-bookmarks {
            width: 100%;
            max-width: 1200px;
            height: 500px;
            margin: 0 auto;
            border-radius: 8px;
            background: #FFF;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 4px rgba(0,0,0,0.25);
        }

        .no-bookmarks-icon {
            font-size: 115px;
            color: #648EB5;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .no-bookmarks-title {
            font-family: 'Poppins', sans-serif;
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }

        .no-bookmarks-text {
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            color: #666;
            margin-bottom: 30px;
        }

        .no-bookmarks-rectangle {
            border-radius: 8px;
            background: #648EB5;
            box-shadow: 0 6px 4px 0 rgba(0, 0, 0, 0.25);
            padding: 12px 24px;
            transition: transform 200ms ease, box-shadow 200ms ease;
        }

        .no-bookmarks-rectangle:hover { transform: translateY(-2px); box-shadow: 0 8px 8px rgba(0,0,0,0.2); }

        .browse-link { color: #fff; text-decoration: none; font-family: 'Roboto', sans-serif; font-weight: 500; font-size: 16px; display: flex; align-items: center; gap: 8px; }
        .browse-link i { font-size: 14px; }
    </style>
@endsection

@push('scripts')
<script>
// Notifications (shared minimal logic)
function toggleNotifDropdown(e){
    e.stopPropagation();
    const dd = document.getElementById('notifDropdown');
    const visible = dd.style.display === 'block';
    if (!visible && dd.dataset.loaded !== '1') { loadNotifications(); }
    dd.style.display = visible ? 'none' : 'block';
}
document.addEventListener('click', function(e){
    const dd = document.getElementById('notifDropdown');
    if (!dd) return;
    if (dd.style.display === 'block' && !dd.contains(e.target)) dd.style.display = 'none';
});
function loadNotifications(){
    const list = document.getElementById('notifList');
    list.innerHTML = '<li class="notif-empty" style="padding:20px; text-align:center; color:#777;">Loading...</li>';
    fetch("{{ route('notifications.list') }}")
        .then(r=>r.json())
        .then(({success, unread, notifications}) => {
            if(!success){ list.innerHTML = '<li class="notif-empty" style="padding:20px; text-align:center; color:#777;">Failed to load</li>'; return; }
            const badge = document.getElementById('notifCount');
            if (badge) badge.textContent = unread; else if (unread > 0) {
                const bell = document.querySelector('.notification-bell');
                const span = document.createElement('span');
                span.className = 'badge'; span.id = 'notifCount'; span.textContent = unread;
                span.style.cssText = 'position:absolute; top:0; right:0; background:#ff4757; color:#fff; border-radius:50%; padding:2px 6px; font-size:10px; font-weight:700;';
                bell.appendChild(span);
            }
            if (!notifications.length){ list.innerHTML = '<li class="notif-empty" style="padding:20px; text-align:center; color:#777;">No notifications yet</li>'; return; }
            list.innerHTML = notifications.map(n => renderNotifItem(n)).join('');
            document.getElementById('notifDropdown').dataset.loaded = '1';
        })
        .catch(()=> list.innerHTML = '<li class="notif-empty" style="padding:20px; text-align:center; color:#777;">Network error</li>');
}
function renderNotifItem(n){
    const icon = n.type === 'application_status_changed' ? 'fa-clipboard-check' : 'fa-paper-plane';
    const isUnread = n.read ? '' : 'background:#f7fbff;';
    const when = new Date(n.created_at).toLocaleString();
    return `<li class="notif-item" style="padding:12px 16px; display:flex; gap:10px; border-bottom:1px solid #f3f3f3; ${isUnread}">
        <i class="fas ${icon}" style="color:#648EB5; margin-top:3px;"></i>
        <div>
            <div style="font-weight:600; color:#333;">${escapeHtml(n.title || 'Notification')}</div>
            <div style="color:#555; font-size:13px;">${escapeHtml(n.message || '')}</div>
            <div style="font-size:12px; color:#888; margin-top:4px;">${when}</div>
        </div>
    </li>`;
}
function escapeHtml(str){ return String(str).replace(/[&<>\"]+/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s])); }
function markAllNotificationsRead(e){
    e.stopPropagation();
    fetch("{{ route('notifications.markAllRead') }}", { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }})
      .then(r=>r.json()).then(({success})=>{
        if(success){
            const items = document.querySelectorAll('#notifList .notif-item');
            items.forEach(li => li.style.background = 'transparent');
            const badge = document.getElementById('notifCount');
            if (badge) badge.remove();
        }
      });
}
function refreshNotifications(e){ e.stopPropagation(); loadNotifications(); }

    // Load expanded state from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
      const expandedJobs = JSON.parse(localStorage.getItem('expandedBookmarkJobs') || '[]');
      
      document.querySelectorAll('.job-card').forEach((card, index) => {
        if (expandedJobs.includes(index)) {
          const details = card.querySelector('.job-details');
          const button = card.querySelector('.btn-details');
          const icon = button ? button.querySelector('i') : null;
          
          if (details) {
            details.classList.add('expanded');
            if (button && icon) {
              icon.classList.remove('fa-chevron-down');
              icon.classList.add('fa-chevron-up');
              button.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Details';
            }
          }
        }
      });
    });

    function toggleJobDetails(button) {
        const jobCard = button.closest('.job-card');
        const details = jobCard.querySelector('.job-details');
        const icon = button.querySelector('i');
        
        if (details.classList.contains('expanded')) {
            details.classList.remove('expanded');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            button.innerHTML = '<i class="fas fa-chevron-down"></i> View Details';
        } else {
            details.classList.add('expanded');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            button.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Details';
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
        localStorage.setItem('expandedBookmarkJobs', JSON.stringify(expandedJobs));
    }

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

function removeBookmark(button){
    const card = button.closest('.job-card');
    const title = card.dataset.title;

    // Disable button while request is in-flight
    button.disabled = true;

    fetch("{{ route('bookmark.remove') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
        body: JSON.stringify({ job: { title: title } })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if(!ok) {
            button.disabled = false;
            showMessage(data.message || 'Failed to remove bookmark', 'error');
            return;
        }

        // Animate card out
        card.style.transition = 'all 0.3s ease';
        card.style.opacity = '0';
        card.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            card.remove();

            // Check if any bookmarks remain
            const remainingCards = document.querySelectorAll('.job-card');
            if (remainingCards.length === 0) {
                // Reload page to show empty state
                location.reload();
            } else {
                // Update count
                const countElement = document.querySelector('.recommendation-header + p');
                if (countElement) {
                    const currentCount = remainingCards.length;
                    countElement.textContent = `Showing ${currentCount} saved ${currentCount === 1 ? 'job' : 'jobs'}`;
                }
            }
        }, 300);

        showMessage('Removed from bookmarks', 'info');
    })
    .catch((error) => {
        console.error('Error removing bookmark:', error);
        button.disabled = false;
        showMessage('Error removing bookmark', 'error');
    });
}
</script>
@endpush