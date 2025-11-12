@extends('jobseeker.layouts.base')

@section('title', 'Recommendations - Job Portal Mandaluyong')
@php $pageTitle = 'JOB SEEKER PORTAL'; @endphp

@section('content')
    @include('partials.trust-banner')

    <!-- All Job Recommendations (unfiltered) -->
    <div class="card-large" style="background:#FFF; margin-top: 0;">
        <div class="recommendation-header">
            <h3>Job Recommendations</h3>
            <p>All available job postings</p>
        </div>
        <p style="font-family: 'Roboto', sans-serif; font-size: 20px; color: #333; margin-bottom: 10px;">
            Showing {{ count($allJobs ?? []) }} {{ Str::plural('job', count($allJobs ?? [])) }}
        </p>
    <div style="display:flex; flex-direction: column; gap: 20px;">
            @foreach(($allJobs ?? []) as $job)
            <div class="job-card" 
                 data-job-id="{{ $job['id'] ?? '' }}"
                 data-title="{{ $job['title'] }}" 
                 data-location="{{ $job['location'] ?? '' }}" 
                 data-type="{{ $job['type'] ?? '' }}" 
                 data-salary="{{ $job['salary'] ?? '' }}" 
                 data-description="{{ $job['description'] ?? '' }}" 
                 data-skills='@json($job['skills'] ?? [])'
                 data-company="{{ $job['company'] ?? '' }}"
                 data-employer-name="{{ $job['employer_name'] ?? '' }}"
                 data-employer-email="{{ $job['employer_email'] ?? '' }}"
                 data-employer-phone="{{ $job['employer_phone'] ?? '' }}"
                 data-posted-date="{{ $job['posted_date'] ?? '' }}">
                <div class="job-title">{{ $job['title'] }}</div>

                <!-- Required Skills - Visible at Top -->
                <div style="margin: 12px 0;">
                    <h4 style="margin: 0 0 8px 0; color: #495057; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-tools" style="color: #648EB5;"></i> Required Skills
                    </h4>
                    <div class="job-skills" style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @if(!empty($job['skills']))
                            @foreach($job['skills'] as $skill)
                                <span class="skill" style="background: linear-gradient(135deg, #648EB5, #7a9cc6); color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">{{ $skill }}</span>
                            @endforeach
                        @else
                            <span class="skill" style="background: #e9ecef; color: #6c757d; padding: 6px 14px; border-radius: 20px; font-size: 13px;">No specific skills listed</span>
                        @endif
                    </div>
                </div>

                <div class="job-preview">
                    <div class="job-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $job['location'] ?? 'N/A' }}</span>
                    </div>
                    <div class="job-type">
                        <i class="fas fa-briefcase"></i>
                        <span>{{ $job['type'] ?? 'Full-time' }}</span>
                    </div>
                    <div class="job-salary">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>{{ $job['salary'] ?? 'Negotiable' }}</span>
                    </div>
                </div>

                <!-- Company Information - Always Visible -->
                <div class="employer-info" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 20px; border-radius: 8px; margin-top: 15px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                    <h4 style="margin: 0 0 15px 0; color: #648EB5; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-building"></i> Company & Contact Information
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px; font-size: 14px;">
                        @if(!empty($job['company']))
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-briefcase" style="color: #648EB5; width: 18px; font-size: 16px;"></i>
                                <div><strong style="color: #495057;">Company:</strong> <span style="color: #212529;">{{ $job['company'] }}</span></div>
                            </div>
                        @endif
                        @if(!empty($job['employer_name']))
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-user-tie" style="color: #648EB5; width: 18px; font-size: 16px;"></i>
                                <div><strong style="color: #495057;">Contact Person:</strong> <span style="color: #212529;">{{ $job['employer_name'] }}</span></div>
                            </div>
                        @endif
                        @if(!empty($job['employer_email']))
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-envelope" style="color: #648EB5; width: 18px; font-size: 16px;"></i>
                                <div><strong style="color: #495057;">Email:</strong> <a href="mailto:{{ $job['employer_email'] }}" style="color: #648EB5; text-decoration: none; font-weight: 500;">{{ $job['employer_email'] }}</a></div>
                            </div>
                        @endif
                        @if(!empty($job['employer_phone']))
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-phone" style="color: #648EB5; width: 18px; font-size: 16px;"></i>
                                <div><strong style="color: #495057;">Phone:</strong> <a href="tel:{{ $job['employer_phone'] }}" style="color: #648EB5; text-decoration: none; font-weight: 500;">{{ $job['employer_phone'] }}</a></div>
                            </div>
                        @endif
                        @if(!empty($job['posted_date']))
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-calendar" style="color: #648EB5; width: 18px; font-size: 16px;"></i>
                                <div><strong style="color: #495057;">Posted:</strong> <span style="color: #212529;">{{ $job['posted_date'] }}</span></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="job-details">
                    <div class="job-description">
                        <h4>Job Description</h4>
                        <p>{{ $job['description'] ?? 'No description available.' }}</p>
                    </div>
                </div>

                <div class="job-actions">
                    <button class="view-details btn-details" onclick="toggleDetails(this)">
                        <i class="fas fa-chevron-down"></i>
                        View Details
                    </button>
                    <button class="apply-btn" onclick="openApplyModal(this)" title="Apply using your profile">
                        <i class="fas fa-paper-plane"></i>
                        Apply
                    </button>
                    <button class="bookmark-btn" onclick="toggleBookmark(this)">
                        @php $isBookmarked = isset($bookmarkedTitles) && in_array($job['title'], $bookmarkedTitles); @endphp
                        <i class="{{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
 
