<!DOCTYPE html>
<html>
<head>
    <title>Notifications - BEACONET-mini</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .notification { background: white; padding: 20px; margin-bottom: 15px; border-radius: 10px; border-left: 4px solid #667eea; }
        .notification.unread { background: #f0f4ff; }
        .notification h3 { color: #333; margin-bottom: 10px; }
        .notification p { color: #666; margin-bottom: 10px; }
        .notification img { max-width: 300px; border-radius: 5px; margin: 10px 0; }
        .btn { padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; }
        a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Notifications</h2>
        <div>
            <a href="{{ route('dashboard') }}" style="color: white; margin-right: 20px;">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="display: inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn">Mark All as Read</button>
            </form>
        </div>

        <div id="notifications"></div>
    </div>

    <script>
        function loadNotifications() {
            fetch('{{ route("notifications.index") }}')
                .then(r => r.json())
                .then(notifications => {
                    const container = document.getElementById('notifications');
                    if (notifications.length === 0) {
                        container.innerHTML = '<p>No notifications yet</p>';
                        return;
                    }
                    
                    container.innerHTML = notifications.map(n => `
                        <div class="notification ${!n.is_read ? 'unread' : ''}">
                            <h3>${n.title}</h3>
                            <p>${n.message}</p>
                            ${n.image_path ? `<img src="/storage/${n.image_path}" alt="">` : ''}
                            <small>${new Date(n.created_at).toLocaleString()}</small>
                            <div style="margin-top: 10px;">
                                ${!n.is_read ? `<form method="POST" action="/notifications/${n.id}/read" style="display: inline;"><button type="submit" class="btn" style="background: #4CAF50;">Mark as Read</button></form>` : ''}
                                <form method="POST" action="/notifications/${n.id}" style="display: inline;"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn" style="background: #d32f2f;">Delete</button></form>
                            </div>
                        </div>
                    `).join('');
                });
        }

        document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>
</body>
</html>
