<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - BEACONET-mini</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #10B981;
            --primary-dark: #059669;
            --bg: #ffffff;
            --bg-secondary: #f5f5f5;
            --text: #333;
            --text-light: #999;
            --border: #ddd;
        }
        body.dark-mode {
            --primary: #10B981;
            --primary-dark: #059669;
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
        .container { max-width: 900px; margin: 20px auto; padding: 20px; }
        .conversations-list { background: var(--bg); border-radius: 10px; overflow: hidden; }
        .conversation-item { border-bottom: 1px solid var(--border); padding: 15px 20px; cursor: pointer; transition: background 0.2s; display: flex; gap: 15px; align-items: center; }
        .conversation-item:hover { background: var(--bg-secondary); }
        .conversation-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0; }
        .conversation-content { flex: 1; min-width: 0; }
        .conversation-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
        .conversation-name { font-weight: bold; color: var(--text); }
        .conversation-time { font-size: 12px; color: var(--text-light); }
        .conversation-preview { font-size: 14px; color: var(--text-light); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .conversation-item-info { font-size: 12px; color: var(--text-light); margin-top: 3px; }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-light); }
        .empty-state-icon { font-size: 64px; margin-bottom: 20px; opacity: 0.5; }
        .empty-state p { font-size: 16px; margin-bottom: 20px; }
        @media (max-width: 768px) {
            .container { margin: 10px auto; padding: 10px; }
            .conversation-item { padding: 12px 15px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-comments"></i> Messages</h2>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}" title="Dashboard"><i class="fas fa-home"></i></a>
            <a href="{{ route('notifications.index') }}" title="Notifications"><i class="fas fa-bell"></i></a>
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
        <div class="conversations-list" id="conversationsList">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                <p>No conversations yet. When someone reports finding your lost item, you can message them here.</p>
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

        function loadConversations() {
            const conversations = @json($conversations);
            const container = document.getElementById('conversationsList');

            if (conversations.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                        <p>No conversations yet. When someone reports finding your lost item, you can message them here.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = conversations.map(conv => `
                <div class="conversation-item" onclick="openConversation(${conv.id})">
                    <div class="conversation-avatar">
                        ${conv.owner_id === {{ auth()->id() }} ? conv.finder.name.charAt(0).toUpperCase() : conv.owner.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-header">
                            <div class="conversation-name">
                                ${conv.owner_id === {{ auth()->id() }} ? conv.finder.name : conv.owner.name}
                            </div>
                            <div class="conversation-time">
                                ${new Date(conv.updated_at).toLocaleDateString()}
                            </div>
                        </div>
                        <div class="conversation-preview">
                            ${conv.latest_message?.message || 'No messages yet'}
                        </div>
                        <div class="conversation-item-info">
                            <i class="fas fa-box"></i> ${conv.lost_item?.title || 'Item'}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function openConversation(id) {
            window.location.href = `/messages/${id}`;
        }

        loadConversations();
    </script>
</body>
</html>
