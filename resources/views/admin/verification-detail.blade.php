<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification Detail</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; background:#f4f6f9; margin:0; padding:20px; }
    .container { max-width: 1200px; margin:0 auto; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.06); padding:20px; margin-bottom:16px; }
    .header { display:flex; justify-content:space-between; align-items:center; }
    .title { margin:0; color:#2B4053; font-size:22px; font-weight:700; }
    .back { text-decoration:none; background:#648EB5; color:#fff; padding:8px 14px; border-radius:8px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; }
    .label { font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:4px; font-weight:600; }
    .value { font-size:15px; color:#212529; }
    .badge { display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; }
    .badge.approved { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .badge.pending { background:#fff3cd; color:#856404; border:1px solid #ffeaa7; }
    .badge.rejected { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    table { width:100%; border-collapse: collapse; }
    th, td { text-align:left; padding:10px 12px; border-bottom:1px solid #eee; }
    thead th { background:#f8f9fa; font-size:12px; color:#6c757d; text-transform:uppercase; letter-spacing:0.3px; }
    .empty { text-align:center; color:#6c757d; padding:24px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card header">
      <h1 class="title">Verification Detail</h1>
      <a href="{{ route('admin.verifications.index') }}" class="back">← Back</a>
    </div>

    <div class="card">
      <div class="grid">
        <div>
          <div class="label">Company</div>
          <div class="value">{{ $validation->user->company_name ?? 'N/A' }}</div>
        </div>
        <div>
          <div class="label">Email</div>
          <div class="value">{{ $validation->user->email }}</div>
        </div>
        <div>
          <div class="label">Status</div>
          <div class="value">
            @php $status = $validation->validation_status; @endphp
            <span class="badge {{ $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'pending') }}">
              {{ ucfirst(str_replace('_',' ', $status)) }}
            </span>
          </div>
        </div>
        <div>
          <div class="label">Confidence</div>
          <div class="value">{{ $validation->confidence_score ?? 0 }}%</div>
        </div>
        <div>
          <div class="label">Permit Number</div>
          <div class="value">{{ $validation->permit_number ?? '—' }}</div>
        </div>
        <div>
          <div class="label">Expiry Date</div>
          @php
            $exp = $validation->permit_expiry_date;
            $expStr = $exp instanceof \DateTimeInterface ? $exp->format('M d, Y') : (is_string($exp) ? $exp : null);
          @endphp
          <div class="value">{{ $expStr ?? '—' }}</div>
        </div>
      </div>
      @if($validation->reason)
        <div style="margin-top:12px;">
          <div class="label">Notes</div>
          <div class="value">{{ $validation->reason }}</div>
        </div>
      @endif
      <div style="margin-top:12px;">
        <a href="{{ route('admin.verifications.file', $validation->id) }}" class="back" style="background:#2B4053;">👁️ View Original File</a>
      </div>
    </div>

    <div class="card">
      <h2 class="title" style="font-size:18px; margin-bottom:12px;">Audit Trail</h2>
      @if(isset($auditTrails) && $auditTrails->count())
        <table>
          <thead>
            <tr>
              <th>When</th>
              <th>Action</th>
              <th>Admin</th>
              <th>Notes</th>
              <th>IP</th>
              <th>User Agent</th>
            </tr>
          </thead>
          <tbody>
            @foreach($auditTrails as $entry)
              <tr>
                <td>{{ $entry->created_at->format('M d, Y h:i A') }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $entry->action)) }}</td>
                <td>{{ $entry->admin_email ?? '—' }}</td>
                <td>{{ $entry->notes ?? '—' }}</td>
                <td>{{ $entry->ip_address ?? '—' }}</td>
                <td style="max-width:420px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $entry->user_agent ?? '—' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <div class="empty">No audit trail entries yet.</div>
      @endif
    </div>
  </div>

@include('partials.logout-confirm')
</body>
</html>
