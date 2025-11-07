@extends('jobseeker.layouts.base')

@section('title', 'Work History')
@php($pageTitle = 'Work History')
@php($hist = isset($employmentHistory) ? $employmentHistory : collect())

@section('content')
    <div class="page-header mb-2">
        <h1 class="page-title"><i class="fas fa-clock-rotate-left"></i> Work History</h1>
        <div class="page-subtitle">Your employment decisions recorded over time</div>
    </div>

    <div class="card">
        @if($hist->isEmpty())
            <div class="d-flex flex-column align-items-center justify-content-center p-3 text-muted" style="min-height:140px;">
                <i class="fas fa-inbox" style="font-size:44px; opacity:0.3;"></i>
                <div class="mt-1">No work history recorded yet.</div>
            </div>
        @else
                        <ul class="p-0 m-0" style="list-style:none;">
                                @foreach($hist as $h)
                                        <li class="d-flex align-items-center gap-2 p-2" style="border-bottom:1px solid #f2f2f2;">
                                                <div class="text-muted" style="width:120px; font-size:12px;">
                                                    {{ $h->decision_date ? \Carbon\Carbon::parse($h->decision_date)->format('M d, Y') : '' }}
                                                </div>
                                                <div style="flex:1; color:#333; font-size:14px;">
                                                    @switch($h->decision)
                                                        @case('hired')
                                                            Hired as {{ $h->job_title ?: 'Role' }} at {{ $h->company_name ?: 'Unknown Company' }}
                                                            @break
                                                        @case('resigned')
                                                            Resigned from {{ $h->company_name ?: 'Unknown Company' }}
                                                            @break
                                                        @case('terminated')
                                                            Terminated from {{ $h->company_name ?: 'Unknown Company' }}
                                                            @break
                                                        @default
                                                            {{ ucfirst(str_replace('_',' ', $h->decision)) }} - {{ $h->company_name ?: 'Unknown Company' }}
                                                    @endswitch
                                                </div>
                                                @if(in_array($h->decision, ['resigned','terminated']) && $h->rejection_reason)
                                                        <div class="text-muted" style="font-size:12px;">Reason: {{ $h->rejection_reason }}</div>
                                                @endif
                                        </li>
                                @endforeach
                        </ul>
        @endif
    </div>
@endsection
