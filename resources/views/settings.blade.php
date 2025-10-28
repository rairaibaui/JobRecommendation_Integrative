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
        /* Flash messages */
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
            animation: slideIn 0.3s ease-out;
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

        /* Tabs */
        .tab.active {
            border-bottom: 2px solid #fff;
            color: #fff !important;
            cursor: pointer;
        }

        .tab {
            cursor: pointer;
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

                <!-- Change Email -->
                <div class="setting-item"
                    style="display:flex; justify-content:space-between; align-items:flex-start; border-top:1px solid #E0E6EB; padding:15px 0;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <i class="fas fa-envelope" style="font-size:20px; color:#1E3A5F; margin-top:2px;"></i>
                        <div>
                            <strong>Email</strong>
                            <p>Update your account email address.</p>
                        </div>
                    </div>
                    <button onclick="openChangeEmailModal()">Change Email</button>
                </div>

                <!-- Change Password -->
                <div class="setting-item"
                    style="display:flex; justify-content:space-between; align-items:flex-start; border-top:1px solid #E0E6EB; padding:15px 0;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <i class="fas fa-lock" style="font-size:20px; color:#1E3A5F; margin-top:2px;"></i>
                        <div>
                            <strong>Password</strong>
                            <p>Change your password to keep your account secure.</p>
                        </div>
                    </div>
                    <button onclick="openChangePasswordModal()">Change Password</button>
                </div>

                <!-- Delete Account -->
                <div class="setting-item"
                    style="display:flex; justify-content:space-between; align-items:flex-start; border-top:1px solid #E0E6EB; padding:15px 0;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <i class="fas fa-user-times" style="font-size:20px; color:#1E3A5F; margin-top:2px;"></i>
                        <div>
                            <strong>Delete Account</strong>
                            <p>Permanently delete your account and all data.</p>
                        </div>
                    </div>
                    <button onclick="openDeleteAccountModal()" style="background:#e74c3c; color:#fff;">Delete</button>
                </div>
            </div>


                <!-- Profile Settings -->
                <div class="card-large profile-card"
                    style="display:none; background:#fff; color:#000; border-radius:10px; padding:25px;">
                    <h2 style="color:#1E3A5F;">Profile Settings</h2>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div>
                            <label>First Name</label>
                            <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" required>
                        </div>

                        <div>
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" required>
                        </div>

                        <div>
                            <label>Email Address</label>
                            <input type="email" value="{{ Auth::user()->email }}" readonly>
                        </div>

                        <div>
                            <label>Date of Birth</label>
                            <input type="date" name="birthday" value="{{ Auth::user()->birthday }}">
                        </div>

                        <div>
                            <label>Phone Number</label>
                            <input type="text" name="phone_number" value="{{ Auth::user()->phone_number }}">
                        </div>

                        <div>
                            <label>Education Level</label>
                            <input type="text" name="education_level" value="{{ Auth::user()->education_level }}">
                        </div>

                        <div>
                            <label>Skills (Comma separated)</label>
                            <input type="text" name="skills" value="{{ Auth::user()->skills }}">
                        </div>

                        <div>
                            <label>Years of Experience</label>
                            <input type="number" name="years_of_experience" value="{{ Auth::user()->years_of_experience }}">
                        </div>

                        <div>
                            <label>Location (Brgy. in Mandaluyong City)</label>
                            <input type="text" name="location" value="{{ Auth::user()->location }}">
                        </div>

                    <div style="margin-bottom:15px;">
                            <label>Profile Picture</label>
                            <input type="file" name="profile_picture" accept="image/*">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                    style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
                            @endif

                        </div>
                        <button type="submit">Update Profile</button>
                    </form>
                </div>


                <!-- Modals -->
                @include('settings.modals')


    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // Modals
            const overlay = document.getElementById('settingsOverlay');
            const changePasswordModal = document.getElementById('changePasswordModal');
            const changeEmailModal = document.getElementById('changeEmailModal');
            const deleteAccountModal = document.getElementById('deleteAccountModal');

            window.openChangePasswordModal = () => { changePasswordModal.style.display = 'flex'; overlay.style.display = 'block'; document.body.style.overflow = 'hidden'; }
            window.closeChangePasswordModal = () => { changePasswordModal.style.display = 'none'; overlay.style.display = 'none'; document.body.style.overflow = 'auto'; }

            window.openChangeEmailModal = () => { changeEmailModal.style.display = 'flex'; overlay.style.display = 'block'; document.body.style.overflow = 'hidden'; }
            window.closeChangeEmailModal = () => { changeEmailModal.style.display = 'none'; overlay.style.display = 'none'; document.body.style.overflow = 'auto'; }

            function openDeactivateModal() { showModal('deactivateModal'); }
            function closeDeactivateModal() { hideModal('deactivateModal'); }

            // Close modal when clicking outside
            [changePasswordModal, changeEmailModal, deleteAccountModal].forEach(modal => {
                modal.addEventListener('click', e => {
                    if (e.target === modal) modal.style.display = 'none';
                    overlay.style.display = 'none';
                    document.body.style.overflow = 'auto';
                });
            });
        });
    </script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection