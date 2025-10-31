<!-- Overlay -->
<div id="settingsOverlay" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:999;
    backdrop-filter: blur(2px);
"></div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="modal">
    <div class="modal-content">
        <h2>Change Password</h2>
        <button onclick="closeChangePasswordModal()" class="close-btn">&times;</button>
        <form method="POST" action="{{ route('change.password.submit') }}">
            @csrf
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>

            <label for="password">New Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>

            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>

            <div class="button-group">
                <button type="button" onclick="closeChangePasswordModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Email Modal -->
<div id="changeEmailModal" class="modal">
    <div class="modal-content">
        <h2>Change Email</h2>
        <button onclick="closeChangeEmailModal()" class="close-btn">&times;</button>
        <form method="POST" action="{{ route('profile.changeEmail') }}">
            @csrf
            <label for="email">New Email</label>
            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>

            <div class="button-group">
                <button type="button" onclick="closeChangeEmailModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-primary">Change Email</button>
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

<div id="deactivateModal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <h2>Deactivate Account</h2>
        <button onclick="closeDeactivateModal()" class="close-btn">&times;</button>
        <p style="color:#666; margin:15px 0; line-height:1.5;">Are you sure you want to deactivate your account? You can reactivate later by logging in.</p>
        <form method="POST" action="{{ route('profile.deactivate') }}">
            @csrf
            <div class="button-group">
                <button type="button" onclick="closeDeactivateModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-danger">Deactivate</button>
            </div>
        </form>
    </div>
</div>



<div id="deactivateModal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <h2>Deactivate Account</h2>
        <button onclick="closeDeactivateModal()" class="close-btn">&times;</button>
        <p style="color:#666; margin:15px 0; line-height:1.5;">Are you sure you want to deactivate your account? You can reactivate later by logging in.</p>
        <form method="POST" action="{{ route('profile.deactivate') }}">
            @csrf
            <div class="button-group">
                <button type="button" onclick="closeDeactivateModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-danger">Deactivate</button>
            </div>
        </form>
    </div>
</div>


<style>
/* Modal styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    width: 90%;
    max-width: 400px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-content h2 {
    color: #1E3A5F;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: 600;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    padding: 5px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: #f0f0f0;
    color: #333;
}

.modal form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modal label {
    font-weight: 600;
    color: #1E3A5F;
    margin-bottom: 5px;
    display: block;
}

.modal input[type="password"],
.modal input[type="email"],
.modal input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #E0E6EB;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.2s ease;
}

.modal input[type="password"]:focus,
.modal input[type="email"]:focus,
.modal input[type="text"]:focus {
    outline: none;
    border-color: #1E3A5F;
    box-shadow: 0 0 5px rgba(30, 58, 95, 0.3);
}

.modal p {
    color: #666;
    margin: 15px 0;
    line-height: 1.5;
}

.button-group {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-cancel {
    background: #E4E9EE;
    color: #333;
    border: 1px solid #B0B8C2;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-cancel:hover {
    background: #D0D7DD;
}

.btn-primary {
    background: #1E3A5F;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background: #2c4c7a;
}

.btn-danger {
    background: #e74c3c;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background: #c0392b;
}

/* Responsive modal design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 20px;
    }

    .modal .button-group {
        flex-direction: column;
    }

    .modal .btn-cancel,
    .modal .btn-primary,
    .modal .btn-danger {
        width: 100%;
    }
}
</style>

<style>
/* Modal styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: #fff;
    border-radius: 10px;
    padding: 25px;
    width: 90%;
    max-width: 400px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: modalFadeIn 0.3s ease-out;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-content h2 {
    color: #1E3A5F;
    margin-bottom: 20px;
    font-size: 24px;
    font-weight: 600;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
    padding: 5px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: #f0f0f0;
    color: #333;
}

.modal form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modal label {
    font-weight: 600;
    color: #1E3A5F;
    margin-bottom: 5px;
    display: block;
}

.modal input[type="password"],
.modal input[type="email"],
.modal input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #E0E6EB;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.2s ease;
}

.modal input[type="password"]:focus,
.modal input[type="email"]:focus,
.modal input[type="text"]:focus {
    outline: none;
    border-color: #1E3A5F;
    box-shadow: 0 0 5px rgba(30, 58, 95, 0.3);
}

.modal p {
    color: #666;
    margin: 15px 0;
    line-height: 1.5;
}

.button-group {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-cancel {
    background: #E4E9EE;
    color: #333;
    border: 1px solid #B0B8C2;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-cancel:hover {
    background: #D0D7DD;
}

.btn-primary {
    background: #1E3A5F;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background: #2c4c7a;
}

.btn-danger {
    background: #e74c3c;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background: #c0392b;
}

/* Responsive modal design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        padding: 20px;
    }

    .modal .button-group {
        flex-direction: column;
    }

    .modal .btn-cancel,
    .modal .btn-primary,
    .modal .btn-danger {
        width: 100%;
    }
}
</style>

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
    ['changePasswordModal','changeEmailModal','changePhoneModal','clearBookmarksModal','deactivateModal'].forEach(id => {
        if(document.getElementById(id).style.display === 'flex') hideModal(id);
    });
});
</script>