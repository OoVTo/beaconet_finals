<!DOCTYPE html>
<html>
<head>
    <title>Lost Items - Admin</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #d32f2f; color: white; padding: 15px 20px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { padding: 8px 16px; background: #d32f2f; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="navbar"><h2><a href="{{ route('admin.dashboard') }}" style="color: white;">Admin Panel</a> > Lost Items</h2></div>
    <div class="container">
        <h2>Manage Lost Items</h2>
        @if($items->count() > 0)
            <table>
                <tr><th>Title</th><th>User</th><th>Status</th><th>Posted</th><th>Action</th></tr>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->user->name }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.lost-items.delete', $item->id) }}" style="display: inline;" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>No items</p>
        @endif
    </div>
</body>
</html>
