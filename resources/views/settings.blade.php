@extends('layouts.recommendation')

@section('content')
@if(session('success'))
<div class="success-message" style="
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #4CAF50;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 10000;
    font-weight: 600;
    animation: slideIn 0.3s ease-out;
">
    <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
    {{ session('success') }}
</div>
<script>
    setTimeout(function() {
        const message = document.querySelector('.success-message');
        if (message) {
            message.style.animation = 'fadeOut 0.5s ease-out forwards';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }
    }, 3000);
</script>
@endif

@if($errors->any())
<div class="error-message" style="
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #f44336;
    color: white;
    padding: 15px 20px;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 10000;
    font-weight: 600;
    animation: slideIn 0.3s ease-out;
">
    <i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i>
    @foreach($errors->all() as $error)
        {{ $error }}
    @endforeach
</div>
<script>
    setTimeout(function() {
        const message = document.querySelector('.error-message');
        if (message) {
            message.style.animation = 'fadeOut 0.5s ease-out forwards';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }
    }, 3000);
</script>
@endif

<style>
@keyframes slideIn {
    from {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
    }
}
</style>
<div class="main">
    <div class="top-navbar">
        <i class="fas fa-bars hamburger"></i>
        Job Portal - Mandaluyong
    </div>

    <!-- Settings -->
    <div class="card-large">
        <div class="recommendation-header">
            <h3 style="color: #FFFFFF;">Settings</h3>
            <p style="color: #FFFFFF;">Manage your account preferences, profile, and security settings.</p>
        </div>

        <div class="settings-container" style="margin-top: 30px;">
            <!-- Tabs -->
            <div class="tabs" style="display: flex; border-bottom: 1px solid #8AA4B8; margin-bottom: 20px;">
                <div class="tab active" style="margin-right: 30px; padding-bottom: 10px; cursor: pointer; color: #fff; font-weight: 600; border-bottom: 2px solid #fff;">
                    Account Settings
                </div>
                <div class="tab" style="margin-right: 30px; padding-bottom: 10px; cursor: pointer; color: #D3DCE3; font-weight: 600;">
                    Profile Settings
                </div>
            </div>

            <!-- Account Settings Card -->
    <div class="card-large" style="background: #fff; color: #000; border-radius: 10px; padding: 25px; margin-bottom: 25px;">
        <h2 style="font-size: 20px; margin-bottom: 15px; color: #1E3A5F;">Account Settings</h2>

        <!-- Password Setting -->
        <div class="setting-item" style="display: flex; justify-content: space-between; align-items: flex-start; border-top: 1px solid #E0E6EB; padding: 15px 0;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <i class="fas fa-lock" style="font-size: 20px; color: #1E3A5F; margin-top: 2px;"></i>
                <div>
                    <strong style="font-size: 16px; display: block;">Password</strong>
                    <p style="margin: 2px 0 0 0; font-size: 14px; color: #555;">Change your password to keep your account secure.</p>
                </div>
            </div>
            <button onclick="openChangePasswordModal()" style="
                background: #E4E9EE;
                border: 1px solid #B0B8C2;
                border-radius: 5px;
                padding: 6px 14px;
                font-size: 13px;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                transition: all 0.2s ease;
            "
            onmouseover="this.style.background='#D8E0E7';"
            onmouseout="this.style.background='#E4E9EE';">
                Change Password
            </button>
        </div>

        <!-- Bookmark Setting -->
        <div class="setting-item" style="display: flex; justify-content: space-between; align-items: flex-start; border-top: 1px solid #E0E6EB; padding: 15px 0;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <i class="fas fa-bookmark" style="font-size: 20px; color: #1E3A5F; margin-top: 2px;"></i>
                <div>
                    <strong style="font-size: 16px; display: block;">Bookmark</strong>
                    <p style="margin: 2px 0 0 0; font-size: 14px; color: #555;">Remove all saved jobs from your bookmarks.</p>
                </div>
            </div>
            <button onclick="openClearBookmarksModal()" style="
                background: #E4E9EE;
                border: 1px solid #B0B8C2;
                border-radius: 5px;
                padding: 6px 14px;
                font-size: 13px;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                transition: all 0.2s ease;
            "
            onmouseover="this.style.background='#D8E0E7';"
            onmouseout="this.style.background='#E4E9EE';">
                Clear All Bookmarks
            </button>
        </div>
    </div>

            <!-- Logout Card -->
