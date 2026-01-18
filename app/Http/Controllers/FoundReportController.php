<?php

namespace App\Http\Controllers;

use App\Models\FoundReport;
use App\Models\LostItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FoundReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lost_item_id' => 'required|exists:lost_items,id',
            'message' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('found-reports', 'public');
        }

        $lostItem = LostItem::find($request->lost_item_id);
        
        $foundReport = FoundReport::create([
            'lost_item_id' => $request->lost_item_id,
            'reporter_id' => Auth::id(),
            'message' => $request->message,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        // Create notification for the item owner
        Notification::create([
            'user_id' => $lostItem->user_id,
            'found_report_id' => $foundReport->id,
            'type' => 'found_report',
            'title' => 'Found Item Report',
            'message' => Auth::user()->name . ' reported finding your lost item!',
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        return response()->json($foundReport, 201);
    }

    public function accept($id)
    {
        $foundReport = FoundReport::find($id);
        if (!$foundReport) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $lostItem = $foundReport->lostItem;
        if ($lostItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $foundReport->status = 'accepted';
        $foundReport->save();

        $lostItem->status = 'found';
        $lostItem->save();

        // Update notification
        $notification = $foundReport->notification;
        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return response()->json($foundReport);
    }

    public function reject($id)
    {
        $foundReport = FoundReport::find($id);
        if (!$foundReport) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $lostItem = $foundReport->lostItem;
        if ($lostItem->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $foundReport->status = 'rejected';
        $foundReport->save();

        return response()->json($foundReport);
    }
}
