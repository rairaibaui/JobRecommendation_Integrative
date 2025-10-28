<!-- Overlay -->
<div id="settingsOverlay" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.3);
    z-index:999;
"></div>

<!-- Change Password Modal -->
<div id="changePasswordModal" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    justify-content:center;
    align-items:center;
    z-index:1000;
">
    <div style="
        background:#fff;
        border-radius:10px;
        padding:25px;
        width:90%;
        max-width:400px;
        position:relative;
    ">
        <h2>Change Password</h2>
        <button onclick="closeChangePasswordModal()" style="position:absolute;top:10px;right:10px;font-size:20px;">&times;</button>
        <form method="POST" action="{{ route('change.password.submit') }}">
            @csrf
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" required style="width:100%;padding:10px;margin:10px 0;">

            <label for="password">New Password</label>
            <input type="password" name="password" required style="width:100%;padding:10px;margin:10px 0;">

            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" required style="width:100%;padding:10px;margin:10px 0;">

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeChangePasswordModal()" style="background:#E4E9EE;color:#333;border:1px solid #B0B8C2;padding:10px 15px;border-radius:5px;">Cancel</button>
                <button type="submit" style="background:#3B6E9C;color:#fff;padding:10px 15px;border:none;border-radius:5px;">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Email Modal -->
<div id="changeEmailModal" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    justify-content:center;
    align-items:center;
    z-index:1000;
">
    <div style="
        background:#fff;
        border-radius:10px;
        padding:25px;
        width:90%;
        max-width:400px;
        position:relative;
    ">
        <h2>Change Email</h2>
        <button onclick="closeChangeEmailModal()" style="position:absolute;top:10px;right:10px;font-size:20px;">&times;</button>
        <form method="POST" action="{{ route('profile.changeEmail') }}">
            @csrf
            <label for="email">New Email</label>
            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
            <button type="submit">Change Email</button>
        </form>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeChangeEmailModal()" style="background:#E4E9EE;color:#333;border:1px solid #B0B8C2;padding:10px 15px;border-radius:5px;">Cancel</button>
                <button type="submit" style="background:#3B6E9C;color:#fff;padding:10px 15px;border:none;border-radius:5px;">Change Email</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Phone Modal -->
<div id="changePhoneModal" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    justify-content:center;
    align-items:center;
    z-index:1000;
">
    <div style="
        background:#fff;
        border-radius:10px;
        padding:25px;
        width:90%;
        max-width:400px;
        position:relative;
    ">
        <h2>Change Phone Number</h2>
        <button onclick="closeChangePhoneModal()" style="position:absolute;top:10px;right:10px;font-size:20px;">&times;</button>
    <form method="POST" action="{{ route('profile.changePhone') }}">
        @csrf
        <label for="phone_number">New Phone Number</label>
        <input type="text" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}" required>
        <button type="submit">Change Phone</button>
    </form>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeChangePhoneModal()" style="background:#E4E9EE;color:#333;border:1px solid #B0B8C2;padding:10px 15px;border-radius:5px;">Cancel</button>
                <button type="submit" style="background:#3B6E9C;color:#fff;padding:10px 15px;border:none;border-radius:5px;">Change Phone</button>
            </div>
        </form>
    </div>
</div>

<!-- Clear Bookmarks Modal -->
<div id="clearBookmarksModal" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    justify-content:center;
    align-items:center;
    z-index:1000;
">
    <div style="
        background:#fff;
        border-radius:10px;
        padding:25px;
        width:90%;
        max-width:400px;
        position:relative;
    ">
        <h2>Clear Bookmarks</h2>
        <button onclick="closeClearBookmarksModal()" style="position:absolute;top:10px;right:10px;font-size:20px;">&times;</button>
        <p>All saved jobs will be deleted. Are you sure?</p>
        <form method="POST" action="{{ route('clear.bookmarks') }}">
            @csrf
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeClearBookmarksModal()" style="background:#E4E9EE;color:#333;border:1px solid #B0B8C2;padding:10px 15px;border-radius:5px;">Cancel</button>
                <button type="submit" style="background:#3B6E9C;color:#fff;padding:10px 15px;border:none;border-radius:5px;">Clear All</button>
            </div>
        </form>
    </div>
</div>


<!-- JS for Modals -->
<script>
function showModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.getElementById('settingsOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function hideModal(id) {
    document.getElementById(id).style.display = 'none';
    document.getElementById('settingsOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openChangePasswordModal() { showModal('changePasswordModal'); }
function closeChangePasswordModal() { hideModal('changePasswordModal'); }

function openChangeEmailModal() { showModal('changeEmailModal'); }
function closeChangeEmailModal() { hideModal('changeEmailModal'); }

function openChangePhoneModal() { showModal('changePhoneModal'); }
function closeChangePhoneModal() { hideModal('changePhoneModal'); }

function openClearBookmarksModal() { showModal('clearBookmarksModal'); }
function closeClearBookmarksModal() { hideModal('clearBookmarksModal'); }

function openDeactivateModal() { showModal('deactivateModal'); }
function closeDeactivateModal() { hideModal('deactivateModal'); }


// Close modal when clicking overlay
document.getElementById('settingsOverlay').addEventListener('click', function () {
    ['changePasswordModal','changeEmailModal','changePhoneModal','clearBookmarksModal'].forEach(id => {
        if(document.getElementById(id).style.display === 'flex') hideModal(id);
    });
});
</script>
