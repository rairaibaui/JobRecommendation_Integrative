@extends('jobseeker.layouts.base')

@section('title', 'Dashboard - Job Portal Mandaluyong')
@php $pageTitle = 'DASHBOARD'; @endphp

@section('content')
  <div class="welcome">Welcome, {{ Auth::user()->first_name }}! 👋</div>

  @include('partials.trust-banner')

  @if(Auth::user()->user_type === 'job_seeker')
    @if(($needsProfileReminder ?? false) && !empty($profileMissing))
      <div class="notice notice-warning" style="background:#fff; border-left:5px solid #ff9800;">
        <i class="fas fa-user-edit" style="color:#ff9800;"></i>
        <div style="flex:1;">
          <div style="font-family:'Poppins', sans-serif; font-size:16px; font-weight:600; color:#263238;">Complete your profile to get better job matches</div>
          <div style="font-size:13px; color:#455a64; margin-top:4px;">Missing: {{ implode(', ', $profileMissing) }}</div>
          <div style="margin-top:10px; background:#f1f5f9; border-radius:8px; overflow:hidden; width:100%; max-width:420px;">
            <div style="height:8px; background:#e2e8f0; width:100%; position:relative;">
              <div style="height:8px; width: {{ $profileCompletePercent }}%; background:linear-gradient(90deg,#4CAF50,#81C784);"></div>
            </div>
            <div style="font-size:12px; color:#64748b; margin-top:6px;">Profile {{ $profileCompletePercent }}% complete</div>
          </div>
        </div>
        <div style="display:flex; gap:8px;">
          <a href="{{ route('settings') }}" class="btn btn-warning btn-sm" style="color:#7a4b00;">Complete Profile</a>
          <a href="{{ route('recommendation') }}" class="btn btn-sm" style="background:#fff; border:1px solid #ffcc80; color:#7a4b00;">See Matches</a>
        </div>
      </div>
    @endif

      {{-- Resume Verification Status --}}
      @if(Auth::user()->resume_file)
        @php
          $status = Auth::user()->resume_verification_status ?? 'pending';
          $score = Auth::user()->verification_score ?? 0;
          $flags = json_decode(Auth::user()->verification_flags ?? '[]', true) ?: [];
          
          // Remove "Missing Resume" flag if resume file exists (safeguard against stale data)
          $flags = array_filter($flags, function($flag) {
            return strtolower($flag) !== 'missing resume';
          });
          
          $notes = Auth::user()->verification_notes ?? '';
        
          $statusConfig = [
            'verified' => [
              'color' => '#28a745',
              'bg' => '#d4edda',
              'border' => '#28a745',
              'icon' => 'fa-check-circle',
              'title' => 'Resume Verified',
              'message' => 'Your resume is verified.'
            ],
            'needs_review' => [
              'color' => '#ff9800',
              'bg' => '#fff3cd',
              'border' => '#ff9800',
              'icon' => 'fa-exclamation-triangle',
              'title' => 'Resume Needs Review',
              'message' => 'Your resume requires admin review before approval.'
            ],
            'incomplete' => [
              'color' => '#dc3545',
              'bg' => '#f8d7da',
              'border' => '#dc3545',
              'icon' => 'fa-times-circle',
              'title' => 'Resume Incomplete',
              'message' => 'Your resume is missing important information.'
            ],
            'pending' => [
              'color' => '#17a2b8',
              'bg' => '#d1ecf1',
              'border' => '#17a2b8',
              'icon' => 'fa-clock',
              'title' => 'Verification Pending',
              'message' => 'Your resume is being analyzed...'
            ],
            'rejected' => [
              'color' => '#dc3545',
              'bg' => '#f8d7da',
              'border' => '#dc3545',
              'icon' => 'fa-ban',
              'title' => 'Resume Rejected',
              'message' => 'Your resume did not meet verification requirements.'
            ]
          ];
        
          $config = $statusConfig[$status] ?? $statusConfig['pending'];
        @endphp

        <div class="card" style="border-left: 4px solid {{ $config['border'] }}; background: {{ $config['bg'] }}; margin-bottom: 20px;">
          <div class="card-body" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start; gap: 20px;">
              <div style="flex: 1;">
                <h3 style="margin: 0 0 8px 0; color: {{ $config['color'] }}; display: flex; align-items: center; gap: 10px; font-size: 18px;">
                  <i class="fas {{ $config['icon'] }}"></i>
                  {{ $config['title'] }}
                </h3>
                <p style="margin: 0 0 12px 0; color: #555; font-size: 14px;">{{ $config['message'] }}</p>
              
                @if($score > 0)
                  <div style="margin-bottom: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                      <span style="font-size: 13px; color: #666; font-weight: 600;">Quality Score</span>
                      <span style="font-size: 13px; color: {{ $config['color'] }}; font-weight: 700;">{{ $score }}/100</span>
                    </div>
                    <div style="background: #fff; border-radius: 10px; height: 12px; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                      <div style="height: 100%; width: {{ $score }}%; background: linear-gradient(90deg, {{ $config['color'] }}, {{ $config['color'] }}aa); transition: width 0.3s ease;"></div>
                    </div>
                  </div>
                @endif
              
                @if(!empty($flags))
                  <div style="margin-bottom: 12px;">
                    <p style="margin: 0 0 8px 0; font-size: 13px; color: #666; font-weight: 600;">Issues Detected:</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                      @foreach($flags as $flag)
                        <span style="background: #fff; padding: 4px 10px; border-radius: 12px; font-size: 12px; color: #555; border: 1px solid rgba(0,0,0,0.1);">
                          <i class="fas fa-flag" style="font-size: 10px; margin-right: 4px;"></i>
                          {{ ucwords(str_replace('_', ' ', $flag)) }}
                        </span>
                      @endforeach
                    </div>
                  </div>
                @endif
              
                @if($notes)
                  <p style="margin: 12px 0 0 0; padding: 10px; background: #fff; border-radius: 6px; font-size: 13px; color: #666; border-left: 3px solid {{ $config['color'] }};">
                    <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                    {{ $notes }}
                  </p>
                @endif
        {{-- If resume is verified but email not verified, prompt user to verify email for additional benefits --}}
        @if($status === 'verified' && method_exists(Auth::user(), 'hasVerifiedEmail') && !Auth::user()->hasVerifiedEmail())
          <div style="margin-top:12px; padding:12px; background:#fff4e5; border-radius:8px; border-left:4px solid #f59e0b; color:#92400e; display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <div style="flex:1;">
              <strong style="display:block; font-size:14px; margin-bottom:6px;">Almost there — verify your email for more benefits</strong>
              <div style="font-size:13px;">Your resume has been approved by an administrator. Verify your email to unlock priority job matching and improved application visibility.</div>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
              <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:#f59e0b; color:white; border:none; padding:8px 12px; border-radius:6px;">Resend verification email</button>
              </form>
              <a href="{{ route('settings') }}" class="btn btn-sm" style="background:#fff; color:#92400e; border:1px solid #f59e0b; padding:8px 12px; border-radius:6px;">Change Email</a>
            </div>
          </div>
        @endif
              </div>
            
              <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                @if($status !== 'verified')
                  <a href="{{ route('settings') }}" class="btn btn-sm" style="background: {{ $config['color'] }}; color: white; white-space: nowrap; border: none;">
                    <i class="fas fa-upload"></i> Re-upload Resume
                  </a>
                @endif
                <a href="{{ asset('storage/' . Auth::user()->resume_file) }}" target="_blank" class="btn btn-secondary btn-sm" style="white-space: nowrap;">
                  <i class="fas fa-file-pdf"></i> View Resume
                </a>
              </div>
            </div>
          </div>
        </div>
      @endif

    @if(Auth::user()->employment_status === 'employed')
      <div class="d-flex align-items-center gap-2 mt-1 mb-1">
        <span class="badge badge-success"><i class="fas fa-briefcase"></i> Currently employed @if(Auth::user()->hired_by_company) at {{ Auth::user()->hired_by_company }} @endif</span>
        @if(Auth::user()->hired_date)
          <span class="badge badge-secondary"><i class="fas fa-calendar-alt"></i> since {{ Auth::user()->hired_date->format('M d, Y') }}</span>
        @endif
      </div>
    @else
      <div class="mt-1 mb-1">
        <span class="badge badge-info"><i class="fas fa-search"></i> Actively seeking opportunities</span>
      </div>
    @endif
  @endif

  <div class="stats-grid">
    <div class="stat-box">
      <h3><i class="fas fa-star text-primary"></i></h3>
      <p>Recommended Jobs</p>
      <div class="mt-1">
        <a href="{{ route('recommendation') }}" class="btn btn-primary btn-sm">View All Recommendations</a>
      </div>
    </div>
    <div class="stat-box">
      <h3><i class="fas fa-bookmark text-primary"></i></h3>
      <p>Bookmarked Jobs</p>
      <div class="mt-1">
        <a href="{{ route('bookmarks') }}" class="btn btn-primary btn-sm">View All Bookmarks</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Top Job Recommendations</h3>
      <span class="text-muted">Based on your profile and skills</span>
    </div>
    <div class="card-body">
      @if(count($jobs ?? []) > 0)
        <p class="mb-2">Showing {{ count($jobs) }} skill-matched {{ Str::plural('job', count($jobs)) }}</p>
        @foreach($jobs as $job)
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

            @if(isset($job['match_score']) && $job['match_score'] > 0)
              <div class="match-indicator" style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                <div class="badge badge-success" style="background:linear-gradient(135deg, #4CAF50, #45a049); color:white;">
                  <i class="fas fa-star"></i> {{ $job['match_score'] }}% Match
                </div>
                @if(isset($job['matching_skills']) && $job['matching_skills']->count() > 0)
                  <div class="text-muted" style="font-size:12px;">
                    <i class="fas fa-check-circle" style="color:#4CAF50;"></i>
                    Matches: {{ $job['matching_skills']->take(3)->implode(', ') }}
                    @if($job['matching_skills']->count() > 3)
                      +{{ $job['matching_skills']->count() - 3 }} more
                    @endif
                  </div>
                @endif
              </div>
            @endif

            <!-- Required Skills - Visible at Top -->
            <div style="margin: 12px 0;">
              <h4 style="margin: 0 0 8px 0; color: #495057; font-size: 14px; font-weight: 600;">
                <i class="fas fa-tools" style="color: #648EB5;"></i> Required Skills
              </h4>
              <div class="job-skills" style="display: flex; flex-wrap: wrap; gap: 8px;">
                @if(!empty($job['skills']))
                  @foreach($job['skills'] as $skill)
                    @php $isMatching = isset($job['matching_skills']) && $job['matching_skills']->contains(strtolower($skill)); @endphp
                    <span class="skill" style="background: {{ $isMatching ? 'linear-gradient(135deg, #4CAF50, #45a049)' : 'linear-gradient(135deg, #648EB5, #7a9cc6)' }}; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                      {{ $skill }} @if($isMatching)<i class="fas fa-check" style="margin-left:4px;"></i>@endif
                    </span>
                  @endforeach
                @else
                  <span class="skill" style="background: #e9ecef; color: #6c757d; padding: 6px 14px; border-radius: 20px; font-size: 13px;">No specific skills listed</span>
                @endif
              </div>
            </div>

            <div class="job-preview">
              <div class="job-location"><i class="fas fa-map-marker-alt"></i> <span>{{ $job['location'] ?? 'N/A' }}</span></div>
              <div class="job-type"><i class="fas fa-briefcase"></i> <span>{{ $job['type'] ?? 'Full-time' }}</span></div>
              <div class="job-salary"><i class="fas fa-money-bill"></i> <span>{{ $job['salary'] ?? 'Negotiable' }}</span></div>
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
              <div class="job-description">{{ $job['description'] ?? '' }}</div>
            </div>

            <div class="job-actions">
              <button class="view-details btn-details" onclick="toggleDetails(this)" data-job-id="{{ $job['id'] }}">
                <i class="fas fa-chevron-down"></i> View Details
              </button>
              <button class="apply-btn" onclick="openApplyModal(this)" title="Apply using your profile">
                <i class="fas fa-paper-plane"></i> Apply
              </button>
              <button class="bookmark-btn" data-job='@json($job)' onclick="toggleBookmark(this)">
                @php $isBookmarked = isset($bookmarkedTitles) && in_array($job['title'], $bookmarkedTitles); @endphp
                <i class="{{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark"></i>
              </button>
            </div>
          </div>
        @endforeach
      @else
        <p class="text-muted" style="font-style:italic;">No skill-matched jobs found. Complete your profile with skills to see personalized recommendations.</p>
      @endif
    </div>
  </div>

  @if(count($otherJobs ?? []) > 0)
    <div class="card mt-2">
      <div class="card-header">
        <h3 class="card-title">Other Recent Jobs</h3>
        <span class="text-muted">More opportunities posted recently</span>
      </div>
      <div class="card-body">
        <p class="mb-2">Showing {{ count($otherJobs) }} additional {{ Str::plural('job', count($otherJobs)) }}</p>
        @foreach($otherJobs as $job)
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
              <div class="job-location"><i class="fas fa-map-marker-alt"></i> <span>{{ $job['location'] ?? 'N/A' }}</span></div>
              <div class="job-type"><i class="fas fa-briefcase"></i> <span>{{ $job['type'] ?? 'Full-time' }}</span></div>
              <div class="job-salary"><i class="fas fa-money-bill"></i> <span>{{ $job['salary'] ?? 'Negotiable' }}</span></div>
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
              <div class="job-description">{{ $job['description'] ?? '' }}</div>
            </div>

            <div class="job-actions">
              <button class="view-details btn-details" onclick="toggleDetails(this)" data-job-id="{{ $job['id'] }}">
                <i class="fas fa-chevron-down"></i> View Details
              </button>
              <button class="apply-btn" onclick="openApplyModal(this)" title="Apply using your profile">
                <i class="fas fa-paper-plane"></i> Apply
              </button>
              <button class="bookmark-btn" data-job='@json($job)' onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

