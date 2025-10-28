<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Mandaluyong</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #334A5E 0%, #648EB5 100%);
            color: #fff;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 220px;
            background-color: #fff;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            border-right: 2px solid #648EB5;
        }

        .profile-ellipse {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(73, 118, 159, 0.44) 48%, rgba(78, 142, 162, 0.44) 86%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .profile-icon img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #fff;
        }

        .sidebar h3 {
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: #000;
            width: 80%;
            padding: 10px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar a.active {
            background-color: #648EB5;
            color: #fff;
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar .logout {
            margin-top: auto;
            margin-bottom: 20px;
            width: 80%;
            padding: 10px;
            background-color: #648EB5;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile-ellipse">
                <div class="profile-icon">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture">
                    @else
                        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Default Profile">
                    @endif
                </div>
            </div>

            <h3>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h3>

            <a href="{{ route('dashboard') }}"><i>🏠</i> Dashboard</a>
            <a href="{{ route('recommendation') }}"><i>📘</i> Recommendation</a>
            <a href="{{ route('bookmarks') }}"><i>🔖</i> Bookmarks</a>
            <a href="{{ route('settings') }}" class="active"><i>⚙️</i> Settings</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout">↪ Logout</button>
            </form>
        </div>

        <div class="main-content">
            @yield('content')
        </div>
    </div>
</body>

</html>