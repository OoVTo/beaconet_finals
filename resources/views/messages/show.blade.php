<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $conversation->owner->name === auth()->user()->name ? $conversation->finder->name : $conversation->owner->name }} - BEACONET-mini</title>
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
        body { font-family: Arial, sans-serif; background: var(--bg-secondary); color: var(--text); transition: all 0.3s; display: flex; flex-direction: column; height: 100vh; }
        .navbar { background: var(--primary); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .navbar h2 { font-size: 24px; margin: 0; white-space: nowrap; display: flex; gap: 10px; align-items: center; }
        .nav-links { display: flex; gap: 15px; align-items: center; justify-content: flex-end; flex-wrap: wrap; }
        .nav-links a, .nav-links button { color: white; text-decoration: none; cursor: pointer; border: none; background: none; font-size: 16px; transition: opacity 0.3s; display: flex; align-items: center; gap: 5px; }
        .nav-links a:hover, .nav-links button:hover { opacity: 0.8; }
        .theme-toggle { background: rgba(255,255,255,0.2); padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); }
        
        .container { display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .conversation-header { background: var(--bg); border-bottom: 1px solid var(--border); padding: 15px 20px; }
        .conversation-title { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; }
        .conversation-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .conversation-title-text h3 { margin: 0; font-size: 18px; }
        .conversation-title-text small { color: var(--text-light); display: block; margin-top: 3px; }
        .messages-container { flex: 1; overflow-y: auto; padding: 20px; background: var(--bg-secondary); }
        .message { margin-bottom: 15px; display: flex; flex-direction: column; align-items: flex-end; gap: 5px; }
        .message.received { align-items: flex-start; }
        .message-bubble { max-width: 70%; padding: 10px 15px; border-radius: 10px; word-wrap: break-word; }
        .message.sent .message-bubble { background: var(--primary); color: white; border-radius: 10px 0px 10px 10px; }
        .message.received .message-bubble { background: var(--bg); border: 1px solid var(--border); border-radius: 0px 10px 10px 10px; color: var(--text); }
        .message-image { max-width: 70%; border-radius: 10px; max-height: 300px; object-fit: cover; }
        .message.sent .message-image { border-radius: 10px 0px 10px 10px; }
        .message.received .message-image { border-radius: 0px 10px 10px 10px; }
        .message-time { font-size: 12px; color: var(--text-light); white-space: nowrap; }
        .empty-messages { text-align: center; padding: 40px 20px; color: var(--text-light); }
        .input-container { background: var(--bg); border-top: 1px solid var(--border); padding: 15px 20px; display: flex; gap: 10px; align-items: flex-end; }
        .input-container textarea { flex: 1; padding: 10px 15px; border: 1px solid var(--border); border-radius: 5px; resize: none; font-family: Arial; background: var(--bg-secondary); color: var(--text); max-height: 100px; }
        .input-container button { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        .input-container button:hover { background: var(--primary-dark); }
        @media (max-width: 768px) {
            .navbar h2 { font-size: 18px; }
            .message-bubble { max-width: 85%; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-comments"></i> Messages</h2>
        <div class="nav-links">
            <a href="{{ route('messages.index') }}" title="Back to Messages"><i class="fas fa-arrow-left"></i></a>
            <a href="{{ route('dashboard') }}" title="Dashboard"><i class="fas fa-home"></i></a>
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
        <div class="conversation-header">
            <div class="conversation-title">
                <div class="conversation-avatar">
                    @if(auth()->id() === $conversation->owner_id)
                        {{ $conversation->finder->name[0] }}
                    @else
                        {{ $conversation->owner->name[0] }}
                    @endif
                </div>
                <div class="conversation-title-text">
                    <h3>
                        @if(auth()->id() === $conversation->owner_id)
                            {{ $conversation->finder->name }}
                        @else
                            {{ $conversation->owner->name }}
                        @endif
                    </h3>
                    <small><i class="fas fa-box"></i> {{ $conversation->lostItem->title }}</small>
                </div>
            </div>
        </div>

        <div class="messages-container" id="messagesContainer">
            @if($messages->isEmpty())
                <div class="empty-messages">
                    <p><i class="fas fa-comments" style="font-size: 32px; opacity: 0.3; display: block; margin-bottom: 10px;"></i></p>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            @else
                @foreach($messages as $message)
                    <div class="message {{ auth()->id() === $message->user_id ? 'sent' : 'received' }}">
                        @if($message->image_path)
                            <img src="/storage/{{ $message->image_path }}" class="message-image" alt="Message image">
                        @endif
                        @if($message->message)
                            <div class="message-bubble">{{ $message->message }}</div>
                        @endif
                        <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="input-container">
            <form id="messageForm" style="flex: 1; display: flex; gap: 10px; align-items: flex-end;">
                @csrf
                <textarea 
                    id="messageInput" 
                    name="message" 
                    placeholder="Type your message..." 
                    rows="1" 
                    style="flex: 1;"
                    onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); submitMessage(); }"
                ></textarea>
                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                <button type="button" onclick="document.getElementById('imageInput').click()" title="Attach image" style="flex-shrink: 0; background: none; color: var(--primary); font-size: 18px; cursor: pointer; border: none;">
                    <i class="fas fa-image"></i>
                </button>
                <button type="button" onclick="submitMessage()" style="flex-shrink: 0;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
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

        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        }

        function submitMessage() {
            const messageInput = document.getElementById('messageInput');
            const imageInput = document.getElementById('imageInput');
            const message = messageInput.value.trim();
            const imageFile = imageInput.files[0];

            if (!message && !imageFile) return;

            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            if (message) formData.append('message', message);
            if (imageFile) {
                console.log('Appending image file:', imageFile.name, imageFile.size);
                formData.append('image', imageFile);
            }

            console.log('Submitting message:', { message, hasImage: !!imageFile });

            fetch(`/messages/{{ $conversation->id }}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            const json = JSON.parse(text);
                            throw new Error(json.error || `Server error (${response.status})`);
                        } catch (e) {
                            if (e instanceof SyntaxError) {
                                throw new Error(`Server error (${response.status}): ${text}`);
                            }
                            throw e;
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    imageInput.value = '';
                    addMessageToDOM(data.message, true);
                    scrollToBottom();
                } else {
                    alert('Error sending message: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending message: ' + error.message);
            });
        }

        function addMessageToDOM(message, isSent) {
            const container = document.getElementById('messagesContainer');
            
            // Remove empty state if it exists
            const emptyState = container.querySelector('.empty-messages');
            if (emptyState) {
                emptyState.remove();
            }

            const messageEl = document.createElement('div');
            messageEl.className = `message ${isSent ? 'sent' : 'received'}`;
            
            let html = '';
            if (message.image_path) {
                html += `<img src="/storage/${message.image_path}" class="message-image" alt="Message image">`;
            }
            if (message.message) {
                html += `<div class="message-bubble">${escapeHtml(message.message)}</div>`;
            }
            html += `<div class="message-time">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</div>`;
            
            messageEl.innerHTML = html;
            container.appendChild(messageEl);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-expand textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });

        // Scroll to bottom on load
        scrollToBottom();
    </script>
</body>
</html>
