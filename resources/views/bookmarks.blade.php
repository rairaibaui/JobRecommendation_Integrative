@extends('jobseeker.layouts.base')

@section('title', 'Bookmarks - Job Portal Mandaluyong')
@php $pageTitle = 'JOB SEEKER PORTAL'; @endphp

@section('content')
    
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
                        <div class="job-card" 
                             data-job-id="{{ $job->id ?? '' }}"
                             data-title="{{ $job->title }}" 
                             data-location="{{ $job->location ?? '' }}" 
                             data-type="{{ $job->type ?? '' }}" 
                             data-salary="{{ $job->salary ?? '' }}" 
                             data-description="{{ $job->description ?? '' }}" 
                             data-skills='@json($job->skills ?? [])'
                             data-company="{{ $job->company ?? '' }}"
                             data-employer-name="{{ $job->employer_name ?? '' }}"
                             data-employer-email="{{ $job->employer_email ?? '' }}"
                             data-employer-phone="{{ $job->employer_phone ?? '' }}"
                             data-posted-date="{{ $job->posted_date ?? '' }}">
                            <div class="job-title">{{ $job->title }}</div>

                            <!-- Required Skills - Visible at Top -->
                            <div style="margin: 12px 0;">
                                <h4 style="margin: 0 0 8px 0; color: #495057; font-size: 14px; font-weight: 600;">
                                    <i class="fas fa-tools" style="color: #648EB5;"></i> Required Skills
                                </h4>
                                <div class="job-skills" style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @if(!empty($job->skills))
                                        @foreach($job->skills as $skill)
                                            <span class="skill" style="background: linear-gradient(135deg, #648EB5, #7a9cc6); color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">{{ $skill }}</span>
                                        @endforeach
                                    @else
                                        <span class="skill" style="background: #e9ecef; color: #6c757d; padding: 6px 14px; border-radius: 20px; font-size: 13px;">No specific skills listed</span>
                                    @endif
                                </div>
                            </div>

                            <div class="job-preview">
                                <div class="job-location"><i class="fas fa-map-marker-alt"></i> {{ $job->location ?? 'N/A' }}</div>
                                <div class="job-type"><i class="fas fa-briefcase"></i> {{ $job->type ?? 'Full-time' }}</div>
                                <div class="job-salary"><i class="fas fa-money-bill-wave"></i> {{ $job->salary ?? 'Negotiable' }}</div>
                            </div>

                            <!-- Company & Contact Information - Always Visible -->
                            <div class="employer-info" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 16px; border-radius: 8px; margin: 16px 0; border-left: 4px solid #648EB5; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <h4 style="margin: 0 0 12px 0; color: #648EB5; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-building"></i> Company & Contact Information
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px; font-size: 14px;">
                                    @if(!empty($job->company))
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-briefcase" style="color: #648EB5; width: 18px;"></i>
                                            <span><strong>Company:</strong> {{ $job->company }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($job->employer_name))
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-user-tie" style="color: #648EB5; width: 18px;"></i>
                                            <span><strong>Contact Person:</strong> {{ $job->employer_name }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($job->employer_email))
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-envelope" style="color: #648EB5; width: 18px;"></i>
                                            <span><strong>Email:</strong> <a href="mailto:{{ $job->employer_email }}" style="color: #648EB5; text-decoration: none; font-weight: 500;">{{ $job->employer_email }}</a></span>
                                        </div>
                                    @endif
                                    @if(!empty($job->employer_phone))
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-phone" style="color: #648EB5; width: 18px;"></i>
                                            <span><strong>Phone:</strong> <a href="tel:{{ $job->employer_phone }}" style="color: #648EB5; text-decoration: none; font-weight: 500;">{{ $job->employer_phone }}</a></span>
                                        </div>
                                    @endif
                                    @if(!empty($job->posted_date))
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-calendar" style="color: #648EB5; width: 18px;"></i>
                                            <span><strong>Posted:</strong> {{ $job->posted_date }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="job-details">
                                <div class="job-description">
                                    <h4>Job Description</h4>
                                    {{ $job->description ?? '' }}
                                </div>
                            </div>

                            <div class="job-actions">
                                <button class="view-details btn-details" onclick="toggleDetails(this)">
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
 
@endsection

@push('scripts')
<script>
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

    function toggleDetails(button) {
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

function showToast(msg, type){
  const el = document.createElement('div');
  el.textContent = msg; el.style.cssText = 'position:fixed; top:20px; right:20px; padding:12px 24px; border-radius:10px; color:#fff; z-index:10000; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-weight:600;';
  el.style.background = type==='success' ? '#28a745' : type==='info' ? '#648EB5' : '#dc3545';
  document.body.appendChild(el); setTimeout(()=>{ el.remove(); }, 2000);
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
            showToast(data.message || 'Failed to remove bookmark', 'error');
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

        showToast('Removed from bookmarks', 'info');
    })
    .catch((error) => {
        console.error('Error removing bookmark:', error);
        button.disabled = false;
        showToast('Error removing bookmark', 'error');
    });
}
</script>
@endpush