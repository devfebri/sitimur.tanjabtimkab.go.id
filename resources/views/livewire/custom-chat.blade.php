<!-- Single root element for Livewire component -->
<div class="custom-chat-wrapper">
    <div class="chat-container">
        <div class="row h-100">
            <!-- Sidebar - Conversations List -->
            <div class="col-md-4 border-end bg-light">
                <div class="p-3 border-bottom">
                    <h5 class="mb-0">💬 Chat</h5>
                    <small class="text-muted">{{ Auth::user()->role == 'ppk' ? 'PPK' : 'Pokja Pemilihan' }}</small>
                </div>
                
                <!-- New Chat -->
                <div class="p-3 border-bottom">
                    <h6 class="mb-2">Mulai Chat Baru</h6>
                    @foreach($availableUsers as $user)
                        <div class="d-flex align-items-center mb-2 p-2 rounded hover-bg" 
                             style="cursor: pointer;"
                             wire:click="startNewChat({{ $user->id }})">
                            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-medium">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->role == 'ppk' ? 'PPK' : 'Pokja Pemilihan' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Conversations List -->
                <div class="conversations-list" style="max-height: 400px; overflow-y: auto;">
                    @forelse($conversations as $conversation)
                        <div class="conversation-item p-3 border-bottom {{ $selectedConversation && $selectedConversation->id == $conversation->id ? 'bg-primary-subtle' : '' }}"
                             style="cursor: pointer;"
                             wire:click="selectConversation({{ $conversation->id }})">
                            
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2">
                                    💬
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">
                                            @php
                                                $otherUser = collect($conversation->getParticipantUsers())->firstWhere('id', '!=', Auth::id());
                                            @endphp
                                            {{ $otherUser ? $otherUser->name : 'Chat' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}
                                        </small>
                                    </div>
                                    
                                    @if($conversation->pengajuan)
                                        <small class="text-info">📋 {{ $conversation->pengajuan->nama_pengadaan }}</small>
                                    @endif
                                    
                                    @if($conversation->lastMessage)
                                        <p class="mb-0 text-muted small">
                                            {{ Str::limit($conversation->lastMessage->message, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted">
                            <p>Belum ada percakapan</p>
                            <small>Pilih user di atas untuk memulai chat</small>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8 d-flex flex-column">
                @if($selectedConversation)
                    <!-- Chat Header -->
                    <div class="p-3 border-bottom bg-white">
                        @php
                            $otherUser = collect($selectedConversation->getParticipantUsers())->firstWhere('id', '!=', Auth::id());
                        @endphp
                        <h6 class="mb-0">{{ $otherUser ? $otherUser->name : 'Chat' }}</h6>
                        <small class="text-muted">{{ $otherUser ? ($otherUser->role == 'ppk' ? 'PPK' : 'Pokja Pemilihan') : '' }}</small>
                        
                        @if($selectedConversation->pengajuan)
                            <div class="mt-1">
                                <span class="badge bg-info">📋 {{ $selectedConversation->pengajuan->nama_pengadaan }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Messages Area -->
                    <div class="messages-area flex-grow-1 p-3" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                        @forelse($messages as $message)
                            <div class="message mb-3 {{ $message->user_id == Auth::id() ? 'text-end' : '' }}">
                                <div class="d-inline-block p-2 rounded {{ $message->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-white' }}" 
                                     style="max-width: 70%;">
                                    <div class="message-content">{{ $message->message }}</div>
                                    <small class="opacity-75 d-block mt-1">
                                        {{ $message->user->name }} • {{ $message->created_at->format('H:i') }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">
                                <p>Belum ada pesan</p>
                                <small>Mulai percakapan dengan mengirim pesan</small>
                            </div>
                        @endforelse
                    </div>

                    <!-- Message Input -->
                    <div class="p-3 border-top bg-white">
                        <form wire:submit.prevent="sendMessage" class="d-flex">
                            <input type="text" 
                                   class="form-control me-2" 
                                   placeholder="Ketik pesan..."
                                   wire:model="newMessage"
                                   wire:keydown.enter.prevent="sendMessage">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-send"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- No Conversation Selected -->
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="mdi mdi-chat-outline" style="font-size: 4rem;"></i>
                            <h5 class="mt-3">Pilih percakapan untuk memulai chat</h5>
                            <p>Atau pilih user di sebelah kiri untuk memulai chat baru</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
    .chat-container {
        height: 70vh;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }

    .hover-bg:hover {
        background-color: #f8f9fa !important;
    }

    .conversation-item:hover {
        background-color: #f8f9fa !important;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .messages-area {
        scroll-behavior: smooth;
    }

    /* Auto scroll to bottom */
    .messages-area:after {
        content: "";
        display: block;
        height: 1px;
    }
    </style>

    <script>
    document.addEventListener('livewire:initialized', () => {
        // Auto scroll ke bawah ketika ada pesan baru
        Livewire.on('message-sent', () => {
            const messagesArea = document.querySelector('.messages-area');
            if (messagesArea) {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }
        });
    });
    </script>
</div>
