<div class="flash-container" style="margin-bottom: 16px;">
  <style>
    .flash { position: relative; border-radius: 12px; padding: 12px 14px 12px 44px; margin: 10px 0; color: #0f1a24; box-shadow: 0 10px 22px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.06); font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    .flash .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; }
    .flash button.close { position: absolute; top: 10px; right: 10px; border: none; background: transparent; color: inherit; opacity: .5; cursor: pointer; font-size: 18px; line-height: 1; }
    .flash button.close:hover { opacity: .8; }
    .flash-success { background: #e8f6ee; color: #17643f; border-color: #c7ecd6; }
    .flash-info    { background: #eaf2fb; color: #084298; border-color: #cfe2ff; }
    .flash-error   { background: #ffe8e8; color: #9b2121; border-color: #ffd2d2; }
    .flash ul { margin: 6px 0 0 18px; }
  </style>

  @if (session('status'))
    <div class="flash flash-success" role="alert">
      <span class="icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
      <strong style="display:block;">{{ session('status') }}</strong>
      <small style="display:block; opacity:.85;">Please check your email inbox (and spam folder).</small>
      <button type="button" class="close" aria-label="Close" onclick="this.parentElement.remove()">×</button>
    </div>
  @endif

  @if (session('success'))
    <div class="flash flash-success" role="alert">
      <span class="icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
      <strong style="display:block;">{{ session('success') }}</strong>
      <button type="button" class="close" aria-label="Close" onclick="this.parentElement.remove()">×</button>
    </div>
  @endif

  @if (session('error'))
    <div class="flash flash-error" role="alert">
      <span class="icon"><i class="fas fa-exclamation-circle" aria-hidden="true"></i></span>
      <strong style="display:block;">{{ session('error') }}</strong>
      <button type="button" class="close" aria-label="Close" onclick="this.parentElement.remove()">×</button>
    </div>
  @endif

  @if ($errors->any())
    <div class="flash flash-error" role="alert">
      <span class="icon"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></span>
      <strong style="display:block;">We found some problems:</strong>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="close" aria-label="Close" onclick="this.parentElement.remove()">×</button>
    </div>
  @endif
</div>