<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - BEACONET-mini</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #d32f2f; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar h2 { font-size: 24px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; }
        .stat-card h3 { font-size: 12px; opacity: 0.8; margin-bottom: 10px; }
        .stat-card .number { font-size: 32px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .btn { padding: 8px 16px; background: #d32f2f; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #b71c1c; }
        a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>Admin Panel</h2>
        <div>
            <a href="{{ route('dashboard') }}" style="color: white; margin-right: 20px;">Back to Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer;">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px;">Dashboard Overview</h2>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number">{{ $stats['users_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Lost Items</h3>
                <div class="number">{{ $stats['lost_items_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Found Reports</h3>
                <div class="number">{{ $stats['found_reports_count'] }}</div>
            </div>
            <div class="stat-card">
                <h3>Items Found</h3>
                <div class="number">{{ $stats['found_count'] }}</div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h3 style="margin-bottom: 15px;">
                <a href="{{ route('admin.users') }}">Manage Users</a>
            </h3>
            <h3 style="margin-bottom: 15px;">
                <a href="{{ route('admin.lost-items') }}">Manage Lost Items</a>
            </h3>
            <h3 style="margin-bottom: 15px;">
                <a href="{{ route('admin.found-reports') }}">Manage Found Reports</a>
            </h3>
        </div>

        <h3 style="margin-top: 40px; margin-bottom: 15px;">Recent Lost Items</h3>
        @if($recentItems->count() > 0)
            <table>
                <tr>
                    <th>Item</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Posted</th>
                </tr>
                @foreach($recentItems as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>No items yet</p>
        @endif
    </div>
</body>
</html>
