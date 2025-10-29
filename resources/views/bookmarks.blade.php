@extends('layouts.recommendation')

@section('content')
    <div class="main">
        <div class="top-navbar">
            <i class="fas fa-bars hamburger"></i>
            Job Portal - Mandaluyong
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

                                <button class="bookmark-btn" onclick="removeBookmark(this)" title="Remove bookmark" data-title="{{ $job->title }}">
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
function getCsrfToken(){ return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }

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
    messageDiv.style.transform = 'translateY(-20px)';
    messageDiv.style.opacity = '0';
    messageDiv.style.transition = 'all 0.3s ease';
    switch(type) { case 'success': messageDiv.style.backgroundColor = '#4CAF50'; break; case 'info': messageDiv.style.backgroundColor = '#2196F3'; break; case 'error': messageDiv.style.backgroundColor = '#f44336'; break; default: messageDiv.style.backgroundColor = '#2196F3'; }
    document.body.appendChild(messageDiv);
    setTimeout(() => { messageDiv.style.transform = 'translateY(0)'; messageDiv.style.opacity = '1'; }, 10);
    setTimeout(() => { messageDiv.style.transform = 'translateY(-20px)'; messageDiv.style.opacity = '0'; setTimeout(() => messageDiv.remove(), 300); }, 2700);
}

function removeBookmark(button){
    const card = button.closest('.job-card');
    const title = card.dataset.title;

    fetch("{{ route('bookmark.remove') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
        body: JSON.stringify({ job: { title: title } })
    }).then(res => res.json()).then(data => {
        if(data.success){
            card.style.transition = 'all 0.3s ease'; card.style.opacity = '0'; card.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                card.remove();
                const remainingCards = document.querySelectorAll('.job-card');
                if (remainingCards.length === 0) { location.reload(); }
                const countElement = document.querySelector('.recommendation-header + p');
                if (countElement) { const currentCount = remainingCards.length; countElement.textContent = `Showing ${currentCount} saved ${currentCount === 1 ? 'job' : 'jobs'}`; }
            }, 300);
            showMessage('Removed from bookmarks', 'info');
        } else { showMessage('Failed to remove bookmark', 'error'); }
    }).catch(() => showMessage('Error removing bookmark', 'error'));
}
</script>
@endpush