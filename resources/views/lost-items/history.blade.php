<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost Items History - BEACONET-mini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #10B981;
            --primary-dark: #059669;
            --primary-light: #6EE7B7;
            --bg: #ffffff;
            --bg-secondary: #f5f5f5;
            --text: #333;
            --text-light: #999;
            --border: #ddd;
        }
        body.dark-mode {
            --primary: #10B981;
            --primary-dark: #059669;
            --primary-light: #6EE7B7;
            --bg: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text: #ffffff;
            --text-light: #aaa;
            --border: #444;
        }
        body { font-family: Arial, sans-serif; background: var(--bg-secondary); color: var(--text); transition: all 0.3s; }
        .navbar { background: var(--primary); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar h2 { font-size: 24px; margin: 0; white-space: nowrap; display: flex; gap: 10px; align-items: center; }
        .nav-links { display: flex; gap: 15px; align-items: center; justify-content: flex-end; flex-wrap: wrap; }
        .nav-links a, .nav-links button { color: white; text-decoration: none; cursor: pointer; border: none; background: none; font-size: 16px; transition: opacity 0.3s; display: flex; align-items: center; gap: 5px; }
        .nav-links a:hover, .nav-links button:hover { opacity: 0.8; }
        .theme-toggle { background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { color: var(--primary); }
        .items-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .item-card { background: var(--bg); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.3s; }
        .item-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); transform: translateY(-2px); }
        .item-image { width: 100%; height: 200px; background: var(--bg-secondary); overflow: hidden; display: flex; align-items: center; justify-content: center; color: var(--text-light); }
        .item-image img { width: 100%; height: 100%; object-fit: cover; }
        .item-content { padding: 20px; }
        .item-title { font-size: 18px; font-weight: bold; color: var(--text); margin-bottom: 10px; }
        .item-description { font-size: 14px; color: var(--text-light); margin-bottom: 10px; line-height: 1.5; }
        .item-meta { font-size: 12px; color: var(--text-light); margin-bottom: 15px; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 10px; }
        .status-lost { background: #fef3c7; color: #92400e; }
        .status-found { background: #d1fae5; color: #065f46; }
        .status-resolved { background: #dbeafe; color: #1e40af; }
        .item-actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn { padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: all 0.3s; display: flex; align-items: center; gap: 6px; flex: 1; justify-content: center; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #ef4444; color: white; }
        .btn-danger:hover { background: #dc2626; }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-light); }
        .empty-state-icon { font-size: 64px; margin-bottom: 20px; opacity: 0.5; }
        .empty-state p { font-size: 16px; margin-bottom: 20px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg); padding: 30px; border-radius: 10px; max-width: 400px; width: 90%; }
        .modal-header { margin-bottom: 20px; }
        .modal-header h2 { color: var(--text); margin-bottom: 10px; }
        .modal-body { margin-bottom: 20px; color: var(--text-light); }
        .modal-actions { display: flex; gap: 10px; }
        .modal-actions .btn { flex: 1; }
        .close-btn { background: none; border: none; font-size: 28px; cursor: pointer; color: var(--text-light); float: right; }
        @media (max-width: 768px) {
            .items-grid { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-history"></i> Lost Items History</h2>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" title="Dashboard"><i class="fas fa-home"></i> Dashboard</a>
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
        <div class="page-header">
            <h1><i class="fas fa-list"></i> Your Lost Items</h1>
            <span id="itemCount" style="color: var(--text-light); font-size: 14px;"></span>
        </div>

        <div id="itemsContainer" class="items-grid">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                <p>Loading your items...</p>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">&times;</button>
            <div class="modal-header">
                <h2>Delete Item</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item? This action cannot be undone.</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>

    <script>
        let itemToDelete = null;

        // Initialize dark mode from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const theme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        }

        function loadLostItems() {
            fetch('{{ route("lost-items.myItems") }}')
                .then(r => r.json())
                .then(items => {
                    const container = document.getElementById('itemsContainer');
                    const count = document.getElementById('itemCount');
                    
                    if (items.length === 0) {
                        container.innerHTML = `
                            <div class="empty-state" style="grid-column: 1 / -1;">
                                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                                <p>You haven't posted any lost items yet.</p>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width: auto; margin-top: 20px;">
                                    <i class="fas fa-plus"></i> Post an Item
                                </a>
                            </div>
                        `;
                        count.textContent = '';
                        return;
                    }

                    count.textContent = `${items.length} item${items.length !== 1 ? 's' : ''}`;
                    
                    container.innerHTML = items.map(item => `
                        <div class="item-card">
                            <div class="item-image">
                                ${item.image_path 
                                    ? `<img src="/storage/${item.image_path}" alt="${item.title}">` 
                                    : '<i class="fas fa-image" style="font-size: 48px; opacity: 0.3;"></i>'
                                }
                            </div>
                            <div class="item-content">
                                <div class="status-badge status-${item.status}">
                                    <i class="fas fa-tag"></i> ${item.status.toUpperCase()}
                                </div>
                                <div class="item-title">${item.title}</div>
                                <div class="item-description">${item.description || 'No description'}</div>
                                <div class="item-meta">
                                    <i class="fas fa-map-pin"></i> ${item.location_name || 'Unknown location'}<br>
                                    <i class="fas fa-calendar"></i> ${new Date(item.created_at).toLocaleDateString()}
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-primary" onclick="viewItem(${item.id})">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteItem(${item.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                })
                .catch(e => {
                    console.error('Error loading items:', e);
                    document.getElementById('itemsContainer').innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <div class="empty-state-icon"><i class="fas fa-exclamation-triangle"></i></div>
                            <p>Error loading your items. Please try again.</p>
                        </div>
                    `;
                });
        }

        function viewItem(id) {
            window.location.href = `{{ route('lost-items.show', '') }}/${id}`;
        }

        function deleteItem(id) {
            itemToDelete = id;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('deleteModal').classList.remove('active');
            itemToDelete = null;
        }

        function confirmDelete() {
            if (!itemToDelete) return;

            fetch(`/lost-items/${itemToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('Failed to delete item');
                closeModal();
                loadLostItems();
                alert('Item deleted successfully!');
            })
            .catch(e => {
                console.error('Error:', e);
                alert('Error deleting item: ' + e.message);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Load items when page loads
        document.addEventListener('DOMContentLoaded', loadLostItems);
    </script>
</body>
</html>
