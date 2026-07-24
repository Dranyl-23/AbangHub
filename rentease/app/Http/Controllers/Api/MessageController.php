<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Get all active conversations for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Fetch the latest message for each conversation
        // A conversation is between $userId and another user
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver', 'property'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($userId) {
                // Group by the ID of the OTHER person
                $otherUserId = $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
                // We could also group by property_id if we want separate chats per property
                return $otherUserId . '-' . ($message->property_id ?? '0');
            })
            ->map(function ($group) {
                return $group->first(); // Get only the most recent message per conversation
            })
            ->values(); // Reset array keys

        return response()->json(['data' => $conversations]);
    }

    /**
     * Get the chat history with a specific user regarding a specific property.
     */
    public function show(Request $request, $userId, $propertyId)
    {
        $authId = $request->user()->id;

        // If propertyId is 0, it means general chat (no specific property)
        $propertyId = $propertyId == '0' ? null : $propertyId;

        $messages = Message::where(function ($query) use ($authId, $userId) {
                $query->where('sender_id', $authId)
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($authId, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $authId);
            })
            ->when($propertyId, function ($query, $propertyId) {
                return $query->where('property_id', $propertyId);
            })
            ->with(['sender', 'receiver', 'property'])
            ->orderBy('created_at', 'asc') // Oldest first for chat UI
            ->get();

        // Mark messages as read if receiver is the authenticated user
        Message::where('receiver_id', $authId)
            ->where('sender_id', $userId)
            ->when($propertyId, function ($query, $propertyId) {
                return $query->where('property_id', $propertyId);
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get the other user details for the header
        $otherUser = User::find($userId);

        return response()->json([
            'other_user' => $otherUser,
            'messages' => $messages
        ]);
    }

    /**
     * Send a new message.
     */
    public function store(Request $request)
    {
        \Log::info('Message Payload: ', $request->all());
        
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'property_id' => $request->property_id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        // Load relationships to return a full message object
        $message->load(['sender', 'receiver', 'property']);

        return response()->json([
            'message' => 'Message sent',
            'data' => $message
        ], 201);
    }

    /**
     * Get the total unread message count for the authenticated user.
     */
    public function unreadCount(Request $request)
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
