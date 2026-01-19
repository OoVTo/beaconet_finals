<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\FoundReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('owner_id', Auth::id())
            ->orWhere('finder_id', Auth::id())
            ->with(['owner', 'finder', 'lostItem', 'latestMessage'])
            ->orderByDesc('updated_at')
            ->get();

        return view('messages.index', ['conversations' => $conversations]);
    }

    public function show($conversationId)
    {
        $conversation = Conversation::with(['owner', 'finder', 'lostItem', 'messages.user'])
            ->find($conversationId);

        if (!$conversation) {
            abort(404, 'Conversation not found');
        }

        // Check if user is part of this conversation
        if (Auth::id() !== $conversation->owner_id && Auth::id() !== $conversation->finder_id) {
            abort(403, 'Unauthorized');
        }

        // Mark messages as read for current user
        Message::where('conversation_id', $conversationId)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages;

        return view('messages.show', [
            'conversation' => $conversation,
            'messages' => $messages
        ]);
    }

    public function store(Request $request, $conversationId)
    {
        try {
            $conversation = Conversation::with(['owner', 'finder'])->find($conversationId);

            if (!$conversation) {
                return response()->json(['error' => 'Conversation not found'], 404);
            }

            // Check if user is part of this conversation
            if (Auth::id() !== $conversation->owner_id && Auth::id() !== $conversation->finder_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $request->validate([
                'message' => 'nullable|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            // At least message or image must be provided
            if (!$request->filled('message') && !$request->hasFile('image')) {
                return response()->json(['error' => 'Message or image is required'], 422);
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('messages', 'public');
            }

            $message = Message::create([
                'conversation_id' => $conversationId,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'image_path' => $imagePath,
            ]);

            $conversation->touch(); // Update conversation timestamp

            // Determine the recipient
            $recipientId = Auth::id() === $conversation->owner_id ? $conversation->finder_id : $conversation->owner_id;

            // Create notification for the recipient
            Notification::create([
                'user_id' => $recipientId,
                'conversation_id' => $conversationId,
                'type' => 'new_message',
                'title' => 'New Message',
                'message' => Auth::user()->name . ' sent you a message',
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'message' => $message->message,
                    'image_path' => $message->image_path,
                    'created_at' => $message->created_at,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error sending message: ' . $e->getMessage()], 500);
        }
    }

    public function startFromFoundReport($foundReportId)
    {
        $foundReport = FoundReport::with('lostItem')->find($foundReportId);

        if (!$foundReport) {
            return redirect()->route('notifications.index')->with('error', 'Found report not found');
        }

        // Check if user is the item owner
        if (Auth::id() !== $foundReport->lostItem->user_id) {
            return redirect()->route('notifications.index')->with('error', 'Unauthorized');
        }

        // Create or get existing conversation
        $conversation = Conversation::firstOrCreate(
            [
                'lost_item_id' => $foundReport->lost_item_id,
                'owner_id' => $foundReport->lostItem->user_id,
                'finder_id' => $foundReport->reporter_id,
            ]
        );

        return redirect()->route('messages.show', $conversation->id);
    }
}
