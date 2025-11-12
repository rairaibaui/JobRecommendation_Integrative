<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Verification')</title>
  @if (file_exists(public_path('mix-manifest.json')))
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
  @else
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @endif
    <style>
      /* Minimal adjustments so verification pages look clean without the main sidebar */
      /* Use a single solid background color instead of a gradient */
      body { font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; background: #334A5E; color:#fff; margin:0; }
      .standalone-container { max-width:1100px; margin:36px auto; padding:20px; }
      .card { background:#fff; border-radius:10px; padding:18px; box-shadow:0 8px 24px rgba(0,0,0,0.12); }
      .page-header { color:#fff; margin-bottom:18px; }
      .page-title { margin:0; font-size:22px; }
      .page-subtitle { margin:6px 0 0 0; opacity:0.95; }
      .btn { display:inline-block; background:#648EB5; color:white; padding:8px 14px; border-radius:8px; border:none; text-decoration:none; }
      .btn-link { color:#64748b; text-decoration:underline; background:none; border:none; padding:0; }
    </style>
</head>
<body>
  <div class="standalone-container">
    <header style="margin-bottom:10px;">
      @hasSection('header')
        <div class="page-header">
          @yield('header')
        </div>
      @else
        <div class="page-header">
          <h1 class="page-title">@yield('title')</h1>
        </div>
      @endif
    </header>

    <main>
      @yield('content')
    </main>

    <footer style="margin-top:28px; text-align:center; color:#cbd5e1; font-size:13px;">
      &copy; {{ date('Y') }} {{ config('app.name') }}
    </footer>
  </div>

  @if (file_exists(public_path('mix-manifest.json')))
    <script src="{{ mix('/js/app.js') }}"></script>
  @else
    <script src="{{ asset('js/app.js') }}"></script>
  @endif
</body>
</html>
