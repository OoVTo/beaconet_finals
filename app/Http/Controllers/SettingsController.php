<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function getPreferences()
    {
        $preferences = Auth::user()->preferences ?? UserPreference::create(['user_id' => Auth::id()]);
        return response()->json($preferences);
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $preferences = Auth::user()->preferences ?? UserPreference::create(['user_id' => Auth::id()]);
        $preferences->theme = $request->theme;
        $preferences->save();

        return response()->json($preferences);
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'required|boolean',
        ]);

        $preferences = Auth::user()->preferences ?? UserPreference::create(['user_id' => Auth::id()]);
        $preferences->notifications_enabled = $request->notifications_enabled;
        $preferences->save();

        return response()->json($preferences);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated']);
    }
}
