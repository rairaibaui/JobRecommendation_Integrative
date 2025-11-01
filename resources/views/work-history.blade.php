@extends('layouts.recommendation')

@section('content')
<div class="main">
    <div class="top-navbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>Job Portal - Mandaluyong</div>
        @include('partials.notifications')
    </div>

    <div class="card-large" style="background:#fff;">
        <div class="recommendation-header">
            <h3>Work History</h3>
            <p>Your employment decisions recorded over time</p>
        </div>

        @php $hist = isset($employmentHistory) ? $employmentHistory : collect(); @endphp
        @if($hist->isEmpty())
            <div style="text-align:center; padding:40px; color:#777;">
                <i class="fas fa-inbox" style="font-size:48px; opacity:0.3;"></i>
                <div style="margin-top:10px; font-size:16px;">No work history recorded yet.</div>
            </div>
        @else
            <div style="background:#fff; border:1px solid #E0E6EB; border-radius:10px; padding:16px;">
                <ul style="list-style:none; margin:0; padding:0;">
                    @foreach($hist as $h)
                        @php
                            $date = $h->decision_date ? (\Carbon\Carbon::parse($h->decision_date)->format('M d, Y')) : '';
                            $company = $h->company_name ?: 'Unknown Company';
                            $title = $h->job_title ?: 'Role';
                            $line = '';
                            switch($h->decision){
                                case 'hired': $line = "Hired as {$title} at {$company}"; break;
                                case 'resigned': $line = "Resigned from {$company}"; break;
                                case 'terminated': $line = "Terminated from {$company}"; break;
                                default: $line = ucfirst(str_replace('_',' ',$h->decision))." - {$company}";
                            }
                        @endphp
                        <li style="display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #f2f2f2; align-items:center;">
                            <div style="width:120px; color:#666; font-size:12px;">{{ $date }}</div>
                            <div style="flex:1; color:#333; font-size:14px;">{{ $line }}</div>
                            @if(in_array($h->decision, ['resigned','terminated']) && $h->rejection_reason)
                                <div style="color:#888; font-size:12px;">Reason: {{ $h->rejection_reason }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