@endsection

@push('scripts')
<script>
// Load expanded state from localStorage on page load
document.addEventListener('DOMContentLoaded', function() {
  const expandedJobs = JSON.parse(localStorage.getItem('expandedRecommendationJobs') || '[]');
  
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
    localStorage.setItem('expandedRecommendationJobs', JSON.stringify(expandedJobs));
}

function toggleBookmark(button) {
    const card = button.closest('.job-card');
    const job = {
        title: card.dataset.title || '',
        location: card.dataset.location || '',
        type: card.dataset.type || '',
        salary: card.dataset.salary || '',
        description: card.dataset.description || '',
        skills: card.dataset.skills ? JSON.parse(card.dataset.skills) : [],
        company: card.dataset.company || '',
        employer_name: card.dataset.employerName || '',
        employer_email: card.dataset.employerEmail || '',
        employer_phone: card.dataset.employerPhone || '',
        posted_date: card.dataset.postedDate || ''
    };
    const icon = button.querySelector('i');
    const isBookmarked = icon.classList.contains('fas');
    const url = isBookmarked ? "{{ route('bookmark.remove') }}" : "{{ route('bookmark.add') }}";

    button.disabled = true;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ job: job })
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (!ok) {
            showToast(data.message || 'Failed to update bookmark', 'error');
            return;
        }

        if (isBookmarked) {
            icon.classList.remove('fas');
            icon.classList.add('far');
            showToast('Removed from bookmarks', 'info');
        } else {
            icon.classList.remove('far');
            icon.classList.add('fas');
            showToast('Bookmarked — visible in Bookmarks page', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update bookmark', 'error');
    })
    .finally(() => { button.disabled = false; });
}
function showToast(msg, type){
  const el = document.createElement('div');
  el.textContent = msg; el.style.cssText = 'position:fixed; top:20px; right:20px; padding:12px 24px; border-radius:10px; color:#fff; z-index:10000; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-weight:600;';
  el.style.background = type==='success' ? '#28a745' : type==='info' ? '#648EB5' : '#dc3545';
  document.body.appendChild(el); setTimeout(()=>{ el.remove(); }, 2000);
}

function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }
</script>
@endpush
