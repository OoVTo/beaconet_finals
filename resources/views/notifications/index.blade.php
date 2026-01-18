<!DOCTYPE html>
<html>
<head>
    <title>Notifications - BEACONET-mini</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #667eea; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h2 { font-size: 24px; }
        .navbar a, .navbar button { color: white; text-decoration: none; cursor: pointer; border: none; background: none; font-size: 16px; margin-left: 20px; }
        .navbar a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        .inbox-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .inbox-list { background: white; border-radius: 10px; overflow: hidden; }
        .inbox-item { border-bottom: 1px solid #eee; padding: 15px 20px; cursor: pointer; transition: background 0.2s; display: flex; justify-content: space-between; align-items: center; }
        .inbox-item:hover { background: #f9f9f9; }
        .inbox-item.unread { background: #f0f4ff; border-left: 4px solid #667eea; }
        .inbox-item-content { flex: 1; }
        .inbox-item-sender { font-weight: bold; color: #333; }
        .inbox-item-preview { color: #666; font-size: 14px; margin-top: 5px; }
        .inbox-item-date { color: #999; font-size: 12px; margin-top: 5px; }
        .inbox-item-badge { background: #d32f2f; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        .notification-detail { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000; }
        .notification-detail.active { display: flex; }
        .detail-content { background: white; padding: 40px; border-radius: 10px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .detail-close { background: none; border: none; font-size: 28px; cursor: pointer; color: #999; }
        .detail-from { margin-bottom: 15px; }
        .detail-from label { color: #999; font-size: 12px; }
        .detail-from p { font-weight: bold; color: #333; margin-top: 3px; }
        .detail-image { max-width: 100%; border-radius: 5px; margin: 20px 0; }
        .detail-message { background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; color: #333; line-height: 1.6; }
        .detail-actions { display: flex; gap: 10px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .btn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #5568d3; }
        .btn-success { background: #4CAF50; }
        .btn-success:hover { background: #45a049; }
        .btn-danger { background: #d32f2f; }
        .btn-danger:hover { background: #b71c1c; }
        .empty-message { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Notifications</h2>
        <div>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="inbox-header">
            <h3>Inbox</h3>
            <button class="btn" onclick="markAllAsRead()" style="width: auto;">Mark All as Read</button>
        </div>

        <div class="inbox-list" id="inboxList"></div>
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
                <button class="btn btn-success" id="markReceivedBtn" onclick="markItemReceived()">I Received This Item</button>
                <button class="btn btn-danger" onclick="deleteCurrentNotification()">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let currentNotificationId = null;
        let currentFoundReportId = null;
        let currentLostItemId = null;

        function loadNotifications() {
            fetch('/api/notifications')
                .then(r => r.json())
                .then(notifications => {
                    const container = document.getElementById('inboxList');
                    if (notifications.length === 0) {
                        container.innerHTML = '<div class="empty-message">No notifications</div>';
                        return;
                    }
                    
                    container.innerHTML = notifications.map(n => `
                        <div class="inbox-item ${!n.is_read ? 'unread' : ''}" onclick="openNotification(${n.id}, ${n.found_report_id}, '${n.found_report.lost_item_id}', '${n.is_read}')">
                            <div class="inbox-item-content">
                                <div class="inbox-item-sender">${n.found_report.reporter.name} found your item</div>
                                <div class="inbox-item-preview">${n.found_report.message.substring(0, 60)}...</div>
                                <div class="inbox-item-date">${new Date(n.created_at).toLocaleDateString()}</div>
                            </div>
                            ${!n.is_read ? '<div class="inbox-item-badge">NEW</div>' : ''}
                        </div>
                    `).join('');
                })
                .catch(e => console.error('Error loading notifications:', e));
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

            fetch(`/lost-items/${currentLostItemId}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: 'received' })
            })
            .then(r => {
                if (!r.ok) throw new Error('Failed to update item');
                return r.json();
            })
            .then(data => {
                alert('Item marked as received! It has been removed from the map.');
                deleteCurrentNotification();
                closeDetail();
                loadNotifications();
            })
            .catch(e => {
                console.error('Error:', e);
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

        document.addEventListener('DOMContentLoaded', loadNotifications);
    </script>
</body>
</html>
