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
        .sidebar img {
            width: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .sidebar h3 {
            font-size: 14px;
            margin-bottom: 30px;
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
            box-shadow: 0 3px 5px rgba(0,0,0,0.2);
        }
        .sidebar .logout {
            margin-top: auto;
            margin-bottom: 20px;
            background-color: #648EB5;
            color: #fff;
            text-align: center;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        header h1 {
            font-size: 18px;
            font-weight: 600;
        }
        .content-box {
            background: #f9f9f9;
            color: #000;
            padding: 20px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
            <h3>{{ Auth::user()->name ?? 'Guest' }}</h3>
            <a href="/dashboard"><i>🏠</i> Dashboard</a>
            <a href="/recommendation"><i>📘</i> Recommendation</a>
            <a href="/bookmarks"><i>🔖</i> Bookmarks</a>
            <a href="/settings" class="active"><i>⚙️</i> Settings</a>
            <a href="/logout" class="logout">↪ Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Job Portal - Mandaluyong</h1>
            </header>
            <div class="content-box">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
