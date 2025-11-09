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
    padding: 20px 20px;
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
            message.style.animation = 'fadeOut 0.3s ease-out forwards';
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
    padding: 20px 20px;
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
            message.style.animation = 'fadeOut 0.3s ease-out forwards';
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
<div class="main" style="position: relative;">
    <div class="top-navbar" style="display:flex; justify-content:space-between; align-items:center;">
        <div>Job Portal - Mandaluyong</div>
        @include('partials.notifications')
    </div>

    <!-- Background overlay (soft white transparent) -->
<div style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(103, 95, 95, 0.75); /* white + transparency */
    backdrop-filter: blur(3px); /* optional soft blur */
    z-index: 10;
"></div>

    <!-- Change Password Modal -->
    <div style="
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        color: #000;
        border-radius: 16px;
        padding: 32px;
        width: 420px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        z-index: 20;
    ">
        <button onclick="window.history.back()" style="position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.1); color: #666; width: 40px; height: 40px; border-radius: 50%; font-size: 20px; border: none; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;">&times;</button>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 25px;">
            <i class="fas fa-lock" style="color: #5B9BD5; font-size: 24px;"></i>
            <h2 style="font-size: 22px; color: #2C3E50; margin: 0; font-weight: 600;">Change Password</h2>
        </div>

        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Please enter your current password and a new password.
        </p>

        <form method="POST" action="{{ route('change.password.submit') }}">
            @csrf

            <!-- Current Password -->
            <div style="margin-bottom: 18px;">
                <label for="current_password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Current Password</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password" required
                       style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s; focus:ring-2 focus:ring-blue-500 focus:border-blue-500;">
            </div>

            <!-- New Password -->
            <div style="margin-bottom: 18px;">
                <label for="password" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">New Password</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required
                       style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s; focus:ring-2 focus:ring-blue-500 focus:border-blue-500;">
            </div>

            <!-- Confirm New Password -->
            <div style="margin-bottom: 24px;">
                <label for="password_confirmation" style="display: block; font-weight: 600; color: #2C3E50; margin-bottom: 8px; font-size: 14px;">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required
                       style="width: 100%; padding: 14px 16px; border: 2px solid #E0E6EB; border-radius: 12px; font-size: 14px; box-sizing: border-box; transition: all 0.3s; focus:ring-2 focus:ring-blue-500 focus:border-blue-500;">
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <!-- Cancel Button -->
                <a href="{{ route('settings') }}" style="
                    padding: 14px 26px;
                    background: transparent;
                    color: #64748b;
                    border: 2px solid #e2e8f0;
                    border-radius: 12px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 600;
                    text-decoration: none;
                    transition: all 0.3s;
                    hover:bg-gray-50 hover:border-gray-300;
                "
                onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e0';"
                onmouseout="this.style.background='transparent'; this.style.borderColor='#e2e8f0';">
                    Cancel
                </a>

                <!-- Change Password Button -->
                <button type="submit" style="
                    padding: 14px 26px;
                    background: #5B9BD5;
                    color: white;
                    border: none;
                    border-radius: 12px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 600;
                    transition: all 0.3s;
                    box-shadow: 0 4px 12px rgba(91, 155, 213, 0.3);
                "
                onmouseover="this.style.background='#4a8bc4'; this.style.boxShadow='0 6px 16px rgba(91, 155, 213, 0.4)';"
                onmouseout="this.style.background='#5B9BD5'; this.style.boxShadow='0 4px 12px rgba(91, 155, 213, 0.3)';">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

