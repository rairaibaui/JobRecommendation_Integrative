<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resume Verification Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f5f7fa; color:#24303a; }
        .container { max-width:1000px; margin:2rem auto; background:#fff; padding:1.5rem; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06);} 
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .badge { padding:6px 10px; border-radius:6px; font-weight:600; }
        .badge.verified { background:#d1fae5; color:#065f46; }
        .badge.needs { background:#fef3c7; color:#92400e; }
        .badge.rejected { background:#fee2e2; color:#991b1b; }
        .grid { display:grid; grid-template-columns:1fr 320px; gap:16px; }
        .box { background:#fff; padding:12px; border-radius:8px; border:1px solid #eef2f7; }
        .field { margin-bottom:8px; }
        .field strong { display:block; color:#334155; }
        pre { background:#0f172a; color:#e6eef8; padding:12px; border-radius:6px; overflow:auto; }
        .actions { display:flex; gap:8px; }
        .btn { padding:8px 12px; border-radius:6px; border:none; cursor:pointer; font-weight:600; }
        .btn-approve { background:#10b981; color:white; }
        .btn-reject { background:#ef4444; color:white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Resume Verification — {{ $user->first_name }} {{ $user->last_name }}</h2>
                <div style="color:#64748b; font-size:13px;">Email: {{ $user->email }} • Uploaded: {{ $user->resume_file ? $user->updated_at->format('M d, Y') : 'N/A' }}</div>
            </div>
            <div>
                @php $status = $user->resume_verification_status ?? 'pending'; @endphp
                @if($status === 'verified')
                    <span class="badge verified">Verified</span>
                @elseif($status === 'rejected')
                    <span class="badge rejected">Rejected</span>
                @else
                    <span class="badge needs">{{ ucfirst($status) }}</span>
                @endif
            </div>
        </div>

        {{-- Show prominent banner for email-related statuses --}}
        @if($latestLog)
            @php $notes = $latestLog->notes ?? ''; @endphp
            @if(
                \Illuminate\Support\Str::contains($notes, 'email not verified') || 
                \Illuminate\Support\Str::contains($notes, 'Resume verification pending')
            )
                <div style="background:#fff4e5; border:1px solid #f59e0b; color:#92400e; padding:12px; border-radius:6px; margin-bottom:12px;">
                    <strong>Action required:</strong> {{ $latestLog->notes }}
                </div>
            @elseif(\Illuminate\Support\Str::contains($notes, 'auto-approved') || \Illuminate\Support\Str::contains($notes, 'auto-approved') || \Illuminate\Support\Str::contains($notes,'auto_verified'))
                <div style="background:#d1fae5; border:1px solid #10b981; color:#064e3b; padding:12px; border-radius:6px; margin-bottom:12px;">
                    <strong>Auto-verified:</strong> {{ $latestLog->notes }}
                </div>
            @endif
        @endif

        <div class="grid">
            <div>
                <div class="box">
                    <h3 style="margin-top:0;">Extracted Fields</h3>

                    @if($latestLog)
                        <div class="field"><strong>Full name</strong> {{ $latestLog->extracted_full_name ?? '-' }} <small style="color:#64748b;">(confidence: {{ $latestLog->confidence_name }}/100)</small></div>
                        <div class="field"><strong>Email</strong> {{ $latestLog->extracted_email ?? '-' }} <small style="color:#64748b;">(confidence: {{ $latestLog->confidence_email }}/100)</small></div>
                        <div class="field"><strong>Phone</strong> {{ $latestLog->extracted_phone ?? '-' }} <small style="color:#64748b;">(confidence: {{ $latestLog->confidence_phone }}/100)</small></div>
                        <div class="field"><strong>Birthday</strong> {{ $latestLog->extracted_birthday ? $latestLog->extracted_birthday->format('Y-m-d') : '-' }} <small style="color:#64748b;">(confidence: {{ $latestLog->confidence_birthday }}/100)</small></div>

                        <h4 style="margin-top:12px;">Match Results</h4>
                        <div class="field"><strong>Name Match</strong> {!! $latestLog->match_name ? '<span style="color:#10b981;">Yes</span>' : '<span style="color:#ef4444;">No</span>' !!}</div>
                        <div class="field"><strong>Email Match</strong> {!! $latestLog->match_email ? '<span style="color:#10b981;">Yes</span>' : '<span style="color:#ef4444;">No</span>' !!}</div>
                        <div class="field"><strong>Phone Match</strong> {!! $latestLog->match_phone ? '<span style="color:#10b981;">Yes</span>' : '<span style="color:#ef4444;">No</span>' !!}</div>
                        <div class="field"><strong>Birthday Match</strong> {!! $latestLog->match_birthday ? '<span style="color:#10b981;">Yes</span>' : '<span style="color:#ef4444;">No</span>' !!}</div>

                        <h4 style="margin-top:12px;">Notes</h4>
                        <p style="color:#374151;">{{ $latestLog->notes }}</p>

                        <h4 style="margin-top:12px;">Raw AI / Extraction Output</h4>
                        <pre>{{ $latestLog->raw_ai_response ?? 'No raw AI output stored.' }}</pre>
                    @else
                        <p>No AI extraction log found for this resume.</p>
                    @endif
                </div>

                <div class="box" style="margin-top:12px;">
                    <h3 style="margin-top:0;">Original Resume</h3>
                    @if($user->resume_file && file_exists(storage_path('app/public/' . $user->resume_file)))
                        <p><a href="{{ asset('storage/' . $user->resume_file) }}" target="_blank">Open resume (new tab)</a></p>
                    @else
                        <p>Resume file not available.</p>
                    @endif
                </div>
            </div>

            <div>
                <div class="box">
                    <h3 style="margin-top:0;">Admin Actions</h3>

                    <form method="POST" action="{{ route('admin.resumes.approve', $user->id) }}">
                        @csrf
                        <label>Admin notes (optional)</label>
                        <textarea name="admin_notes" style="width:100%; height:80px; margin-bottom:8px;">Approved by admin</textarea>
                        <div class="actions">
                            <button class="btn btn-approve" type="submit"><i class="fas fa-check"></i> Approve</button>
                        </div>
                    </form>

                    <hr>

                    <form method="POST" action="{{ route('admin.resumes.reject', $user->id) }}">
                        @csrf
                        <label>Rejection reason (required)</label>
                        <textarea name="rejection_reason" required style="width:100%; height:80px; margin-bottom:8px;">Please provide the reason for rejection</textarea>
                        <div class="actions">
                            <button class="btn btn-reject" type="submit"><i class="fas fa-times"></i> Reject</button>
                        </div>
                    </form>

                </div>

                <div class="box" style="margin-top:12px;">
                    <h4>Helpful Tips</h4>
                    <ul style="color:#475569;">
                        <li>If extracted email/phone do not match profile, advise job seeker to re-upload a resume that includes their registered contact info.</li>
                        <li>Use confidence scores to judge whether manual transcription errors occurred.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>