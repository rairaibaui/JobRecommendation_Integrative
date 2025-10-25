@extends('layouts.recommendation')

@section('content')
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
            <div class="card" style="background: #fff; color: #000; border-radius: 10px; box-shadow: 0px 3px 10px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 20px 25px;">
                <h2 style="font-size: 18px; margin-bottom: 15px; color: #1E3A5F;">Account Settings</h2>

                <!-- Password Setting -->
                <div class="setting-item" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #E0E6EB; padding: 15px 0;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-lock" style="font-size: 16px; color: #1E3A5F;"></i>
                        <div>
                            <strong>Password</strong>
                            <p style="margin: 0; font-size: 14px; color: #555;">Change your password to keep your account secure.</p>
                        </div>
                    </div>
                    <button style="
                        background: #E4E9EE;
                        border: 1px solid #B0B8C2;
                        border-radius: 5px;
                        padding: 8px 16px;
                        font-size: 14px;
                        cursor: pointer;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.background='#D8E0E7'; this.style.boxShadow='0 3px 8px rgba(0,0,0,0.25)'"
                    onmouseout="this.style.background='#E4E9EE'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.15)'">
                        Change Password
                    </button>
                </div>

                <!-- Bookmark Setting (with icon and shadow) -->
                <div class="setting-item" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #E0E6EB; padding: 15px 0;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bookmark" style="font-size: 16px; color: #1E3A5F;"></i>
                        <div>
                            <strong>Bookmark</strong>
                            <p style="margin: 0; font-size: 14px; color: #555;">Remove all saved jobs from your bookmarks.</p>
                        </div>
                    </div>
                    <button style="
                        background: #E4E9EE;
                        border: 1px solid #B0B8C2;
                        border-radius: 5px;
                        padding: 8px 16px;
                        font-size: 14px;
                        cursor: pointer;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.background='#D8E0E7'; this.style.boxShadow='0 3px 8px rgba(0,0,0,0.25)'"
                    onmouseout="this.style.background='#E4E9EE'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.15)'">
                        Clear All Bookmarks
                    </button>
                </div>
            </div>

            <!-- Logout Card -->
            <div class="card" style="background: #fff; color: #000; border-radius: 10px; padding: 20px 25px;">
                <h2 style="font-size: 18px; margin-bottom: 15px; color: #1E3A5F;">Session</h2>
                <button style="
                    background: #3B6E9C;
                    color: #fff;
                    font-weight: 600;
                    border: none;
                    border-radius: 6px;
                    padding: 10px 20px;
                    cursor: pointer;
                    transition: 0.2s;
                "
                onmouseover="this.style.background='#2E5982'"
                onmouseout="this.style.background='#3B6E9C'">
                    Log out
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