@endsection

@push('scripts')
<script>
  // Local expand/collapse with persistence
  document.addEventListener('DOMContentLoaded', function() {
    const expandedJobs = JSON.parse(localStorage.getItem('expandedDashboardJobs') || '[]');
    document.querySelectorAll('.job-card').forEach((card, index) => {
      if (expandedJobs.includes(index)) {
        const details = card.querySelector('.job-details');
        const button = card.querySelector('.btn-details');
        const icon = button ? button.querySelector('i') : null;
        if (details) {
          details.classList.add('expanded');
          if (button && icon) { icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); button.innerHTML = '<i class="fas fa-chevron-up"></i> Hide Details'; }
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
    const cards = Array.from(document.querySelectorAll('.job-card'));
    const expandedJobs = [];
    cards.forEach((card, index) => { if (card.querySelector('.job-details')?.classList.contains('expanded')) expandedJobs.push(index); });
    localStorage.setItem('expandedDashboardJobs', JSON.stringify(expandedJobs));
  }

  function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute('content'); }
  function toggleBookmark(button) {
    const job = JSON.parse(button.getAttribute('data-job') || '{}');
    const icon = button.querySelector('i');
    const isBookmarked = icon.classList.contains('fas');
    const url = isBookmarked ? "{{ route('bookmark.remove') }}" : "{{ route('bookmark.add') }}";
    button.disabled = true;
    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }, body: JSON.stringify({ job }) })
      .then(r => r.json().then(data => ({ ok: r.ok, data })))
      .then(({ ok, data }) => {
        if (!ok) { showToast(data.message || 'Failed to update bookmark', 'error'); return; }
        icon.classList.toggle('fas'); icon.classList.toggle('far');
        showToast(isBookmarked ? 'Removed from bookmarks' : 'Bookmarked — visible in Bookmarks page', isBookmarked ? 'info' : 'success');
      })
      .catch(() => showToast('Failed to update bookmark', 'error'))
      .finally(() => { button.disabled = false; });
  }
  function showToast(msg, type){
    const el = document.createElement('div');
    el.textContent = msg; el.style.cssText = 'position:fixed; top:20px; right:20px; padding:12px 24px; border-radius:10px; color:#fff; z-index:10000; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-weight:600;';
    el.style.background = type==='success' ? '#28a745' : type==='info' ? '#648EB5' : '#dc3545';
    document.body.appendChild(el); setTimeout(()=>{ el.remove(); }, 2000);
  }

  // Job Seeker guide visibility
  document.addEventListener('DOMContentLoaded', function(){
    try { const hide = localStorage.getItem('hideJobSeekerGuide') === '1'; const el = document.getElementById('jobSeekerGuide'); if (el && !hide) el.style.display = 'block'; } catch(_) {}
  });
  function dismissJobSeekerGuide(){ try { localStorage.setItem('hideJobSeekerGuide', '1'); } catch(_) {} const el = document.getElementById('jobSeekerGuide'); if (el) el.style.display = 'none'; }
</script>
@endpush

