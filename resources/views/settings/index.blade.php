<!DOCTYPE html>
<html>
<head>
    <title>Settings - BEACONET-mini</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .section h2 { color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #5568d3; }
        .toggle { display: flex; align-items: center; gap: 10px; }
        input[type="checkbox"] { width: auto; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Settings</h2>
        <div>
            <a href="{{ route('dashboard') }}" style="color: white; margin-right: 20px;">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
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
