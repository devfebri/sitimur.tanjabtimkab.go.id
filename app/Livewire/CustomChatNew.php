<?php

namespace App\Livewire;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomChat extends Component
{
    use WithFileUploads;

    public $conversations = [];
    public $selectedConversation = null;
    public $messages = [];
    public $newMessage = '';
    public $searchUsers = '';
    public $availableUsers = [];
    public $pengajuanId = null;
    public $fileUpload = null;
    public $isUploading = false;

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
        if (trim($this->newMessage) === '' && !$this->fileUpload) return;

        if (!$this->selectedConversation) return;

        $messageData = [
            'conversation_id' => $this->selectedConversation->id,
            'user_id' => Auth::id(),
            'type' => 'text'
        ];

        // Handle file upload
        if ($this->fileUpload) {
            $this->isUploading = true;
            
            try {
                // Store file
                $fileName = time() . '_' . $this->fileUpload->getClientOriginalName();
                $filePath = $this->fileUpload->storeAs('chat-files', $fileName, 'public');
                
                $messageData['type'] = 'document';
                $messageData['message'] = $this->newMessage ?: 'Mengirim file: ' . $this->fileUpload->getClientOriginalName();
                $messageData['file_path'] = $filePath;
                $messageData['file_name'] = $this->fileUpload->getClientOriginalName();
                $messageData['file_size'] = $this->fileUpload->getSize();
                $messageData['file_type'] = $this->fileUpload->getClientOriginalExtension();
                
                $this->fileUpload = null;
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal mengupload file: ' . $e->getMessage());
                $this->isUploading = false;
                return;
            }
            
            $this->isUploading = false;
        } else {
            $messageData['message'] = $this->newMessage;
        }

        $message = ChatMessage::create($messageData);

        // Update conversation last message time
        $this->selectedConversation->update(['last_message_at' => now()]);

        $this->newMessage = '';
        $this->loadMessages();
        $this->loadConversations();
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

    public function downloadFile($messageId)
    {
        $message = ChatMessage::find($messageId);
        
        if (!$message || !$message->isFile() || !$message->file_path) {
            session()->flash('error', 'File tidak ditemukan');
            return;
        }

        // Check if user has access to this conversation
        if (!$message->conversation->hasParticipant(Auth::id())) {
            session()->flash('error', 'Akses ditolak');
            return;
        }

        return Storage::disk('public')->download($message->file_path, $message->file_name);
    }

    public function render()
    {
        return view('livewire.custom-chat');
    }
}
