<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- ✅ Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #406482 0%, #5C7A94 100%);
            font-family: "Roboto", sans-serif;
        }

        .login-box {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 40px 50px;
            width: 380px;
            text-align: center;
        }

        h1 {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 30px;
            color: #1a1a1a;
        }

        label {
            display: block;
            text-align: left;
            font-size: 14px;
            color: #1a1a1a;
            margin-bottom: 6px;
            font-weight: 500;
        }

        input {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #cfcfcf;
            padding: 8px 10px;
            margin-bottom: 18px;
            font-size: 14px;
            font-family: "Roboto", sans-serif;
        }

        input::placeholder {
            color: #aaa;
        }

        .btn-signin {
            width: 305px;
            height: 40px;
            background: linear-gradient(90deg, #6C9AC4 0%, #334A5E 100%);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .btn-signin:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 25px 0 15px;
        }

        .small-text {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .signup-btn {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .signup-btn:hover {
            background: #f3f3f3;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.25);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: -10px;
            margin-bottom: 18px;
        }

        .form-options label {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-options input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin: 0;
            vertical-align: middle;
        }

        .form-options a {
            font-size: 13px;
            color: #406482;
            text-decoration: none;
            transition: 0.2s;
            font-family: "Roboto", sans-serif;
        }

        .form-options a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-box">
    <h1>WELCOME</h1>

    <!-- ✅ Success Message After Registration -->
    @if(session('success'))
        <div style="color: green; margin-bottom: 15px; text-align: center;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->has('email'))
        <div style="color: red; margin-bottom: 15px; text-align: center;">
            {{ $errors->first('email') }}
        </div>
    @endif
    @if($errors->has('password'))
        <div style="color: red; margin-bottom: 15px; text-align: center;">
            {{ $errors->first('password') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="ex. juandelacruz@gmail.com" value="{{ session('email') ?? old('email') }}" required autofocus>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>

        <div class="form-options">
            <label>
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
            </label>
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>

        <button type="submit" class="btn-signin">Sign In</button>
    </form>

    <hr>

    <p class="small-text">Don't have an account yet?</p>
    <a href="{{ route('register') }}">
        <button class="signup-btn">Sign up</button>
    </a>
</div>

