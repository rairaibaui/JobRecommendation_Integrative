<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>

    <!-- Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

        .forgot-password-box {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 40px 50px;
            width: 420px;
            text-align: center;
        }

        .icon-header {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #648EB5, #406482);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
        }

        .icon-header i {
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1a1a1a;
        }

        .description {
            font-size: 14px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        label {
            display: block;
            text-align: left;
            font-size: 14px;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input {
            width: 100%;
            height: 45px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 8px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            font-family: "Roboto", sans-serif;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #648EB5;
            box-shadow: 0 0 0 4px rgba(100, 142, 181, 0.1);
        }

        input::placeholder {
            color: #aaa;
        }

        .btn-submit {
            width: 100%;
            height: 45px;
            background: linear-gradient(135deg, #648EB5, #406482);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(100, 142, 181, 0.3);
            margin-bottom: 20px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(100, 142, 181, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-to-login {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #648EB5;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-to-login:hover {
            color: #406482;
        }

        .back-to-login i {
            font-size: 16px;
        }

        .success-message {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .success-message i {
            font-size: 20px;
        }

        .error-message {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="forgot-password-box">
        <div class="icon-header">
            <i class="fas fa-key"></i>
        </div>

        <h1>Forgot Password?</h1>
        <p class="description">
            No worries! Enter your email address and we'll send you a link to reset your password.
        </p>

        @include('partials.flash')

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email"><i class="fas fa-envelope" style="margin-right:6px;"></i>Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email address" value="{{ old('email') }}" required autofocus>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane" style="margin-right:8px;"></i>Send Reset Link
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-to-login">
            <i class="fas fa-arrow-left"></i>
            Back to Login
        </a>
    </div>
</body>
</html>
