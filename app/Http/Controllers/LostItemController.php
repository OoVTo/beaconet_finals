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
        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error submitting item: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $item = LostItem::with(['user', 'foundReports.reporter'])->find($id);
        if (!$item) {
            abort(404, 'Lost item not found');
        }
        return view('lost-items.show', ['item' => $item]);
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

    public function update($id, Request $request)
    {
        try {
            $lostItem = LostItem::with('user')->find($id);
            
            \Log::info('Update Lost Item Request', [
                'id' => $id,
                'auth_id' => Auth::id(),
                'item_exists' => !!$lostItem,
                'item_user_id' => $lostItem?->user_id,
                'status' => $request->status ?? null,
                'all_request_data' => $request->all()
            ]);
            
            if (!$lostItem) {
                return response()->json(['error' => 'Item not found'], 404);
            }
            
            // Allow the item owner or anyone marking it as received to update the status
            if ($request->has('status') && $request->status === 'resolved') {
                // Anyone can mark an item as received/resolved
                $lostItem->status = $request->status;
                $saved = $lostItem->save();
                \Log::info('Item status updated to resolved', [
                    'id' => $id, 
                    'updated_by' => Auth::id(),
                    'save_result' => $saved
                ]);
                return response()->json($lostItem->toArray(), 200);
            } elseif ($lostItem->user_id === Auth::id() && $request->has('status')) {
                // Only owner can update other statuses
                $lostItem->status = $request->status;
                $saved = $lostItem->save();
                \Log::info('Item status updated', [
                    'id' => $id, 
                    'new_status' => $request->status,
                    'save_result' => $saved
                ]);
                return response()->json($lostItem->toArray(), 200);
            } else {
                \Log::warning('Unauthorized update attempt', [
                    'item_id' => $id,
                    'item_user_id' => $lostItem->user_id,
                    'auth_id' => Auth::id(),
                    'status_value' => $request->status,
                    'has_status' => $request->has('status')
                ]);
                return response()->json(['error' => 'You do not own this item'], 403);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating lost item', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to update item', 'message' => $e->getMessage()], 500);
        }
    }
}
