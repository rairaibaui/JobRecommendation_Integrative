@extends('admin.layout')

@section('title', 'User Details')

@section('content')
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary mt-4 ms-4" data-bs-toggle="modal" data-bs-target="#userDetailsModal">
  View User Details
</button>

<!-- Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Modal Header: User Name and Type Badge -->
        <div class="text-center mb-4">
          <h2 class="fw-bold mb-2" style="color: #2B4053; font-size: 28px;">
            {{ $user->first_name ?? $user->name ?? 'N/A' }} {{ $user->last_name ?? '' }}
          </h2>
          <span class="badge badge-{{ $user->user_type ?? 'job_seeker' }}" style="font-size: 12px; padding: 6px 12px;">
            {{ ucfirst(str_replace('_', ' ', $user->user_type ?? 'job_seeker')) }}
          </span>
        </div>

        <!-- Main Content: Two-Column Grid -->
        <div class="row g-4 mb-4">
          <!-- Column 1: Contact Info -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-3" style="color: #506B81;">Contact Information</h6>

                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-envelope" style="color: #648EB5; width: 16px;"></i>
                    <strong style="font-size: 14px; color: #506B81;">Email:</strong>
                  </div>
                  <div style="font-size: 15px; color: #2B4053; margin-left: 18px;">{{ $user->email }}</div>
                </div>

                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-phone" style="color: #648EB5; width: 16px;"></i>
                    <strong style="font-size: 14px; color: #506B81;">Phone:</strong>
                  </div>
                  <div style="font-size: 15px; color: #2B4053; margin-left: 18px;">{{ $user->phone_number ?? 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Column 2: Company Info -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="background: #f8f9fa;">
              <div class="card-body">
                <h6 class="card-title fw-bold mb-3" style="color: #506B81;">Company Information</h6>

                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-building" style="color: #648EB5; width: 16px;"></i>
                    <strong style="font-size: 14px; color: #506B81;">Company:</strong>
                  </div>
                  <div style="font-size: 15px; color: #2B4053; margin-left: 18px;">{{ $user->company_name ?? 'N/A' }}</div>
                </div>

                <div class="mb-3">
                  <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-briefcase" style="color: #648EB5; width: 16px;"></i>
                    <strong style="font-size: 14px; color: #506B81;">Job Title:</strong>
                  </div>
                  <div style="font-size: 15px; color: #2B4053; margin-left: 18px;">{{ $user->job_title ?? 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer / Metadata -->
        <hr class="my-4" style="border-color: #E5E7EB;">
        <div class="text-center">
          <div class="mb-3">
            <span class="badge badge-{{ $user->employment_status === 'employed' ? 'success' : 'info' }}" style="font-size: 12px; padding: 6px 12px;">
              {{ ucfirst($user->employment_status ?? 'unemployed') }}
            </span>
          </div>
          <div style="font-size: 13px; color: #64748b;">
            <div><strong>Created At:</strong> {{ $user->created_at->format('M d, Y \a\t h:i A') }}</div>
            <div><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \a\t h:i A') }}</div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Automatically open the modal when the page loads
  document.addEventListener('DOMContentLoaded', function() {
    var userModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    userModal.show();
  });
</script>
@endsection
