<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatBox extends Component
{
    /** @var \Illuminate\Database\Eloquent\Collection|array */
    public $conversations = [];
    
    /** @var \App\Models\User|null */
    public $activeUser = null;
    
    /** @var \Illuminate\Database\Eloquent\Collection|array */
    public $messages = [];
    public $newMessage = '';
    
    public function mount(?int $selectedUserId = null)
    {
        $this->loadConversations();
        
        if ($selectedUserId) {
            $this->selectConversation($selectedUserId);
            // If the user isn't in conversations list yet, add them
            if (!collect($this->conversations)->contains('id', $selectedUserId)) {
                $user = User::find($selectedUserId);
                if ($user) {
                    $this->conversations = collect($this->conversations)->prepend($user);
                }
            }
        } elseif (count($this->conversations) > 0) {
            $this->selectConversation($this->conversations[0]->id);
        }
    }

    public function loadConversations()
    {
        $userId = Auth::id();
        
        // Find all users this user has exchanged messages with
        $userIds = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->select('sender_id', 'receiver_id')
            ->get()
            ->flatMap(function ($msg) use ($userId) {
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->reject(function ($id) use ($userId) {
                return $id === $userId;
            })
            ->unique();
            
        $this->conversations = User::whereIn('id', $userIds)->get();
    }

    public function selectConversation(int $userId)
    {
        $this->activeUser = User::find($userId);
        $this->loadMessages();
        
        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function loadMessages()
    {
        if (!$this->activeUser) return;
        
        $userId = Auth::id();
        $otherUserId = $this->activeUser->id;
        
        $this->messages = Message::where(function($query) use ($userId, $otherUserId) {
            $query->where('sender_id', $userId)->where('receiver_id', $otherUserId);
        })->orWhere(function($query) use ($userId, $otherUserId) {
            $query->where('sender_id', $otherUserId)->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|max:1000'
        ]);

        if (!$this->activeUser) return;

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->activeUser->id,
            'content' => $this->newMessage,
            'is_read' => false
        ]);

        $this->newMessage = '';
        $this->loadMessages();
        
        // If we want it to show up on top, we might reload conversations
        // $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}
