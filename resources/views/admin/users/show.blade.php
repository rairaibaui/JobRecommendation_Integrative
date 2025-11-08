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
        <div class="card border-0 shadow-none">
          <div class="card-body">
            <h5 class="card-title">{{ $user->first_name ?? $user->name ?? 'N/A' }} {{ $user->last_name ?? '' }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="card-text"><strong>User Type:</strong> {{ $user->user_type ?? 'N/A' }}</p>
            <p class="card-text"><strong>Company Name:</strong> {{ $user->company_name ?? 'N/A' }}</p>
            <p class="card-text"><strong>Job Title:</strong> {{ $user->job_title ?? 'N/A' }}</p>
            <p class="card-text"><strong>Phone Number:</strong> {{ $user->phone_number ?? 'N/A' }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $user->employment_status ?? 'N/A' }}</p>
            <p class="card-text"><strong>Created At:</strong> {{ $user->created_at }}</p>
            <p class="card-text"><strong>Last Updated:</strong> {{ $user->updated_at }}</p>
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
