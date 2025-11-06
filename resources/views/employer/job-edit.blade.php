<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Edit Job - Employer | Job Portal Mandaluyong</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@include('employer.partials.unified-styles')

<style>
  /* Page-specific styles */
  * { box-sizing: border-box; margin:0; padding:0; }
  body { width:100vw; min-height:100vh; display:flex; font-family:'Roboto', sans-serif; background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%); padding:88px 20px 20px 20px; gap:20px; }
  
  .main { margin-left:270px; flex:1; display:flex; flex-direction:column; gap:20px; padding-bottom: 40px; }
  
  .content-area { width: 100%; }
  .page-header { margin-bottom: 20px; }
  .page-title { font-size: 28px; font-weight: 700; color: #2B4053; margin: 0; display: flex; align-items: center; gap: 12px; }
  
  .card { background:#FFF; border-radius:8px; padding:28px; box-shadow:0 8px 4px rgba(144, 141, 141, 0.3); }
  
  .form-group { margin-bottom: 20px; }
  .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 14px; }
  .form-group input[type="text"],
  .form-group select,
  .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: 'Roboto', sans-serif; transition: border-color 0.2s; }
  .form-group input[type="text"]:focus,
  .form-group select:focus,
  .form-group textarea:focus { outline: none; border-color: #648EB5; box-shadow: 0 0 0 3px rgba(100, 142, 181, 0.1); }
  .form-group textarea { min-height: 150px; resize: vertical; }
  .form-group small { display: block; margin-top: 6px; font-size: 12px; color: #6c757d; }
  .form-group .error { display: block; margin-top: 6px; font-size: 12px; color: #dc3545; }
  
  .btn-primary { background: #648EB5; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; }
  .btn-primary:hover { background: #567a9c; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
  .btn-secondary { background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; text-decoration: none; margin-left: 10px; }
  .btn-secondary:hover { background: #5a6268; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
  
  @media (max-width: 768px) {
    body { padding: 88px 12px 20px 12px; }
    .main { margin-left: 0; }
    .card { padding: 20px; }
  }
</style>
</head>
<body>

@include('employer.partials.navbar')
@include('employer.partials.sidebar')

<div class="main">
  <div class="content-area">
    <div class="page-header">
      <h1 class="page-title"><i class="fas fa-edit"></i> Edit Job Posting</h1>
    </div>

    <div class="card">
      <h2 style="margin-bottom:24px; color:#334A5E; font-family:'Poppins', sans-serif;">Edit Job Posting</h2>

      @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:12px; border-radius:6px; margin-bottom:20px; border:1px solid #f5c6cb;">
          <strong>Please fix the following errors:</strong>
          <ul style="margin:8px 0 0 20px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('employer.jobs.update', $jobPosting) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label for="title">Job Title <span style="color:red;">*</span></label>
          <input type="text" id="title" name="title" value="{{ old('title', $jobPosting->title) }}" required placeholder="e.g. Senior Web Developer">
          @error('title')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" value="{{ old('location', $jobPosting->location) }}" placeholder="e.g. Mandaluyong, Manila">
          @error('location')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="type">Job Type <span style="color:red;">*</span></label>
          <select id="type" name="type" required>
            <option value="Full-time" {{ old('type', $jobPosting->type) == 'Full-time' ? 'selected' : '' }}>Full-time</option>
            <option value="Part-time" {{ old('type', $jobPosting->type) == 'Part-time' ? 'selected' : '' }}>Part-time</option>
            <option value="Contract" {{ old('type', $jobPosting->type) == 'Contract' ? 'selected' : '' }}>Contract</option>
            <option value="Internship" {{ old('type', $jobPosting->type) == 'Internship' ? 'selected' : '' }}>Internship</option>
            <option value="Freelance" {{ old('type', $jobPosting->type) == 'Freelance' ? 'selected' : '' }}>Freelance</option>
          </select>
          @error('type')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="salary">Salary Range</label>
          <input type="text" id="salary" name="salary" value="{{ old('salary', $jobPosting->salary) }}" placeholder="e.g. PHP 30,000 - 50,000/month or Negotiable">
          @error('salary')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="description">Job Description <span style="color:red;">*</span></label>
          <textarea id="description" name="description" required placeholder="Describe the role, responsibilities, and requirements...">{{ old('description', $jobPosting->description) }}</textarea>
          @error('description')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="skills">Required Skills</label>
          <input type="text" id="skills" name="skills" value="{{ old('skills', is_array($jobPosting->skills) ? implode(', ', $jobPosting->skills) : '') }}" placeholder="e.g. PHP, Laravel, JavaScript, React (comma-separated)">
          <small>Enter skills separated by commas</small>
          @error('skills')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" name="status">
            <option value="active" {{ old('status', $jobPosting->status) == 'active' ? 'selected' : '' }}>Active (Accepting applications)</option>
            <option value="closed" {{ old('status', $jobPosting->status) == 'closed' ? 'selected' : '' }}>Closed (Position filled / No longer hiring)</option>
            <option value="draft" {{ old('status', $jobPosting->status) == 'draft' ? 'selected' : '' }}>Draft (Not visible to job seekers)</option>
          </select>
          <small>Set to "Closed" when position is filled or no more applicants needed</small>
          @error('status')<span class="error">{{ $message }}</span>@enderror
        </div>

        <div style="margin-top:30px;">
          <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
          <a href="{{ route('employer.jobs') }}" class="btn-secondary">Cancel</a>
        </div>
      </form>
      </div>
    </div>
  </div>

</div>

@include('partials.logout-confirm')

</body>
</html>