<div class="card" style="background: #fff; color: #000; border-radius: 10px; padding: 30px 30px;">
    <h2 style="font-size: 24px; margin-bottom: 15px; color: #1E3A5F;">Session</h2>
    <button style="
        display: flex;
        align-items: center;
        gap: 10px;
        background: #3B6E9C;
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 16px;
        transition: 0.2s;
    "
    onmouseover="this.style.background='#2E5982'"
    onmouseout="this.style.background='#3B6E9C'">
        <i class="fas fa-sign-out-alt" style="font-size: 20px; color: #fff;"></i>
        Log out
    </button>
<!-- Change Password Modal -->
<div id="changePasswordModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(212, 58, 58, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
">
    <div style="
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        position: relative;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; margin: 0; color: #1E3A5F;">Change Password</h2>
            <button onclick="closeChangePasswordModal()" style="
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            ">&times;</button>
        </div>

        <form method="POST" action="{{ route('change.password.submit') }}">
            @csrf

            <!-- Current Password -->
            <div style="margin-bottom: 15px;">
                <label for="current_password" style="display: block; font-weight: 600; margin-bottom: 5px;">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       style="width: 100%; padding: 10px; border: 1px solid #B0B8C2; border-radius: 5px; font-size: 14px;">
            </div>

            <!-- New Password -->
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; font-weight: 600; margin-bottom: 5px;">New Password</label>
                <input type="password" id="password" name="password" required
                       style="width: 100%; padding: 10px; border: 1px solid #B0B8C2; border-radius: 5px; font-size: 14px;">
            </div>

            <!-- Confirm New Password -->
            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; font-weight: 600; margin-bottom: 5px;">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       style="width: 100%; padding: 10px; border: 1px solid #B0B8C2; border-radius: 5px; font-size: 14px;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeChangePasswordModal()" style="
                    background: #E4E9EE;
                    color: #333;
                    font-weight: 600;
                    border: 1px solid #B0B8C2;
                    border-radius: 8px;
                    padding: 12px 24px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: 0.2s;
                    flex: 1;
                "
                onmouseover="this.style.background='#D8E0E7'"
                onmouseout="this.style.background='#E4E9EE'">
                    Cancel
                </button>
                <button type="submit" style="
                    background: #3B6E9C;
                    color: #fff;
                    font-weight: 600;
                    border: none;
                    border-radius: 8px;
                    padding: 12px 24px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: 0.2s;
                    flex: 1;
                "
                onmouseover="this.style.background='#2E5982'"
                onmouseout="this.style.background='#3B6E9C'">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Clear Bookmarks Modal -->
<div id="clearBookmarksModal" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(212, 58, 58, 0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
">
    <div style="
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        position: relative;
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; margin: 0; color: #1E3A5F;">Clearing Bookmarks</h2>
            <button onclick="closeClearBookmarksModal()" style="
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #666;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            ">&times;</button>
        </div>

        <p style="margin-bottom: 20px; color: #555;">All the jobs that are saved in bookmark will be deleted.</p>

        <form method="POST" action="{{ route('clear.bookmarks') }}">
            @csrf

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeClearBookmarksModal()" style="
                    background: #E4E9EE;
                    color: #333;
                    font-weight: 600;
                    border: 1px solid #B0B8C2;
                    border-radius: 8px;
                    padding: 12px 24px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: 0.2s;
                    flex: 1;
                "
                onmouseover="this.style.background='#D8E0E7'"
                onmouseout="this.style.background='#E4E9EE'">
                    Cancel
                </button>
                <button type="submit" style="
                    background: #3B6E9C;
                    color: #fff;
                    font-weight: 600;
                    border: none;
                    border-radius: 8px;
                    padding: 12px 24px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: 0.2s;
                    flex: 1;
                "
                onmouseover="this.style.background='#2E5982'"
                onmouseout="this.style.background='#3B6E9C'">
                    Clear All Bookmarks
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Overlay for settings page -->
<div id="settingsOverlay" style="
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.1);
    z-index: 999;
    pointer-events: none;
"></div>

<script>
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
    document.getElementById('settingsOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
    document.getElementById('settingsOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openClearBookmarksModal() {
    document.getElementById('clearBookmarksModal').style.display = 'flex';
    document.getElementById('settingsOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeClearBookmarksModal() {
    document.getElementById('clearBookmarksModal').style.display = 'none';
    document.getElementById('settingsOverlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('changePasswordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeChangePasswordModal();
    }
});

document.getElementById('clearBookmarksModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeClearBookmarksModal();
    }
});
</script>
</div>

<!-- Font Awesome for the logout icon -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
