<!DOCTYPE html>
<html>
<head>
    <title>Users - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .navbar { background: #d32f2f; color: white; padding: 15px 20px; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }
        .btn { padding: 8px 16px; background: #d32f2f; color: white; border: none; border-radius: 5px; cursor: pointer; }
        a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="navbar">
        <h2><a href="{{ route('admin.dashboard') }}" style="color: white;">Admin Panel</a> > Users</h2>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px;">Manage Users</h2>
        
        @if($users->count() > 0)
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Action</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            @if(!$user->isAdmin())
                                <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn">Delete</button>
                                </form>
                            @else
                                <span style="color: #999;">Admin</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            <div style="margin-top: 20px;">
                {{ $users->links() }}
            </div>
        @else
            <p>No users found</p>
        @endif
    </div>
</body>
</html>
