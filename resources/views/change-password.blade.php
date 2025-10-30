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
        border-radius: 12px;
        padding: 30px 40px;
        width: 400px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        z-index: 20;
    ">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <div style="
                background: #3B6E9C;
                color: #fff;
                border-radius: 50%;
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <i class="fas fa-lock"></i>
            </div>
            <h2 style="font-size: 18px; color: #1E3A5F; margin: 0;">Change Password</h2>
        </div>

        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Please enter your current password and a new password.
        </p>

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

            <!-- Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <!-- Cancel Button -->
                <a href="{{ route('settings') }}" style="
                    background: #E4E9EE;
                    color: #000;
                    font-weight: 600;
                    border: 1px solid #B0B8C2;
                    border-radius: 6px;
                    padding: 10px 20px;
                    cursor: pointer;
                    font-size: 14px;
                    text-decoration: none;
                    transition: 0.2s;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                "
                onmouseover="this.style.background='#D8E0E7'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.2)';"
                onmouseout="this.style.background='#E4E9EE'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.15)';">
                    Cancel
                </a>

                <!-- Change Password Button -->
                <button type="submit" style="
                    background: #3B6E9C;
                    color: #fff;
                    font-weight: 600;
                    border: none;
                    border-radius: 6px;
                    padding: 10px 20px;
                    cursor: pointer;
                    font-size: 14px;
                    transition: 0.2s;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                "
                onmouseover="this.style.background='#2E5982'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.2)';"
                onmouseout="this.style.background='#3B6E9C'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.15)';">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

