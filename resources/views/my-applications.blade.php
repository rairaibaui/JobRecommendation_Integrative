@extends('layouts.recommendation')

@section('content')
<div class="main">
    <div class="top-navbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div style="display:flex; align-items:center; gap:12px;">
            <i class="fas fa-bars hamburger"></i>
            Job Portal - Mandaluyong
        </div>
        <div class="notif-wrapper" style="position:relative;">
            <div class="notification-bell" onclick="toggleNotifDropdown(event)" style="padding:8px;">
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

    <!-- My Applications -->
    <div class="card-large" style="background: #FFF;">
        @if(session('success'))
            <div style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:8px; margin-bottom:20px; border:1px solid #c3e6cb;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="recommendation-header">
            <h3>My Applications</h3>
            <p>Track your job applications and their status</p>
        </div>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['total'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Total</div>
            </div>
            <div style="background: linear-gradient(135deg, #FFA726 0%, #FB8C00 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['pending'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Pending</div>
            </div>
            <div style="background: linear-gradient(135deg, #42A5F5 0%, #1E88E5 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['reviewing'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Reviewing</div>
            </div>
            <div style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['for_interview'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">For Interview</div>
            </div>
            <div style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['interviewed'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Interviewed</div>
            </div>
            <div style="background: linear-gradient(135deg, #66BB6A 0%, #43A047 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['accepted'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Accepted</div>
            </div>
            <div style="background: linear-gradient(135deg, #EF5350 0%, #E53935 100%); padding: 20px; border-radius: 12px; text-align: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 28px; font-weight: bold; margin-bottom: 5px;">{{ $stats['rejected'] }}</div>
                <div style="font-size: 13px; opacity: 0.9;">Rejected</div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
            <button class="filter-btn active" onclick="filterApplications('all')" style="padding: 8px 16px; border: 2px solid #648EB5; background: #648EB5; color: white; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">All</button>
            <button class="filter-btn" onclick="filterApplications('pending')" style="padding: 8px 16px; border: 2px solid #FFA726; background: white; color: #FFA726; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">Pending</button>
            <button class="filter-btn" onclick="filterApplications('reviewing')" style="padding: 8px 16px; border: 2px solid #42A5F5; background: white; color: #42A5F5; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">Reviewing</button>
            <button class="filter-btn" onclick="filterApplications('for_interview')" style="padding: 8px 16px; border: 2px solid #17a2b8; background: white; color: #17a2b8; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">For Interview</button>
            <button class="filter-btn" onclick="filterApplications('interviewed')" style="padding: 8px 16px; border: 2px solid #ffc107; background: white; color: #ffc107; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">Interviewed</button>
            <button class="filter-btn" onclick="filterApplications('accepted')" style="padding: 8px 16px; border: 2px solid #66BB6A; background: white; color: #66BB6A; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">Accepted</button>
            <button class="filter-btn" onclick="filterApplications('rejected')" style="padding: 8px 16px; border: 2px solid #EF5350; background: white; color: #EF5350; border-radius: 20px; cursor: pointer; transition: all 0.3s; font-size: 14px; font-weight: 500;">Rejected</button>
        </div>

        <!-- Count -->
        <p style="font-family: 'Roboto', sans-serif; font-size: 18px; color: #333; margin-bottom: 15px;">
            Showing {{ $applications->count() }} {{ Str::plural('application', $applications->count()) }}
        </p>

        @if($applications->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @foreach($applications as $application)
                    <div class="application-card job-card" data-status="{{ $application->status }}">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                            <div style="flex: 1;">
                                <div class="job-title" style="margin-bottom: 10px;">{{ $application->job_title }}</div>
                                
                                <div class="job-preview" style="margin-bottom: 10px;">
                                    <div class="job-location">
                                        <i class="fas fa-building"></i>
                                        <span>{{ $application->company_name ?? 'Company Name' }}</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-calendar"></i>
                                        <span>Applied {{ $application->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($application->status_updated_at)
                                        <div class="job-salary">
                                            <i class="fas fa-clock"></i>
                                            <span>Updated {{ $application->status_updated_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Employer Contact Information -->
                                @if($application->jobPosting && $application->jobPosting->employer)
                                    <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 10px; font-size: 13px; border-left: 3px solid #648EB5;">
                                        <div style="font-weight: 600; color: #648EB5; margin-bottom: 6px;">
                                            <i class="fas fa-info-circle"></i> Employer Contact
                                        </div>
                                        <div style="display: grid; gap: 4px; color: #555;">
                                            @if($application->jobPosting->employer->first_name)
                                                <div><i class="fas fa-user-tie" style="width: 16px; color: #648EB5;"></i> <strong>Contact:</strong> {{ $application->jobPosting->employer->first_name }} {{ $application->jobPosting->employer->last_name }}</div>
                                            @endif
                                            @if($application->jobPosting->employer->email)
                                                <div><i class="fas fa-envelope" style="width: 16px; color: #648EB5;"></i> <strong>Email:</strong> <a href="mailto:{{ $application->jobPosting->employer->email }}" style="color: #648EB5; text-decoration: none;">{{ $application->jobPosting->employer->email }}</a></div>
                                            @endif
                                            @if($application->jobPosting->employer->phone_number)
                                                <div><i class="fas fa-phone" style="width: 16px; color: #648EB5;"></i> <strong>Phone:</strong> <a href="tel:{{ $application->jobPosting->employer->phone_number }}" style="color: #648EB5; text-decoration: none;">{{ $application->jobPosting->employer->phone_number }}</a></div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                                                                @php
                                                                        $statusColors = [
                                                                                'pending' => ['bg' => '#fff3cd', 'text' => '#856404'],
                                                                                'reviewing' => ['bg' => '#cfe2ff', 'text' => '#084298'],
                                                                                'for_interview' => ['bg' => '#d1ecf1', 'text' => '#0c5460'],
                                                                                'interviewed' => ['bg' => '#fff3cd', 'text' => '#856404'],
                                                                                'accepted' => ['bg' => '#d1e7dd', 'text' => '#0f5132'],
                                                                                'rejected' => ['bg' => '#f8d7da', 'text' => '#842029']
                                                                        ];
                                                                        $color = $statusColors[$application->status] ?? ['bg' => '#e0e0e0', 'text' => '#666'];
                                                                @endphp
                                <span style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: capitalize; display: inline-block; background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                                                        {{ $application->status === 'for_interview' ? 'For Interview' : ucfirst($application->status) }}
                                </span>

                                                                @if(in_array($application->status, ['for_interview','interviewed']) && ($application->interview_date || $application->interview_location || $application->interview_notes))
                                                                    <div style="margin-top:8px; font-size:12px; color:#555; text-align:right;">
                                                                        @if($application->interview_date)
                                                                            <div><i class="fas fa-calendar-alt" style="color:#17a2b8;"></i> {{ \Carbon\Carbon::parse($application->interview_date)->format('M d, Y h:i A') }}</div>
                                                                        @endif
                                                                        @if($application->interview_location)
                                                                            <div><i class="fas fa-map-marker-alt" style="color:#17a2b8;"></i> {{ $application->interview_location }}</div>
                                                                        @endif
                                                                        @if($application->interview_notes)
                                                                            <div style="color:#666;"><i class="fas fa-sticky-note" style="color:#17a2b8;"></i> {{ $application->interview_notes }}</div>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                
                                <!-- Withdraw/Delete button -->
                                <form method="POST" action="{{ route('my-applications.destroy', $application) }}" onsubmit="return confirm('Are you sure you want to withdraw this application? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
                                        <i class="fas fa-trash"></i> Withdraw
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: #999;">
                <i class="fas fa-inbox" style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                <h3 style="color: #666; margin-bottom: 10px; font-size: 20px;">No Applications Yet</h3>
                <p style="color: #999; margin-bottom: 20px;">You haven't applied to any jobs yet. Start exploring opportunities!</p>
                <a href="{{ route('recommendation') }}" style="display: inline-block; padding: 12px 24px; background: #648EB5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s;">
                    <i class="fas fa-search"></i> Browse Jobs
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .application-card {
        transition: transform 200ms ease, box-shadow 200ms ease;
    }

    .application-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .filter-btn.active {
        background: #648EB5 !important;
        color: white !important;
        border-color: #648EB5 !important;
    }
</style>

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
    function filterApplications(status) {
        const cards = document.querySelectorAll('.application-card');
        const buttons = document.querySelectorAll('.filter-btn');
        
        // Update active button
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filter cards
        cards.forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
