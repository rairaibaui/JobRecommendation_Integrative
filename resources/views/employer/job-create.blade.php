<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Post New Job - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  
  .content-area { width: 100%; min-width:600px; max-width:1200px; }
  .page-header { margin-bottom: 20px; }
  .page-title { font-size: 28px; font-weight: 700; color: #2B4053; margin: 0; display: flex; align-items: center; gap: 12px; }
  
  .card { background:#FFF; border-radius:8px; padding:0; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  .card-header { padding: 20px 28px; border-bottom: 2px solid #e5e7eb; }
  .card-title { font-size: 20px; font-weight: 600; color: #2B4053; margin: 0; display: flex; align-items: center; gap: 10px; }
  .card-body { padding: 28px; }
  
  .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; align-items: flex-start; gap: 10px; }
  .alert i { font-size: 18px; margin-top: 2px; }
  .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
  
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
  .form-group { margin-bottom: 20px; }
  .form-label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px; }
  .form-control { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px !important; min-height: 44px; font-family: 'Roboto', sans-serif; transition: border-color 0.2s; }
  .form-control:focus { outline: none; border-color: #648EB5; box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1); }
  textarea.form-control { min-height: 120px !important; resize: vertical; }
  .form-help { display: block; margin-top: 6px; font-size: 12px; color: #6c757d; }
  .error { display: block; margin-top: 6px; font-size: 12px; color: #dc3545; }
  
  .text-danger { color: #dc3545; }
  
  .d-flex { display: flex; }
  .gap-2 { gap: 12px; }
  .justify-content-end { justify-content: flex-end; }
  .mt-4 { margin-top: 24px; }
  
  .btn { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
  .btn-primary { background: #648EB5; color: white; }
  .btn-primary:hover { background: #567a9c; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
  .btn-secondary { background: #6c757d; color: white; }
  .btn-secondary:hover { background: #5a6268; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
  
  @media (max-width: 768px) {
    body { padding: 88px 12px 20px 12px; }
    .main { margin-left: 0; }
    .content-area { min-width: auto; }
    .card-header, .card-body { padding: 20px; }
    .form-row { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <div class="content-area">
    <div class="page-header">
      <h1 class="page-title"><i class="fas fa-plus-circle"></i> Post New Job</h1>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="card-title"><i class="fas fa-briefcase"></i> Job Details</h2>
      </div>
      
      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger" style="margin-bottom:25px;">
            <i class="fas fa-exclamation-circle"></i>
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0 20px;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('employer.jobs.store') }}">
          @csrf

          <div class="form-row">
            <div class="form-group" style="flex:2;">
              <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
              <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Senior Web Developer">
              @error('title')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
              <label for="location" class="form-label">Location</label>
              <input type="text" id="location" name="location" class="form-control" value="{{ old('location', 'Mandaluyong') }}" placeholder="Mandaluyong">
              @error('location')<span class="error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="type" class="form-label">Job Type <span class="text-danger">*</span></label>
              <select id="type" name="type" class="form-control" required>
                <option value="Full-time" {{ old('type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                <option value="Part-time" {{ old('type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                <option value="Contract" {{ old('type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                <option value="Internship" {{ old('type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                <option value="Freelance" {{ old('type') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
              </select>
              @error('type')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
              <label for="salary" class="form-label">Salary Range</label>
              <input type="text" id="salary" name="salary" class="form-control" value="{{ old('salary') }}" placeholder="e.g. PHP 30,000 - 50,000/month or Negotiable">
              @error('salary')<span class="error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div class="form-group">
            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
            <textarea id="description" name="description" class="form-control" required rows="6" placeholder="Describe the role, responsibilities, and requirements...">{{ old('description') }}</textarea>
            @error('description')<span class="error">{{ $message }}</span>@enderror
          </div>

          <div class="form-row">
            <div class="form-group" style="flex:2;">
              <label for="skills" class="form-label">Required Skills</label>
              <input type="text" id="skills" name="skills" class="form-control" value="{{ old('skills') }}" placeholder="e.g. PHP, Laravel, JavaScript, React">
              <small class="form-help"><i class="fas fa-info-circle"></i> Enter skills separated by commas</small>
              @error('skills')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
              <label for="status" class="form-label">Status</label>
              <select id="status" name="status" class="form-control">
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active (Visible to job seekers)</option>
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Not visible yet)</option>
              </select>
              @error('status')<span class="error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('employer.dashboard') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Post Job</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

@include('partials.logout-confirm')

</body>
</html>