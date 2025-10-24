<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Mandaluyong</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="header">
        <div class="hamburger"></div>
        <h1>Job Portal - Mandaluyong</h1>
    </header>

    <div class="container">
        <aside class="sidebar">
            <div class="profile">
                <div class="profile-pic"></div>
                <div class="profile-icon"></div>
                <p class="profile-name">Arkin Sanchez</p>
            </div>
            <nav>
                <button class="active">🏠 Dashboard</button>
                <button>💡 Recommendation</button>
                <button>🔖 Bookmarks</button>
                <button>⚙️ Settings</button>
            </nav>
            <button class="logout">Log out</button>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
