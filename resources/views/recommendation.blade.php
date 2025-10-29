@extends('layouts.recommendation')

@section('content')
<div class="main">
    <div class="top-navbar">
        <i class="fas fa-bars hamburger"></i>
        Job Portal - Mandaluyong
    </div>

    <!-- Top Job Recommendations -->
    <div class="card-large" style="background: #FFF;">
        <div class="recommendation-header">
            <h3>Job Recommendations</h3>
            <p>Jobs matched to your skills and preferences.</p>
        </div>

        <!-- Search Bar Container -->
        <div style="width: 898px; height: 74px; flex-shrink: 0; border-radius: 8px; background: #FFF; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; padding: 0 20px;">
            <div class="inner-search-box" style="width: 850px; height: 50px; background: #F5F5F5; border-radius: 8px; display: flex; align-items: center; padding: 0 15px; border: 2px solid #648EB5; transition: all 0.3s ease;">
                <i class="fas fa-search" style="font-size: 20px; color: #648EB5; margin-right: 10px;"></i>
                <input type="text" placeholder="Search for jobs..." style="flex: 1; border: none; outline: none; background: transparent; font-size: 18px; font-family: 'Roboto', sans-serif;">
            </div>
        </div>

        <p style="font-family: 'Roboto', sans-serif; font-size: 20px; color: #333; margin-bottom: 10px;">Showing {{ count($jobs) }} {{ Str::plural('job', count($jobs)) }}</p>

        <!-- Job list (stacked like dashboard) -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($jobs as $job)
            <div class="job-card" data-title="{{ $job['title'] }}" data-location="{{ $job['location'] ?? '' }}" data-type="{{ $job['type'] ?? '' }}" data-salary="{{ $job['salary'] ?? '' }}" data-description="{{ $job['description'] ?? '' }}" data-skills='@json($job['skills'] ?? [])'>
                <div class="job-title">{{ $job['title'] }}</div>

                <div class="job-preview">
                    <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job['location'] ?? '' }}</div>
                    <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job['type'] ?? '' }}</div>
                    <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job['salary'] ?? '' }}</div>
                </div>

                <div class="job-details">
                    <div class="job-description">
                        <h4>Job Description</h4>
                        {{ $job['description'] ?? '' }}
                    </div>

                    <div class="skills-section">
                        <h4>Required Skills</h4>
                        <div class="job-skills">
                            @if(!empty($job['skills']))
                                @foreach($job['skills'] as $skill)
                                    <div class="skill">{{ $skill }}</div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="job-actions">
                    <button class="view-details" onclick="toggleJobDetails(this)">
                        <i class="fas fa-chevron-down"></i> View Details
                    </button>

                    <button class="bookmark-btn" onclick="toggleBookmark(this)" title="{{ in_array($job['title'], $bookmarkedTitles ?? []) ? 'Unbookmark this job' : 'Bookmark this job' }}" data-title="{{ $job['title'] }}">
                        <i class="{{ in_array($job['title'], $bookmarkedTitles ?? []) ? 'fas' : 'far' }} fa-bookmark"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.inner-search-box:hover { border-color: #648EB5; box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
.inner-search-box:focus-within { border-color: #648EB5; box-shadow: 0 6px 12px rgba(0,0,0,0.08); }
</style>
@endsection

@push('scripts')
<script>
function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

function toggleBookmark(button) {
    const icon = button.querySelector('i');
    const jobCard = button.closest('.job-card');
    const title = button.dataset.title || jobCard.dataset.title;
    const job = {
        title: title,
        location: jobCard.dataset.location,
        type: jobCard.dataset.type,
        salary: jobCard.dataset.salary,
        description: jobCard.dataset.description,
        skills: JSON.parse(jobCard.dataset.skills || '[]')
    };

    const isBookmarking = icon.classList.contains('far');
    const url = isBookmarking ? "{{ route('bookmark.add') }}" : "{{ route('bookmark.remove') }}";

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
        body: JSON.stringify({ job: job })
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
        if (!ok) throw data;
        if (isBookmarking) {
            icon.classList.remove('far'); icon.classList.add('fas'); button.title = 'Unbookmark this job'; showMessage('Bookmarked — visible in Bookmarks page', 'success');
        } else {
            icon.classList.remove('fas'); icon.classList.add('far'); button.title = 'Bookmark this job'; showMessage('Removed from bookmarks', 'info');
        }
    })
    .catch(err => { console.error(err); showMessage('Failed to update bookmark', 'error'); });
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = message;
    messageDiv.style.position = 'fixed';
    messageDiv.style.top = '20px';
    messageDiv.style.right = '20px';
    messageDiv.style.padding = '10px 20px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.zIndex = '1000';
    messageDiv.style.color = 'white';
    switch(type) { case 'success': messageDiv.style.backgroundColor = '#4CAF50'; break; case 'info': messageDiv.style.backgroundColor = '#2196F3'; break; case 'error': messageDiv.style.backgroundColor = '#f44336'; break; default: messageDiv.style.backgroundColor = '#2196F3'; }
    document.body.appendChild(messageDiv);
    setTimeout(() => messageDiv.remove(), 3000);
}
</script>
@endpush
