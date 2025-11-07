<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Hiring History - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px !important; padding-left: 0; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  .card { background:#FFF; border-radius:8px; padding:20px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .stat-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; }
  .stat { background:#fff; border-radius:10px; padding:16px; border-left:4px solid #648EB5; }
  .stat h3 { margin:0; font-size:24px; color:#334A5E; }
  .stat p { margin:4px 0 0 0; font-size:12px; color:#666; }
  .filters { display:flex; gap:8px; flex-wrap:wrap; }
  .filter-btn { padding:8px 14px; border-radius:20px; border:1px solid #648EB5; background:#fff; color:#648EB5; font-size:12px; cursor:pointer; text-decoration:none; }
  .filter-btn.active { background:#648EB5; color:#fff; }
  .history-card { border:1px solid #e5e7eb; border-radius:10px; padding:16px; margin-bottom:12px; transition: transform .2s, box-shadow .2s; }
  .history-card:hover { transform: translateY(-2px); box-shadow:0 12px 24px rgba(0,0,0,0.12); }
  .history-card.hired { border-left:4px solid #43A047; background:#f1f8f4; }
  .history-card.rejected { border-left:4px solid #E53935; background:#fef5f5; }
  .history-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; }
  .decision-badge { padding:6px 12px; border-radius:12px; font-size:12px; font-weight:600; }
  .decision-badge.hired { background:#43A047; color:#fff; }
  .decision-badge.rejected { background:#E53935; color:#fff; }
  .info-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:10px; font-size:13px; color:#555; }
  .info-label { font-weight:600; color:#334A5E; }
  .rejection-reason { background:#fff3cd; border-left:3px solid #ffc107; padding:10px; margin-top:10px; border-radius:6px; }
  .pagination { display:flex; gap:8px; justify-content:center; margin-top:20px; }
    .pagination a, .pagination span { padding:8px 12px; border-radius:6px; background:#fff; color:#648EB5; text-decoration:none; border:1px solid #648EB5; }
  .pagination .active { background:#648EB5; color:#fff; }
  
  @media (max-width: 768px) {
    body { padding: 88px 12px 20px 12px; }
    .main { margin-left: 0; }
    .card { padding: 16px; }
    .info-grid { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <!-- Stats Card -->
    <div class="card">
      <h2 style="margin:0 0 15px 0; color:#334A5E;"><i class="fas fa-chart-bar"></i> Hiring & Rejection Records</h2>
      <div class="stat-grid">
        <div class="stat">
          <h3>{{ $stats['total'] }}</h3>
          <p>Total Records</p>
        </div>
        <div class="stat" style="border-left-color:#43A047;">
          <h3>{{ $stats['hired'] }}</h3>
          <p>Hired</p>
        </div>
        <div class="stat" style="border-left-color:#E53935;">
          <h3>{{ $stats['rejected'] }}</h3>
          <p>Rejected</p>
        </div>
        <div class="stat" style="border-left-color:#6c757d;">
          <h3>{{ $stats['terminated'] }}</h3>
          <p>Terminated</p>
        </div>
        <div class="stat" style="border-left-color:#ffc107;">
          <h3>{{ $stats['resigned'] }}</h3>
          <p>Resigned</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card">
      <div class="filters">
        <a href="{{ route('employer.history') }}" class="filter-btn {{ !request('decision') ? 'active' : '' }}">
          All Records
        </a>
        <a href="{{ route('employer.history', ['decision' => 'hired']) }}" class="filter-btn {{ request('decision') === 'hired' ? 'active' : '' }}">
          <i class="fas fa-check-circle"></i> Hired
        </a>
        <a href="{{ route('employer.history', ['decision' => 'rejected']) }}" class="filter-btn {{ request('decision') === 'rejected' ? 'active' : '' }}">
          <i class="fas fa-times-circle"></i> Rejected
        </a>
        <a href="{{ route('employer.history', ['decision' => 'terminated']) }}" class="filter-btn {{ request('decision') === 'terminated' ? 'active' : '' }}">
          <i class="fas fa-user-slash"></i> Terminated
        </a>
        <a href="{{ route('employer.history', ['decision' => 'resigned']) }}" class="filter-btn {{ request('decision') === 'resigned' ? 'active' : '' }}">
          <i class="fas fa-door-open"></i> Resigned
        </a>
      </div>
    </div>

    <!-- History List -->
    <div class="card">
      <h3 style="margin:0 0 15px 0; color:#334A5E;">
        @if(request('decision') === 'hired')
          <i class="fas fa-check-circle" style="color:#43A047;"></i> Hired Applicants
        @elseif(request('decision') === 'rejected')
          <i class="fas fa-times-circle" style="color:#E53935;"></i> Rejected Applicants
        @elseif(request('decision') === 'terminated')
          <i class="fas fa-user-slash" style="color:#6c757d;"></i> Terminated Employees
        @elseif(request('decision') === 'resigned')
          <i class="fas fa-door-open" style="color:#ffc107;"></i> Resignations
        @else
          <i class="fas fa-list"></i> All Records
        @endif
      </h3>

      @if($history->count() > 0)
        @foreach($history as $record)
          <div class="history-card {{ $record->decision }}">
            <div class="history-header">
              <div>
                <h4 style="margin:0 0 4px 0; color:#334A5E; font-size:16px;">
                  {{ data_get($record->applicant_snapshot, 'first_name') }} {{ data_get($record->applicant_snapshot, 'last_name') }}
                </h4>
                <p style="margin:0; color:#666; font-size:14px;">
                  Applied for: <strong>{{ $record->job_title }}</strong>
                  @if($record->company_name)
                    at {{ $record->company_name }}
                  @endif
                </p>
              </div>
              <span class="decision-badge {{ $record->decision }}" style="{{ $record->decision === 'hired' ? 'background:#43A047;color:#fff;' : ($record->decision === 'rejected' ? 'background:#E53935;color:#fff;' : ($record->decision === 'terminated' ? 'background:#6c757d;color:#fff;' : 'background:#ffc107;color:#000;')) }}">
                @if($record->decision === 'hired')
                  <i class="fas fa-check"></i> HIRED
                @elseif($record->decision === 'rejected')
                  <i class="fas fa-times"></i> REJECTED
                @elseif($record->decision === 'terminated')
                  <i class="fas fa-user-slash"></i> TERMINATED
                @elseif($record->decision === 'resigned')
                  <i class="fas fa-door-open"></i> RESIGNED
                @endif
              </span>
            </div>

            <div class="info-grid">
              @if(data_get($record->applicant_snapshot, 'email'))
                <div>
                  <span class="info-label"><i class="fas fa-envelope"></i> Email:</span> 
                  <a href="mailto:{{ data_get($record->applicant_snapshot, 'email') }}" style="color:#648EB5; text-decoration:none;">
                    {{ data_get($record->applicant_snapshot, 'email') }}
                  </a>
                </div>
              @endif
              @if(data_get($record->applicant_snapshot, 'phone_number'))
                <div>
                  <span class="info-label"><i class="fas fa-phone"></i> Phone:</span> 
                  <a href="tel:{{ data_get($record->applicant_snapshot, 'phone_number') }}" style="color:#648EB5; text-decoration:none;">
                    {{ data_get($record->applicant_snapshot, 'phone_number') }}
                  </a>
                </div>
              @endif
              @if(data_get($record->applicant_snapshot, 'location'))
                <div>
                  <span class="info-label"><i class="fas fa-map-marker-alt"></i> Location:</span> 
                  {{ data_get($record->applicant_snapshot, 'location') }}
                </div>
              @endif
              <div>
                <span class="info-label"><i class="fas fa-calendar"></i> Decision Date:</span> 
                {{ $record->decision_date->format('M d, Y h:i A') }}
              </div>
            </div>

            @if(in_array($record->decision, ['rejected','terminated','resigned']) && $record->rejection_reason)
              <div class="rejection-reason">
                <strong style="color:#856404;"><i class="fas fa-info-circle"></i> {{ ucfirst($record->decision) }} Reason:</strong>
                <p style="margin:4px 0 0 0; color:#856404;">{{ $record->rejection_reason }}</p>
              </div>
            @endif
          </div>
        @endforeach

        <!-- Pagination -->
        @if($history->hasPages())
          <div class="pagination">
            @if($history->onFirstPage())
              <span style="opacity:0.5;"><i class="fas fa-chevron-left"></i></span>
            @else
              <a href="{{ $history->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
            @endif

            @foreach($history->links()->elements[0] as $page => $url)
              @if($page == $history->currentPage())
                <span class="active">{{ $page }}</span>
              @else
                <a href="{{ $url }}">{{ $page }}</a>
              @endif
            @endforeach

            @if($history->hasMorePages())
              <a href="{{ $history->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
            @else
              <span style="opacity:0.5;"><i class="fas fa-chevron-right"></i></span>
            @endif
          </div>
        @endif
      @else
        <div style="text-align:center; color:#999; padding:40px; background:#f8f9fa; border-radius:8px;">
          <i class="fas fa-inbox" style="font-size:48px; opacity:0.4; margin-bottom:12px; display:block;"></i>
          <p style="margin:0; font-size:16px;">No records found.</p>
          <p style="margin:8px 0 0 0; font-size:14px;">Hiring and rejection records will appear here.</p>
        </div>
      @endif
    </div>
  </div>

</div>

@include('partials.logout-confirm')

</body>
</html>