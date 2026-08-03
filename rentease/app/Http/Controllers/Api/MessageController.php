<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
                // Group by the ID of the OTHER person + property
                $otherUserId = $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
                return $otherUserId . '-' . ($message->property_id ?? '0');
            })
            ->map(function ($group) {
                return $group->first(); // Most recent message per conversation
            })
            ->values(); // Reset array keys

        return response()->json(['data' => $conversations]);
    }

    /**
     * Get the chat history with a specific user regarding a specific property.
     *
     * CRIT-7 FIX:
     * 1. Verify the requested $userId is a valid conversation partner
     *    (i.e., has actually exchanged messages with the authenticated user)
     *    before returning any user details. Prevents arbitrary user enumeration.
     * 2. Return the other user wrapped in UserResource instead of raw model
     *    to prevent internal fields from leaking.
     */
    public function show(Request $request, $userId, $propertyId)
    {
        $authId = $request->user()->id;

        // CRIT-7 FIX: Validate that the target user actually exists
        $otherUser = User::find($userId);
        if (!$otherUser) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // If propertyId is 0, it means general chat (no specific property)
        $propertyId = $propertyId == '0' ? null : $propertyId;

        // CRIT-7 FIX: Ensure a real conversation exists between these two users
        // (on this property if specified) before returning any data.
        // This prevents any authenticated user from peeking at another user's profile
        // by guessing user IDs through this endpoint.
        $conversationExists = Message::where(function ($q) use ($authId, $userId) {
                $q->where(function ($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $authId)->where('receiver_id', $userId);
                })->orWhere(function ($inner) use ($authId, $userId) {
                    $inner->where('sender_id', $userId)->where('receiver_id', $authId);
                });
            })
            ->when($propertyId, fn($q, $pid) => $q->where('property_id', $pid))
            ->exists();

        if (!$conversationExists) {
            return response()->json([
                'message' => 'No conversation found with this user.'
            ], 404);
        }

        // Fetch the actual messages now that we've confirmed membership
        $messages = Message::where(function ($query) use ($authId, $userId) {
                $query->where(function ($q) use ($authId, $userId) {
                    $q->where('sender_id', $authId)->where('receiver_id', $userId);
                })->orWhere(function ($q) use ($authId, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $authId);
                });
            })
            ->when($propertyId, fn($query, $pid) => $query->where('property_id', $pid))
            ->with(['sender', 'receiver', 'property'])
            ->orderBy('created_at', 'asc') // Oldest first for chat UI
            ->get();

        // Mark messages as read where the authenticated user is the receiver
        Message::where('receiver_id', $authId)
            ->where('sender_id', $userId)
            ->when($propertyId, fn($query, $pid) => $query->where('property_id', $pid))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // CRIT-7 FIX: Wrap the other user in UserResource to prevent leaking
        // sensitive fields (phone, id_picture, emergency contacts, etc.)
        return response()->json([
            'other_user' => new UserResource($otherUser),
            'messages'   => $messages,
        ]);
    }

    /**
     * Send a new message.
     */
    public function store(Request $request)
    {
        // LOW-1 FIX: Removed debug log (\Log::info) that was logging all message
        // content to production log files on every send.

        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'content'     => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'property_id' => $request->property_id,
            'content'     => $request->content,
            'is_read'     => false,
        ]);

        // Load relationships to return a full message object
        $message->load(['sender', 'receiver', 'property']);

        return response()->json([
            'message' => 'Message sent',
            'data'    => $message,
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
