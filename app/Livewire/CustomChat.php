<?php

namespace App\Livewire;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomChat extends Component
{
    use WithFileUploads;    public $conversations = [];
    public $selectedConversation = null;
    public $messages = [];
    public $newMessage = '';
    public $searchConversations = '';
    public $pengajuanId = null;
    public $withUserId = null;
    public $fileUpload = null;
    public $isUploading = false;    protected $listeners = [
        'set-chat-context' => 'setChatContext'
    ];public function setChatContext($params)
    {
        $this->pengajuanId = $params['pengajuanId'] ?? null;
        $this->withUserId = $params['withUserId'] ?? null;
        
        $this->loadConversations();
        
        // Auto-open chat dengan user tertentu jika ada
        if ($this->withUserId) {
            $this->autoStartChatWithUser($this->withUserId);
        }
    }public function mount($pengajuanId = null)
    {        $this->pengajuanId = $pengajuanId;
        $this->withUserId = request()->query('with_user');
        
        $this->loadConversations();
        
        // Auto-open chat dengan user tertentu jika parameter with_user ada
        if ($this->withUserId) {
            $this->autoStartChatWithUser($this->withUserId);
        }
    }public function loadConversations()
    {
        $userId = Auth::id();
        $searchQuery = trim($this->searchConversations);
        
        $conversationsQuery = ChatConversation::whereJsonContains('participants', $userId)
            ->with(['lastMessage.user', 'pengajuan']);
            
        // Tambahkan filter pencarian jika ada query
        if (!empty($searchQuery)) {
            $conversationsQuery->whereHas('pengajuan', function($q) use ($searchQuery) {
                $q->where('nama_pengadaan', 'LIKE', '%' . $searchQuery . '%');
            })->orWhereHas('lastMessage', function($q) use ($searchQuery) {
                $q->where('message', 'LIKE', '%' . $searchQuery . '%');
            })->orWhere(function($q) use ($searchQuery, $userId) {
                // Cari berdasarkan nama participant lain
                $q->whereJsonContains('participants', $userId);
            });
        }
        
        $this->conversations = $conversationsQuery
            ->orderBy('last_message_at', 'desc')
            ->get();
    }
      public function updatedSearchConversations()
    {
        $this->loadConversations();
    }
      public function autoStartChatWithUser($userId)
    {
        // Cek apakah sudah ada conversation dengan user ini
        $existingConversation = ChatConversation::where(function($query) use ($userId) {
            $query->whereJsonContains('participants', Auth::id())
                  ->whereJsonContains('participants', (int)$userId);
        })->first();
        
        if ($existingConversation) {
            // Jika sudah ada conversation, langsung buka
            $this->selectConversation($existingConversation->id);
            session()->flash('info', 'Chat dibuka dengan ' . User::find($userId)->name);
        } else {
            // Jika belum ada, buat conversation baru
            $this->startNewChat($userId);
            session()->flash('success', 'Chat baru dimulai dengan ' . User::find($userId)->name);
        }    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversation = ChatConversation::find($conversationId);
        
        if ($this->selectedConversation && $this->selectedConversation->hasParticipant(Auth::id())) {
            $this->loadMessages();
            $this->markMessagesAsRead();
            
            // Emit event for JavaScript to update badge
            $this->dispatch('conversation-selected');
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
            
            $this->isUploading = false;        } else {
            $messageData['message'] = $this->newMessage;
        }        $message = ChatMessage::create($messageData);

        // Fire event untuk real-time broadcasting
        event(new MessageSent($message));

        // Update conversation last message time
        $this->selectedConversation->update(['last_message_at' => now()]);

        // Emit Livewire event for UI updates
        $this->dispatch('message-sent');

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
    }    public function downloadFile($messageId)
    {
        $message = ChatMessage::find($messageId);
        
        if (!$message || !$message->isFile() || !$message->file_path) {
            session()->flash('error', 'File tidak ditemukan');
            return redirect()->back();
        }

        $filePath = storage_path('app/public/' . $message->file_path);
        
        if (!file_exists($filePath)) {
            session()->flash('error', 'File tidak ada di server');
            return redirect()->back();
        }

        return response()->download($filePath, $message->file_name);
    }

    // API method untuk mengambil unread count
    public function getUnreadCount()
    {
        $unreadCount = 0;
        $user = Auth::user();
        
        if ($user && in_array($user->role, ['ppk', 'pokjapemilihan'])) {
            $conversationIds = ChatConversation::whereJsonContains('participants', $user->id)
                ->pluck('id');
            
            $unreadCount = ChatMessage::whereIn('conversation_id', $conversationIds)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }
          return response()->json(['count' => $unreadCount]);
    }

    public function messageReceived($data)
    {
        // Reload messages when new message received
        $this->loadMessages();
        
        // Emit event to scroll to bottom
        $this->dispatch('message-received');
    }

    public function render()
    {
        return view('livewire.custom-chat');
    }
}
