@props(['picUrl' => null, 'name' => ''])

<div id="profilePicModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10001; align-items:center; justify-content:center;">
  <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 10px 40px rgba(0,0,0,0.3); display:flex; flex-direction:column; align-items:center; max-width:350px; width:90%; position:relative;">
    <button onclick="hideProfilePictureModal()" style="position:absolute; top:15px; right:15px; background:rgba(0,0,0,0.1); border:none; width:32px; height:32px; border-radius:50%; font-size:18px; cursor:pointer; color:#333;">&times;</button>
    <h3 style="margin-bottom:18px; color:#648EB5; font-size:20px; font-weight:600;">Your Profile</h3>
    @if($picUrl)
      <img src="{{ $picUrl }}" alt="Profile Picture" style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:4px solid #648EB5; margin-bottom:12px;">
    @else
      <div style="width:120px; height:120px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:48px; color:#aaa; margin-bottom:12px;"><i class="fas fa-user"></i></div>
    @endif
    <div style="font-size:16px; color:#333; font-weight:500;">{{ $name }}</div>
    <button onclick="hideProfilePictureModal()" style="margin-top:22px; background:#6c757d; color:white; border:none; padding:8px 22px; border-radius:8px; cursor:pointer; font-size:14px;">Close</button>
  </div>
</div>

@once
  @push('scripts')
  <script>
    function showProfilePictureModal() {
      const el = document.getElementById('profilePicModal');
      if (el) el.style.display = 'flex';
    }
    function hideProfilePictureModal() {
      const el = document.getElementById('profilePicModal');
      if (el) el.style.display = 'none';
    }
    document.addEventListener('click', function(e) {
      if (e.target && e.target.id === 'profilePicModal') hideProfilePictureModal();
    });
  </script>
  @endpush
@endonce
