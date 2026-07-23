<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        // Get the latest message for each conversation
        // This query finds the maximum message ID between the current user and any other user
        $latestMessageIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy(DB::raw('CASE WHEN sender_id = ' . $userId . ' THEN receiver_id ELSE sender_id END'))
            ->pluck('id');

        $conversations = Message::whereIn('id', $latestMessageIds)
            ->with(['sender', 'receiver', 'property'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    public function show(User $user): View
    {
        $currentUserId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($query) use ($currentUserId, $user) {
                $query->where('sender_id', $currentUserId)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUserId, $user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $currentUserId);
            })
            ->with(['sender', 'receiver', 'property'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.show', compact('messages', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:5000',
            'property_id' => 'nullable|exists:properties,id',
        ]);

        $validated['sender_id'] = Auth::id();
        Message::create($validated);

        return redirect()->route('messages.show', $validated['receiver_id'])
            ->with('success', 'Message sent!');
    }
}
