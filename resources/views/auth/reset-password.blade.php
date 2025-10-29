<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

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

        .reset-password-box {
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

        input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
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

        .password-requirements {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }

        .password-requirements p {
            margin: 0 0 8px 0;
            color: #1565c0;
            font-size: 13px;
            font-weight: 500;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #1976d2;
            font-size: 13px;
        }

        .password-requirements li {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="reset-password-box">
        <div class="icon-header">
            <i class="fas fa-lock"></i>
        </div>

        <h1>Reset Password</h1>
        <p class="description">
            Enter your new password below to reset your account password.
        </p>

        @if($errors->any())
            <div class="error-message">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <div class="password-requirements">
            <p><i class="fas fa-info-circle" style="margin-right:6px;"></i>Password Requirements:</p>
            <ul>
                <li>Minimum 8 characters</li>
                <li>Must match confirmation</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email"><i class="fas fa-envelope" style="margin-right:6px;"></i>Email Address</label>
            <input type="email" name="email" id="email" value="{{ $email ?? old('email') }}" required readonly>

            <label for="password"><i class="fas fa-lock" style="margin-right:6px;"></i>New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password (min. 8 characters)" required autofocus>

            <label for="password_confirmation"><i class="fas fa-check-circle" style="margin-right:6px;"></i>Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm new password" required>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save" style="margin-right:8px;"></i>Reset Password
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-to-login">
            <i class="fas fa-arrow-left"></i>
            Back to Login
        </a>
    </div>
</body>
</html>
