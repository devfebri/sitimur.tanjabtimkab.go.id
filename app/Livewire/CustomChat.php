<?php

namespace App\Livewire;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomChat extends Component
{
    public $conversations = [];
    public $selectedConversation = null;
    public $messages = [];
    public $newMessage = '';
    public $searchUsers = '';
    public $availableUsers = [];
    public $pengajuanId = null;

    public function mount($pengajuanId = null)
    {
        $this->pengajuanId = $pengajuanId;
        $this->loadConversations();
        $this->loadAvailableUsers();
    }

    public function loadConversations()
    {
        $userId = Auth::id();
        
        $this->conversations = ChatConversation::whereJsonContains('participants', $userId)
            ->with(['lastMessage.user', 'pengajuan'])
            ->orderBy('last_message_at', 'desc')
            ->get();
    }

    public function loadAvailableUsers()
    {
        $currentUser = Auth::user();
        
        // PPK bisa chat dengan Pokja, Pokja bisa chat dengan PPK
        if ($currentUser->role === 'ppk') {
            $this->availableUsers = User::where('role', 'pokjapemilihan')
                ->where('id', '!=', $currentUser->id)
                ->get();
        } elseif ($currentUser->role === 'pokjapemilihan') {
            $this->availableUsers = User::where('role', 'ppk')
                ->where('id', '!=', $currentUser->id)
                ->get();
        }
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversation = ChatConversation::find($conversationId);
        
        if ($this->selectedConversation && $this->selectedConversation->hasParticipant(Auth::id())) {
            $this->loadMessages();
            $this->markMessagesAsRead();
        }
    }

    public function loadMessages()
    {
        if (!$this->selectedConversation) return;

        $this->messages = ChatMessage::where('conversation_id', $this->selectedConversation->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        if (trim($this->newMessage) === '') return;

        if (!$this->selectedConversation) return;

        $message = ChatMessage::create([
            'conversation_id' => $this->selectedConversation->id,
            'user_id' => Auth::id(),
            'message' => $this->newMessage,
            'type' => 'text'
        ]);

        // Update conversation last message time
        $this->selectedConversation->update(['last_message_at' => now()]);

        $this->newMessage = '';
        $this->loadMessages();
        $this->loadConversations();

        // Broadcast event untuk real-time
        $this->dispatch('message-sent', [
            'conversationId' => $this->selectedConversation->id,
            'message' => $message->load('user')
        ]);
    }

    public function startNewChat($userId)
    {
        $currentUserId = Auth::id();
        
        // Check jika sudah ada conversation
        $existingConversation = ChatConversation::where(function($query) use ($currentUserId, $userId) {
            $query->whereJsonContains('participants', $currentUserId)
                  ->whereJsonContains('participants', $userId);
        })->first();

        if ($existingConversation) {
            $this->selectConversation($existingConversation->id);
            return;
        }

        // Buat conversation baru
        $conversation = ChatConversation::create([
            'type' => 'direct',
            'participants' => [$currentUserId, $userId],
            'pengajuan_id' => $this->pengajuanId,
            'last_message_at' => now()
        ]);

        $this->loadConversations();
        $this->selectConversation($conversation->id);
    }

    public function markMessagesAsRead()
    {
        if (!$this->selectedConversation) return;

        ChatMessage::where('conversation_id', $this->selectedConversation->id)
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    #[On('message-received')]
    public function messageReceived($data)
    {
        if ($this->selectedConversation && $this->selectedConversation->id == $data['conversationId']) {
            $this->loadMessages();
        }
        $this->loadConversations();
    }

    public function render()
    {
        return view('livewire.custom-chat');
    }
}
