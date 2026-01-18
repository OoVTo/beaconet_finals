<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use App\Models\FoundReport;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LostItemController extends Controller
{
    public function index()
    {
        $lostItems = LostItem::with('user')->where('status', '!=', 'resolved')->get();
        return response()->json($lostItems);
    }

    public function myItems()
    {
        $lostItems = LostItem::where('user_id', Auth::id())->get();
        return response()->json($lostItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'location_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('lost-items', 'public');
        }

        $lostItem = LostItem::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_name' => $request->location_name,
            'image_path' => $imagePath,
            'status' => 'lost',
        ]);

        return response()->json($lostItem, 201);
    }

    public function show($id)
    {
        $lostItem = LostItem::with(['user', 'foundReports'])->find($id);
        if (!$lostItem) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json($lostItem);
    }

    public function destroy($id)
    {
        $lostItem = LostItem::find($id);
        if (!$lostItem || $lostItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if ($lostItem->image_path) {
            Storage::disk('public')->delete($lostItem->image_path);
        }
        
        $lostItem->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
