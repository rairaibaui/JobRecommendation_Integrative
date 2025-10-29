@extends('layouts.recommendation')

@section('content')

    @if(session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <style>
        /* Flash Messages */
        .success-message,
        .error-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }

        .success-message {
            background: #4CAF50;
        }

        .error-message {
            background: #f44336;
        }

        /* Tabs */
        .tab.active {
            border-bottom: 2px solid #fff;
            color: #fff !important;
            cursor: pointer;
        }

        .tab {
            cursor: pointer;
        }

        /* Setting Items */
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-top: 1px solid #E0E6EB;
            padding: 15px 0;
        }

        .setting-item:first-child {
            border-top: none;
        }

        .setting-item i {
            font-size: 20px;
            color: #1E3A5F;
            margin-top: 2px;
        }

        /* Buttons */
        .edit-btn {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .edit-btn:hover {
            background-color: #f3f3f3;
        }

        .delete-btn {
            border-color: #e74c3c;
            color: #e74c3c;
        }

        .delete-btn:hover {
            background-color: #e74c3c;
            color: white;
        }

        /* Profile Settings Form */
        .profile-card form label {
            display: block;
            font-weight: 600;
            color: #1E3A5F;
            margin-bottom: 6px;
        }

        .profile-card input[type="text"],
        .profile-card input[type="email"],
        .profile-card input[type="file"],
        .profile-card input[type="number"],
        .profile-card input[type="date"],
        .profile-card textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px;
        }

        .profile-card input:focus,
        .profile-card textarea:focus {
            border-color: #1E3A5F;
            outline: none;
        }

        .profile-card textarea {
            min-height: 100px;
            resize: vertical;
        }

        .profile-card button[type="submit"] {
            background: #1E3A5F;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: 0.2s ease;
        }

        .profile-card button[type="submit"]:hover {
            background: #2c4c7a;
        }

        .profile-card img {
            margin-top: 10px;
            border: 2px solid #ddd;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Modal Styling */
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
            transition: 0.2s ease;
        }

        .close-btn:hover {
            background: #f0f0f0;
            color: #333;
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
        }

        .btn-primary:hover {
            background: #2c4c7a;
        }
    </style>

    <div class="main">
        <div class="top-navbar">
            <i class="fas fa-bars hamburger"></i> Job Portal - Mandaluyong
        </div>

        <div class="card-large">
            <div class="recommendation-header">
                <h3 style="color: #fff;">Settings</h3>
                <p style="color: #fff;">Manage your account preferences, profile, and security settings.</p>
            </div>

            <div class="settings-container" style="margin-top:30px;">
                <!-- Tabs -->
                <div class="tabs" style="display:flex; border-bottom:1px solid #8AA4B8; margin-bottom:20px;">
                    <div class="tab active" style="margin-right:30px; padding-bottom:10px; font-weight:600; color:#fff;">
                        Account Settings</div>
                    <div class="tab" style="margin-right:30px; padding-bottom:10px; font-weight:600; color:#D3DCE3;">Profile
                        Settings</div>
                </div>

                <!-- Account Settings -->
                <div class="card-large account-card" style="background:#fff; color:#000; border-radius:10px; padding:25px;">
                    <h2 style="color:#1E3A5F;">Account Settings</h2>
                    <div class="setting-item">
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <i class="fas fa-envelope"></i>
                            <div><strong>Email</strong>
                                <p>Update your account email address.</p>
                            </div>
                        </div>
                        <button class="edit-btn" onclick="openChangeEmailModal()">Change Email</button>
                    </div>
                    <div class="setting-item">
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <i class="fas fa-lock"></i>
                            <div><strong>Password</strong>
                                <p>Change your password to keep your account secure.</p>
                            </div>
                        </div>
                        <button class="edit-btn" onclick="openChangePasswordModal()">Change Password</button>
                    </div>
                    <div class="setting-item">
                        <div style="display:flex; align-items:flex-start; gap:12px;">
                            <i class="fas fa-user-times"></i>
                            <div><strong>Delete Account</strong>
                                <p>Permanently delete your account and all data.</p>
                            </div>
                        </div>
                        <button class="edit-btn delete-btn" onclick="openDeleteAccountModal()">Delete</button>
                    </div>
                </div>

                <!-- Profile Settings -->
                <div class="card-large profile-card"
                    style="display:none; background:#fff; color:#000; border-radius:10px; padding:25px;">
                    <h2 style="color:#1E3A5F;">Profile Settings</h2>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <label>First Name</label>
                        <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" required>

                        <label>Last Name</label>
                        <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" required>

                        <label>Email</label>
                        <input type="email" value="{{ Auth::user()->email }}" readonly>

                        <label>Date of Birth</label>
                        <input type="date" name="birthday" value="{{ Auth::user()->birthday }}">

                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="{{ Auth::user()->phone_number }}">

                        <label>Education Level</label>
                        <input type="text" name="education_level" value="{{ Auth::user()->education_level }}">

                        <label>Skills (Comma separated)</label>
                        <input type="text" name="skills" value="{{ Auth::user()->skills }}">

                        <label>Years of Experience</label>
                        <input type="number" name="years_of_experience" value="{{ Auth::user()->years_of_experience }}">

                        <label>Location (Brgy. in Mandaluyong City)</label>
                        <input type="text" name="location" value="{{ Auth::user()->location }}">

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

                        <button type="submit" class="edit-btn">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('settings.modals')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
   <script>
document.addEventListener('DOMContentLoaded', () => {
    // ===== Tabs =====
    const tabs = document.querySelectorAll('.tabs .tab');
    const accountCard = document.querySelector('.account-card');
    const profileCard = document.querySelector('.profile-card');

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            accountCard.style.display = i === 0 ? 'block' : 'none';
            profileCard.style.display = i === 1 ? 'block' : 'none';
        });
    });

    // ===== Modals =====
    const overlay = document.getElementById('settingsOverlay');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const changeEmailModal = document.getElementById('changeEmailModal');
    const deleteAccountModal = document.getElementById('deleteAccountModal');

    window.openChangePasswordModal = () => { changePasswordModal.style.display = 'flex'; overlay.style.display = 'block'; document.body.style.overflow = 'hidden'; }
    window.closeChangePasswordModal = () => { changePasswordModal.style.display = 'none'; overlay.style.display = 'none'; document.body.style.overflow = 'auto'; }

    window.openChangeEmailModal = () => { changeEmailModal.style.display = 'flex'; overlay.style.display = 'block'; document.body.style.overflow = 'hidden'; }
    window.closeChangeEmailModal = () => { changeEmailModal.style.display = 'none'; overlay.style.display = 'none'; document.body.style.overflow = 'auto'; }

    window.openDeleteAccountModal = () => { deleteAccountModal.style.display = 'flex'; overlay.style.display = 'block'; document.body.style.overflow = 'hidden'; }
    window.closeDeleteAccountModal = () => { deleteAccountModal.style.display = 'none'; overlay.style.display = 'none'; document.body.style.overflow = 'auto'; }

    // ===== Flash Messages Auto-hide =====
    const successMsg = document.querySelector('.success-message');
    const errorMsg = document.querySelector('.error-message');

    if (successMsg) {
        setTimeout(() => {
            successMsg.style.transition = 'opacity 0.5s';
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }, 2000); // 2 seconds
    }

    if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.transition = 'opacity 0.5s';
            errorMsg.style.opacity = '0';
            setTimeout(() => errorMsg.remove(), 500);
        }, 2000); // 2 seconds
    }
});
</script>

@endsection
