<label>Profile Picture</label>
<input type="file" name="profile_picture" accept="image/*">

@if(Auth::user()->profile_picture)
    <div style="display:flex; align-items:center; gap:10px; margin-top:10px;">
        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" width="120" height="120"
            style="border-radius:50%;object-fit:cover; border:2px solid #ddd;">
        <label style="font-weight:500;">
            <input type="checkbox" name="remove_picture" value="1">
            Remove current picture
        </label>
    </div>
@endif