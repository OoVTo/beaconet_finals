<!DOCTYPE html>
<html>
<head>
    <title>Settings - BEACONET-mini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #10B981;
            --primary-dark: #059669;
            --bg: #ffffff;
            --bg-secondary: #f5f5f5;
            --text: #333;
            --text-light: #666;
            --border: #ddd;
        }
        body.dark-mode {
            --primary: #10B981;
            --primary-dark: #059669;
            --bg: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text: #ffffff;
            --text-light: #bbb;
            --border: #444;
        }
        body { font-family: Arial; background: var(--bg-secondary); color: var(--text); transition: all 0.3s; }
        .navbar { background: var(--primary); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { display: flex; gap: 10px; align-items: center; margin: 0; white-space: nowrap; }
        .navbar a { color: white; text-decoration: none; margin-right: 20px; transition: opacity 0.3s; }
        .navbar a:hover { opacity: 0.8; }
        .navbar button { background: none; border: none; color: white; cursor: pointer; transition: opacity 0.3s; }
        .navbar button:hover { opacity: 0.8; }
        .theme-toggle { background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 5px; cursor: pointer; margin-left: 15px; }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background: var(--bg); border-radius: 10px; }
        .section { margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }
        .section h2 { color: var(--primary); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: var(--text); font-weight: 500; }
        input, select { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 5px; background: var(--bg-secondary); color: var(--text); }
        .btn { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        .btn:hover { background: var(--primary-dark); }
        .toggle { display: flex; align-items: center; gap: 10px; }
        input[type="checkbox"] { width: auto; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-cog"></i> Settings</h2>
        <div>
            <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
            </button>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
            </form>
        </div>
    </div>

    <div class="container">
        <!-- Theme Settings -->
        <div class="section">
            <h2>Appearance</h2>
            <div class="form-group">
                <label for="theme">Theme</label>
                <select id="theme" onchange="updateTheme()">
                    <option value="light">Light Mode</option>
                    <option value="dark">Dark Mode</option>
                </select>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="section">
            <h2>Profile</h2>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" value="{{ auth()->user()->name }}" placeholder="Your name">
            </div>
            <button class="btn" onclick="updateProfile()">Update Profile</button>
        </div>

        <!-- Password Settings -->
        <div class="section">
            <h2>Change Password</h2>
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" id="currentPassword" placeholder="Enter current password">
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" placeholder="Confirm new password">
            </div>
            <button class="btn" onclick="updatePassword()">Change Password</button>
        </div>

        <!-- Notification Settings -->
        <div class="section">
            <h2>Notifications</h2>
            <div class="form-group toggle">
                <input type="checkbox" id="notificationsEnabled" onchange="updateNotifications()">
                <label for="notificationsEnabled">Enable Notifications</label>
            </div>
        </div>
    </div>

    <script>
        // Initialize dark mode from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadPreferences();
        });

        function loadPreferences() {
            fetch('{{ route("settings.preferences") }}')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('theme').value = data.theme;
                    document.getElementById('notificationsEnabled').checked = data.notifications_enabled;
                });
        }

        function updateTheme() {
            const theme = document.getElementById('theme').value;
            fetch('{{ route("settings.theme") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ theme })
            })
            .then(r => r.json())
            .then(() => alert('Theme updated!'))
            .catch(e => alert('Error: ' + e));
        }

        function updateProfile() {
            const name = document.getElementById('name').value;
            fetch('{{ route("settings.profile") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name })
            })
            .then(r => r.json())
            .then(() => alert('Profile updated!'))
            .catch(e => alert('Error: ' + e));
        }

        function updatePassword() {
            const current_password = document.getElementById('currentPassword').value;
            const password = document.getElementById('newPassword').value;
            const password_confirmation = document.getElementById('confirmPassword').value;

            if (password !== password_confirmation) {
                alert('Passwords do not match');
                return;
            }

            fetch('{{ route("settings.password") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password, password, password_confirmation })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    alert('Password updated!');
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    document.getElementById('confirmPassword').value = '';
                }
            })
            .catch(e => alert('Error: ' + e));
        }

        function updateNotifications() {
            const notifications_enabled = document.getElementById('notificationsEnabled').checked;
            fetch('{{ route("settings.notifications") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notifications_enabled })
            })
            .then(r => r.json())
            .then(() => alert('Notification settings updated!'))
            .catch(e => alert('Error: ' + e));
        }
    </script>
</body>
</html>
