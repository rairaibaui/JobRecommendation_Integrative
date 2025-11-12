@extends('jobseeker.layouts.base')

@section('title', 'My Applications - Job Portal Mandaluyong')
@php $pageTitle = 'JOB SEEKER PORTAL'; @endphp

@section('content')
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

        <!-- Current Work Summary (Job Seeker) -->
        @if(($user->user_type ?? null) === 'job_seeker')
            <div style="background:#e8f0f7; border-left:4px solid #648EB5; padding:16px; border-radius:8px; margin-bottom:20px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:240px;">
                        <div style="font-weight:700; color:#334A5E; font-size:16px; margin-bottom:6px;">
                            <i class="fas fa-briefcase"></i>
                            @if(($user->employment_status ?? 'unemployed') === 'employed')
                                Currently Working
                            @else
                                Not Currently Employed
                            @endif
                        </div>
                        @if(($user->employment_status ?? 'unemployed') === 'employed')
                            <div style="color:#1E3A5F; font-size:14px;">
                                <div style="margin-bottom:4px;">
                                    <strong>Company:</strong>
                                    {{ $user->hired_by_company ?? ($currentHire->company_name ?? '—') }}
                                </div>
                                <div style="margin-bottom:4px;">
                                    <strong>Role:</strong>
                                    {{ optional($currentHire)->job_title ?? '—' }}
                                </div>
                                @if(!empty($user->hired_date))
                                    <div style="margin-bottom:4px;">
                                        <strong>Since:</strong>
                                        {{ optional($user->hired_date)->format('M d, Y') ?? \Carbon\Carbon::parse($user->hired_date)->format('M d, Y') }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div style="color:#555; font-size:14px;">You're free to apply for new opportunities.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

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
                                        <span>
                                            @if($application->company_name)
                                                {{ $application->company_name }}
                                            @elseif($application->employer)
                                                {{ $application->employer->company_name ?? trim($application->employer->first_name . ' ' . $application->employer->last_name) }}
                                            @elseif($application->jobPosting && $application->jobPosting->employer)
                                                {{ $application->jobPosting->employer->company_name ?? trim($application->jobPosting->employer->first_name . ' ' . $application->jobPosting->employer->last_name) }}
                                            @else
                                                Company Name Not Available
                                            @endif
                                        </span>
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
                                @php
                                    $employerInfo = null;
                                    if ($application->employer) {
                                        $employerInfo = $application->employer;
                                    } elseif ($application->jobPosting && $application->jobPosting->employer) {
                                        $employerInfo = $application->jobPosting->employer;
                                    }
                                @endphp
                                
                                @if($employerInfo)
                                    <div style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 10px; font-size: 13px; border-left: 3px solid #648EB5;">
                                        <div style="font-weight: 600; color: #648EB5; margin-bottom: 6px;">
                                            <i class="fas fa-info-circle"></i> Employer Contact
                                        </div>
                                        <div style="display: grid; gap: 4px; color: #555;">
                                            @if($employerInfo->company_name)
                                                <div><i class="fas fa-building" style="width: 16px; color: #648EB5;"></i> <strong>Company:</strong> {{ $employerInfo->company_name }}</div>
                                            @endif
                                            @if($employerInfo->first_name)
                                                <div><i class="fas fa-user-tie" style="width: 16px; color: #648EB5;"></i> <strong>Contact:</strong> {{ $employerInfo->first_name }} {{ $employerInfo->last_name }}</div>
                                            @endif
                                            @if($employerInfo->email)
                                                <div><i class="fas fa-envelope" style="width: 16px; color: #648EB5;"></i> <strong>Email:</strong> <a href="mailto:{{ $employerInfo->email }}" style="color: #648EB5; text-decoration: none;">{{ $employerInfo->email }}</a></div>
                                            @endif
                                            @if($employerInfo->phone_number)
                                                <div><i class="fas fa-phone" style="width: 16px; color: #648EB5;"></i> <strong>Phone:</strong> <a href="tel:{{ $employerInfo->phone_number }}" style="color: #648EB5; text-decoration: none;">{{ $employerInfo->phone_number }}</a></div>
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

                                <!-- Employment Status for Accepted Applications -->
                                @if($application->status === 'accepted' && isset($application->employment_status))
                                    <div style="margin-top: 8px; padding: 10px 12px; border-radius: 8px; font-size: 12px; text-align: right; 
                                        @if($application->employment_status === 'currently_working')
                                            background: #e7f5ff; border: 1px solid #1971c2; color: #1971c2;
                                        @elseif($application->employment_status === 'terminated')
                                            background: #fff3cd; border: 1px solid #856404; color: #856404;
                                        @else
                                            background: #f8f9fa; border: 1px solid #6c757d; color: #6c757d;
                                        @endif
                                    ">
                                        <div style="font-weight: 600; margin-bottom: 4px;">
                                            @if($application->employment_status === 'currently_working')
                                                <i class="fas fa-briefcase"></i> Currently Working
                                            @elseif($application->employment_status === 'terminated')
                                                <i class="fas fa-user-times"></i> Terminated
                                            @else
                                                <i class="fas fa-sign-out-alt"></i> Resigned
                                            @endif
                                        </div>
                                        @if($application->employment_status === 'terminated' && isset($application->termination_date))
                                            <div style="font-size: 11px; opacity: 0.9;">
                                                {{ $application->termination_date->format('M d, Y') }}
                                            </div>
                                            @if(isset($application->termination_reason))
                                                <div style="font-size: 11px; margin-top: 4px; opacity: 0.9;">
                                                    Reason: {{ $application->termination_reason }}
                                                </div>
                                            @endif
                                        @elseif($application->employment_status === 'currently_working')
                                            <div style="font-size: 11px; opacity: 0.9;">
                                                Since {{ \Carbon\Carbon::parse($application->updated_at)->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

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
                                <form method="POST" action="{{ route('my-applications.destroy', $application) }}" onsubmit="return handleWithdrawApplication(event, this);">
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
    // Page interactions only

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

    // Handle withdraw application
    async function handleWithdrawApplication(event, form) {
        event.preventDefault();
        
        const confirmed = await customConfirm(
            'Are you sure you want to withdraw this application? This action cannot be undone.',
            'Withdraw Application',
            'Yes, Withdraw'
        );
        
        if (confirmed) {
            form.submit();
        }
        
        return false;
    }
</script>

@include('partials.custom-modals')

<style>
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
</style>
@endsection
