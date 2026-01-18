<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                return redirect('/login');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'users_count' => User::where('role', 'user')->count(),
            'lost_items_count' => LostItem::count(),
            'found_reports_count' => FoundReport::count(),
            'found_count' => LostItem::where('status', 'found')->count(),
        ];

        $recentItems = LostItem::with('user')->latest()->take(10)->get();
        $recentReports = FoundReport::with(['lostItem', 'reporter'])->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentItems', 'recentReports'));
    }

    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users', compact('users'));
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user || $user->isAdmin()) {
            return back()->with('error', 'Cannot delete admin user');
        }

        // Delete related data
        $user->lostItems()->delete();
        $user->foundReports()->delete();
        $user->notifications()->delete();
        $user->preferences()->delete();
        $user->delete();

        return back()->with('success', 'User deleted successfully');
    }

    public function lostItems()
    {
        $items = LostItem::with('user')->paginate(15);
        return view('admin.lost-items', compact('items'));
    }

    public function deleteLostItem($id)
    {
        $item = LostItem::find($id);
        if ($item) {
            $item->foundReports()->delete();
            $item->delete();
        }

        return back()->with('success', 'Lost item deleted');
    }

    public function foundReports()
    {
        $reports = FoundReport::with(['lostItem', 'reporter'])->paginate(15);
        return view('admin.found-reports', compact('reports'));
    }

    public function deleteFoundReport($id)
    {
        $report = FoundReport::find($id);
        if ($report) {
            $report->notification()->delete();
            $report->delete();
        }

        return back()->with('success', 'Report deleted');
    }
}
