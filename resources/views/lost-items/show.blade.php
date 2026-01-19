<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item->title }} - BEACONET-mini</title>
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
            --text-light: #666;
            --border: #ddd;
        }
        body.dark-mode {
            --primary: #10B981;
            --primary-dark: #059669;
            --primary-light: #6EE7B7;
            --bg: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text: #ffffff;
            --text-light: #bbb;
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
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: var(--bg); border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .item-image { width: 100%; max-height: 400px; background: var(--bg-secondary); border-radius: 10px; margin-bottom: 30px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .item-image img { width: 100%; height: 100%; object-fit: cover; }
        .item-image-placeholder { font-size: 96px; color: var(--text-light); opacity: 0.3; }
        .item-header { margin-bottom: 30px; }
        .item-title { font-size: 36px; font-weight: bold; color: var(--text); margin-bottom: 15px; }
        .item-meta { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
        .meta-item { display: flex; align-items: center; gap: 8px; color: var(--text-light); font-size: 14px; }
        .meta-item i { color: var(--primary); font-size: 16px; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 15px; }
        .status-lost { background: #fef3c7; color: #92400e; }
        .status-found { background: #d1fae5; color: #065f46; }
        .status-resolved { background: #dbeafe; color: #1e40af; }
        .item-description { background: var(--bg-secondary); padding: 25px; border-radius: 10px; margin-bottom: 30px; line-height: 1.8; font-size: 16px; color: var(--text); }
        .item-section { margin-bottom: 25px; }
        .item-section h3 { font-size: 18px; color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .item-section p { color: var(--text-light); font-size: 14px; line-height: 1.6; }
        .posted-by { background: var(--bg-secondary); padding: 15px; border-radius: 8px; display: flex; align-items: center; gap: 15px; }
        .posted-by-avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; }
        .posted-by-info { flex: 1; }
        .posted-by-name { font-weight: bold; color: var(--text); }
        .posted-by-date { font-size: 12px; color: var(--text-light); margin-top: 3px; }
        .actions { display: flex; gap: 10px; margin-top: 30px; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: all 0.3s; display: flex; align-items: center; gap: 8px; justify-content: center; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-secondary { background: var(--bg-secondary); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); }
        .found-reports { margin-top: 30px; padding-top: 30px; border-top: 2px solid var(--border); }
        .report-card { background: var(--bg-secondary); padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .report-card h4 { color: var(--primary); margin-bottom: 10px; }
        .report-card p { font-size: 14px; color: var(--text-light); line-height: 1.6; }
        .report-image { max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 10px; }
        @media (max-width: 768px) {
            .container { margin: 15px auto; padding: 15px; }
            .item-title { font-size: 24px; }
            .item-meta { flex-direction: column; gap: 10px; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><i class="fas fa-info-circle"></i> Item Details</h2>
        <div class="nav-links">
            <a href="{{ route('lost-items.history') }}" title="Back to History">
                <i class="fas fa-arrow-left"></i> Back
            </a>
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
        <div class="item-image">
            @if($item->image_path)
                <img src="/storage/{{ $item->image_path }}" alt="{{ $item->title }}">
            @else
                <div class="item-image-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            @endif
        </div>

        <div class="item-header">
            <div class="status-badge status-{{ $item->status }}">
                <i class="fas fa-tag"></i> {{ strtoupper($item->status) }}
            </div>
            <h1 class="item-title">{{ $item->title }}</h1>
            
            <div class="item-meta">
                <div class="meta-item">
                    <i class="fas fa-map-pin"></i>
                    <span>{{ $item->location_name ?? 'Unknown location' }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>{{ $item->created_at->format('F d, Y') }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <span>{{ $item->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        @if($item->description)
            <div class="item-section">
                <h3><i class="fas fa-align-left"></i> Description</h3>
                <div class="item-description">
                    {{ $item->description }}
                </div>
            </div>
        @endif

        <div class="item-section">
            <h3><i class="fas fa-user"></i> Posted By</h3>
            <div class="posted-by">
                <div class="posted-by-avatar">
                    {{ strtoupper(substr($item->user->name, 0, 1)) }}
                </div>
                <div class="posted-by-info">
                    <div class="posted-by-name">{{ $item->user->name }}</div>
                    <div class="posted-by-date">{{ $item->user->email }}</div>
                </div>
            </div>
        </div>

        @if($item->foundReports && $item->foundReports->count() > 0)
            <div class="found-reports">
                <h3><i class="fas fa-check-circle"></i> Found Reports ({{ $item->foundReports->count() }})</h3>
                @foreach($item->foundReports as $report)
                    <div class="report-card">
                        <h4><i class="fas fa-user"></i> {{ $report->reporter->name }}</h4>
                        <p>{{ $report->message }}</p>
                        @if($report->image_path)
                            <img src="/storage/{{ $report->image_path }}" alt="Found item photo" class="report-image">
                        @endif
                        <div style="margin-top: 10px; font-size: 12px; color: var(--text-light);">
                            <i class="fas fa-calendar"></i> {{ $report->created_at->format('F d, Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="actions">
            <a href="{{ route('lost-items.history') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to History
            </a>
            @if(auth()->id() === $item->user_id)
                <button class="btn btn-primary" onclick="deleteItem()">
                    <i class="fas fa-trash"></i> Delete Item
                </button>
            @endif
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

        function deleteItem() {
            if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                fetch('/lost-items/{{ $item->id }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(r => {
                    if (!r.ok) throw new Error('Failed to delete item');
                    alert('Item deleted successfully!');
                    window.location.href = '{{ route("lost-items.history") }}';
                })
                .catch(e => {
                    console.error('Error:', e);
                    alert('Error deleting item: ' + e.message);
                });
            }
        }
    </script>
</body>
</html>
