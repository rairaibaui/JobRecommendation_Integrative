@php
    // Count unread notifications related to document uploads (resumes and permits)
    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->whereIn('type', ['resume_uploaded', 'resume_updated', 'permit_uploaded', 'permit_updated'])
        ->count();
@endphp

@if($unreadCount > 0)
    <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
@endif
