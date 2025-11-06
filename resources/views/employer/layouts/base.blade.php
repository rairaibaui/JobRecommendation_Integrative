<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Employer - Job Portal Mandaluyong')</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  @include('employer.partials.unified-styles')
  @stack('head')
</head>
<body>
  @php
    $pageTitle = $pageTitle ?? 'EMPLOYER PORTAL';
  @endphp
  @include('employer.partials.navbar')

  <div class="main-content">
    @include('employer.partials.sidebar')
    <div class="content-area">
      @yield('content')
    </div>
  </div>

  @stack('scripts')
</body>
</html>
