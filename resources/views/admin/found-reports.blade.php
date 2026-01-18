<!DOCTYPE html>
<html>
<head>
    <title>Found Reports - Admin</title>
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
    <div class="navbar"><h2><a href="{{ route('admin.dashboard') }}" style="color: white;">Admin Panel</a> > Found Reports</h2></div>
    <div class="container">
        <h2>Manage Found Reports</h2>
        @if($reports->count() > 0)
            <table>
                <tr><th>Item</th><th>Reported By</th><th>Status</th><th>Date</th><th>Action</th></tr>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $report->lostItem->title }}</td>
                        <td>{{ $report->reporter->name }}</td>
                        <td>{{ $report->status }}</td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.found-reports.delete', $report->id) }}" style="display: inline;" onsubmit="return confirm('Delete?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </table>
        @else
            <p>No reports</p>
        @endif
    </div>
</body>
</html>
