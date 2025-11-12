@extends('layouts.admin')

@section('content')
    <div style="padding:20px; max-width:900px;">
        <h2>Review Permit #{{ $permit->id }}</h2>
        <p><strong>Employer:</strong> {{ $permit->email ?? 'N/A' }}</p>
        <p>
            <strong>Document Type:</strong>
            @php
                $typeLabels = [
                    'MAYORS_PERMIT' => "Mayor's Permit",
                    'BARANGAY_CLEARANCE' => 'Barangay Clearance',
                    'BARANGAY_LOCATIONAL_CLEARANCE' => 'Barangay Locational Clearance',
                    'DTI' => 'DTI Certificate',
                    'BUSINESS_PERMIT' => 'Business Permit',
                    'UNKNOWN' => 'Not specified',
                ];
                $raw = $permit->document_type ?? 'UNKNOWN';
                $label = $typeLabels[strtoupper((string)$raw)] ?? 'Not specified';
            @endphp
            {{ $label }}
        </p>
        <p><strong>Has signature:</strong> {{ $permit->has_signature ? 'Yes' : 'No' }}</p>
        <p><strong>Submitted:</strong> {{ $permit->created_at->toDayDateTimeString() }}</p>

        <h3 style="margin-top:18px;">Extracted Fields</h3>
        <pre style="background:#f8f8f8;padding:12px;border-radius:6px;">{{ json_encode($permit->fields, JSON_PRETTY_PRINT) }}</pre>

        <h3 style="margin-top:12px;">OCR Text</h3>
        <pre style="background:#fafafa;padding:12px;border-radius:6px;">{{ $permit->raw_text ?? 'N/A' }}</pre>

        <h3 style="margin-top:12px;">Review Reason</h3>
        <p>{{ $permit->review_reason ?? 'None provided' }}</p>

        <div style="margin-top:20px; display:flex; gap:8px;">
            <form method="POST" action="{{ route('admin.permits.approve', $permit->id) }}">
                @csrf
                <input name="admin_comment" placeholder="Optional note" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
                <button type="submit" style="padding:8px 12px;background:#10b981;color:white;border:none;border-radius:6px;margin-left:8px;">Approve</button>
            </form>

            <form method="POST" action="{{ route('admin.permits.reject', $permit->id) }}">
                @csrf
                <input name="admin_comment" placeholder="Reason for rejection (required)" required style="padding:8px;border:1px solid #ddd;border-radius:6px;">
                <button type="submit" style="padding:8px 12px;background:#ef4444;color:white;border:none;border-radius:6px;margin-left:8px;">Reject</button>
            </form>
        </div>

        @if($permit->file_path)
            <div style="margin-top:20px;">
                <a href="{{ asset('storage/' . $permit->file_path) }}" target="_blank">Open document file</a>
            </div>
        @endif
    </div>
@endsection
