<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\FoundReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        $user = auth()->user();
        
        // Redirect to admin if admin, else to dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }

    return back()->with('error', 'Invalid credentials');
})->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

// User Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    // Lost Items
    Route::get('/lost-items', [LostItemController::class, 'index'])->name('lost-items.index');
    Route::get('/my-items', [LostItemController::class, 'myItems'])->name('lost-items.myItems');
    Route::get('/lost-items/history', function () {
        return view('lost-items.history');
    })->name('lost-items.history');
    Route::post('/lost-items', [LostItemController::class, 'store'])->name('lost-items.store');
    Route::get('/lost-items/{id}', [LostItemController::class, 'show'])->name('lost-items.show');
    Route::patch('/lost-items/{id}', [LostItemController::class, 'update'])->name('lost-items.update');
    Route::delete('/lost-items/{id}', [LostItemController::class, 'destroy'])->name('lost-items.destroy');

    // Found Reports
    Route::post('/found-reports', [FoundReportController::class, 'store'])->name('found-reports.store');
    Route::patch('/found-reports/{id}/accept', [FoundReportController::class, 'accept'])->name('found-reports.accept');
    Route::patch('/found-reports/{id}/reject', [FoundReportController::class, 'reject'])->name('found-reports.reject');

    // Notifications
    Route::get('/notifications', function () {
        return view('notifications.index');
    })->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('notifications.api.index');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/mark-all/read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    // Settings
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
    
    Route::get('/settings/preferences', [SettingsController::class, 'getPreferences'])->name('settings.preferences');
    Route::patch('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversationId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversationId}', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/found-reports/{foundReportId}/message', [MessageController::class, 'startFromFoundReport'])->name('messages.startFromFoundReport');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/lost-items', [AdminController::class, 'lostItems'])->name('admin.lost-items');
    Route::delete('/admin/lost-items/{id}', [AdminController::class, 'deleteLostItem'])->name('admin.lost-items.delete');
    Route::get('/admin/found-reports', [AdminController::class, 'foundReports'])->name('admin.found-reports');
    Route::delete('/admin/found-reports/{id}', [AdminController::class, 'deleteFoundReport'])->name('admin.found-reports.delete');
});

