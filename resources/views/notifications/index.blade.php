<!DOCTYPE html>
<html>
<head>
    <title>Notifications - BEACONET-mini</title>
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
            --border: #eee;
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
        .navbar { background: var(--primary); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 999; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex-wrap: nowrap; }
        .navbar > div { display: flex; align-items: center; gap: 0; flex-wrap: nowrap; }
        .navbar h2 { display: flex; gap: 10px; align-items: center; margin: 0; white-space: nowrap; }
        .navbar a, .navbar button { color: white; text-decoration: none; cursor: pointer; border: none; background: none; font-size: 16px; transition: opacity 0.3s; margin-left: 20px; display: flex; gap: 8px; align-items: center; white-space: nowrap; }
        .navbar a:hover, .navbar button:hover { opacity: 0.8; }
        .theme-toggle { background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; margin-top: 70px; }
        .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .inbox-list { background: var(--bg); border-radius: 10px; overflow: hidden; }
        .inbox-item { border-bottom: 1px solid var(--border); padding: 15px 20px; cursor: pointer; transition: background 0.2s; display: flex; justify-content: space-between; align-items: center; background: var(--bg); color: var(--text); }
        .inbox-item:hover { background: var(--bg-secondary); }
        .inbox-item.unread { background: var(--bg-secondary); border-left: 4px solid var(--primary); }
        .inbox-item-content { flex: 1; }
        .inbox-item-sender { font-weight: bold; color: var(--text); }
        .inbox-item-preview { color: var(--text-light); font-size: 14px; margin-top: 5px; }
        .inbox-item-date { color: var(--text-light); font-size: 12px; margin-top: 5px; }
        .inbox-item-badge { background: #ef4444; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        .notification-detail { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000; }
        .notification-detail.active { display: flex; }
        .detail-content { background: var(--bg); padding: 40px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; color: var(--text); }
        .detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
        .detail-close { background: none; border: none; font-size: 28px; cursor: pointer; color: var(--text-light); }
        .detail-from { margin-bottom: 15px; }
        .detail-from label { color: var(--text-light); font-size: 12px; }
        .detail-from p { font-weight: bold; color: var(--text); margin-top: 3px; }
        .detail-image { max-width: 100%; border-radius: 5px; margin: 20px 0; }
        .detail-message { background: var(--bg-secondary); padding: 15px; border-radius: 5px; margin: 20px 0; color: var(--text); line-height: 1.6; }
        .detail-actions { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .btn { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background 0.3s; display: flex; gap: 8px; align-items: center; }
        .btn:hover { background: var(--primary-dark); }
        .btn-success { background: #10b981; }
        .btn-success:hover { background: #059669; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .empty-message { text-align: center; padding: 40px; color: var(--text-light); }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-bell"></i> Notifications</h2>
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
        <div class="inbox-header">
            <h3>Inbox</h3>
            <button class="btn" onclick="markAllAsRead()" style="width: auto;"><i class="fas fa-envelope-open"></i> Mark All as Read</button>
        </div>

        <div class="inbox-list" id="inboxList"><div class="empty-message">Loading...</div></div>
    </div>

    <!-- Notification Detail Modal -->
    <div class="notification-detail" id="detailModal">
        <div class="detail-content">
            <div class="detail-header">
                <h2>Found Item Report</h2>
                <button class="detail-close" onclick="closeDetail()">&times;</button>
            </div>
            
            <div class="detail-from">
                <label>From:</label>
                <p id="detailFrom"></p>
            </div>

            <div id="detailImage"></div>

            <div class="detail-message" id="detailMessage"></div>

            <div class="detail-actions">
                <button class="btn" id="messageBtn" onclick="openMessaging()" style="background: #3b82f6;"><i class="fas fa-comments"></i> Message</button>
                <button class="btn btn-success" id="markReceivedBtn" onclick="markItemReceived()"><i class="fas fa-check"></i> I Received This Item</button>
                <button class="btn btn-danger" onclick="deleteCurrentNotification()"><i class="fas fa-trash"></i> Delete</button>
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

        let currentNotificationId = null;
        let currentFoundReportId = null;
        let currentLostItemId = null;

        function loadNotifications() {
            fetch('/api/notifications')
                .then(r => r.json())
                .then(notifications => {
                    console.log('Notifications loaded:', notifications);
                    const container = document.getElementById('inboxList');
                    if (notifications.length === 0) {
                        container.innerHTML = '<div class="empty-message">No notifications</div>';
                        return;
                    }
                    
                    container.innerHTML = notifications.map(n => {
                        if (!n.found_report || !n.found_report.reporter || !n.found_report.lost_item) {
                            console.error('Invalid notification structure:', n);
                            return '';
                        }
                        return `
                            <div class="inbox-item ${!n.is_read ? 'unread' : ''}" onclick="openNotification(${n.id}, ${n.found_report_id}, ${n.found_report.lost_item.id}, '${n.is_read}')">
                                <div class="inbox-item-content">
                                    <div class="inbox-item-sender">${n.found_report.reporter.name} found your item</div>
                                    <div class="inbox-item-preview">${n.found_report.message.substring(0, 60)}...</div>
                                    <div class="inbox-item-date">${new Date(n.created_at).toLocaleDateString()}</div>
                                </div>
                                ${!n.is_read ? '<div class="inbox-item-badge">NEW</div>' : ''}
                            </div>
                        `;
                    }).join('');
                })
                .catch(e => {
                    console.error('Error loading notifications:', e);
                    document.getElementById('inboxList').innerHTML = '<div class="empty-message">Error loading notifications</div>';
                });
        }

        function openNotification(notificationId, foundReportId, lostItemId, isRead) {
            currentNotificationId = notificationId;
            currentFoundReportId = foundReportId;
            currentLostItemId = lostItemId;

            fetch('/api/notifications')
                .then(r => r.json())
                .then(notifications => {
                    const notif = notifications.find(n => n.id == notificationId);
                    if (!notif) return;

                    const reporter = notif.found_report.reporter;
                    const message = notif.found_report.message;
                    const imagePath = notif.found_report.image_path;

                    document.getElementById('detailFrom').textContent = reporter.name;
                    document.getElementById('detailMessage').textContent = message;
                    
                    const imageDiv = document.getElementById('detailImage');
                    if (imagePath) {
                        imageDiv.innerHTML = `<img src="/storage/${imagePath}" class="detail-image" alt="Found item">`;
                    } else {
                        imageDiv.innerHTML = '';
                    }

                    document.getElementById('detailModal').classList.add('active');

                    // Mark as read if not already
                    if (!isRead) {
                        markAsRead(notificationId);
                    }
                })
                .catch(e => console.error('Error:', e));
        }

        function closeDetail() {
            document.getElementById('detailModal').classList.remove('active');
            currentNotificationId = null;
            currentFoundReportId = null;
            currentLostItemId = null;
        }

        function openMessaging() {
            if (!currentFoundReportId) {
                alert('Unable to open messaging. Please try again.');
                return;
            }

            // Redirect to messaging for this found report
            window.location.href = `/found-reports/${currentFoundReportId}/message`;
        }

        function markAsRead(id) {
            fetch('{{ route("notifications.read", ":id") }}'.replace(':id', id), {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => loadNotifications())
            .catch(e => console.error('Error:', e));
        }

        function markItemReceived() {
            if (!currentLostItemId) return;

            console.log('Marking item as received:', currentLostItemId);

            fetch(`/lost-items/${currentLostItemId}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: 'resolved' })
            })
            .then(r => {
                console.log('Response status:', r.status);
                if (!r.ok) {
                    return r.text().then(text => {
                        console.log('Response text:', text);
                        try {
                            const err = JSON.parse(text);
                            throw new Error(err.error || `Server error ${r.status}`);
                        } catch (e) {
                            throw new Error(`Server error ${r.status}: ${text}`);
                        }
                    });
                }
                return r.json();
            })
            .then(data => {
                console.log('Item updated:', data);
                alert('Item marked as received! It has been removed from the map.');
                deleteCurrentNotification();
                closeDetail();
                loadNotifications();
                // Redirect back to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1500);
            })
            .catch(e => {
                console.error('Error details:', e);
                alert('Error marking item as received: ' + e.message);
            });
        }

        function deleteCurrentNotification() {
            if (currentNotificationId) {
                fetch('{{ route("notifications.delete", ":id") }}'.replace(':id', currentNotificationId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error('Failed to delete');
                    loadNotifications();
                    closeDetail();
                })
                .catch(e => {
                    console.error('Error:', e);
                    alert('Error deleting notification');
                });
            }
        }

        function markAllAsRead() {
            fetch('{{ route("notifications.mark-all-read") }}', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => loadNotifications())
            .catch(e => console.error('Error:', e));
        }

        window.onclick = function(event) {
            if (event.target.id === 'detailModal') {
                closeDetail();
            }
        }

        // Load notifications when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, fetching notifications...');
            loadNotifications();
        });

        // Also try loading immediately in case DOM is already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadNotifications);
        } else {
            loadNotifications();
        }
    </script>
</body>
</html>
