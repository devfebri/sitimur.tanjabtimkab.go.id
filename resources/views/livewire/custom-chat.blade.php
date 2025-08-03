<!-- Single root element for Livewire component -->
<div class="custom-chat-wrapper">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <i class="mdi mdi-information-outline me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="govt-chat-container-inner">
        <div class="row g-0 h-100">
            <!-- Sidebar - Conversations List -->
            <div class="col-lg-4 col-md-5 col-12">
                <div class="chat-sidebar">
                    <!-- Sidebar Header -->
                    <div class="sidebar-header">
                        <div class="d-flex align-items-center justify-content-between">                            <h6 class="mb-0 fw-bold text-white" style="font-size: 0.8rem;">
                                <i class="mdi mdi-forum me-1"></i>Percakapan
                            </h6>
                            {{-- <div class="badge bg-light text-dark" style="font-size: 0.65rem;">{{ count($conversations) }}</div> --}}
                        </div>                        <small class="text-white-50 d-block mt-1" style="font-size: 0.65rem;">
                            {{ Auth::user()->role == 'ppk' ? 'Pejabat Pembuat Komitmen' : 'Tim Pokja Pemilihan' }}
                        </small>                    </div>
                    
                    <!-- Conversations List -->
                    <div class="conversations-section">
                        @if(count($conversations) > 0)                            <div class="section-title">
                                <i class="mdi mdi-history me-1"></i>Riwayat Komunikasi
                                @if(!empty($searchConversations))
                                    <span class="search-counter">({{ count($conversations) }} hasil)</span>
                                @endif
                            </div>
                            
                            <!-- Search Conversations -->
                            <div class="conversation-search-container mb-2">
                                <div class="search-input-group">
                                    <i class="mdi mdi-magnify search-icon"></i>                                    <input type="text" 
                                           class="user-search-input" 
                                           placeholder="Cari percakapan... (Ctrl+Shift+F)"
                                           wire:model.live.debounce.300ms="searchConversations"
                                           id="conversationSearchInput">
                                    @if(!empty($searchConversations))
                                        <button type="button" 
                                                class="search-clear-btn" 
                                                wire:click="$set('searchConversations', '')">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="conversations-list">                            @forelse($conversations as $conversation)
                                @php
                                    $otherUser = collect($conversation->getParticipantUsers())->firstWhere('id', '!=', Auth::id());
                                    // Hitung unread messages untuk conversation ini
                                    $unreadCount = $conversation->messages()
                                        ->where('user_id', '!=', Auth::id())
                                        ->whereNull('read_at')
                                        ->count();
                                @endphp
                                  <div class="conversation-item {{ $selectedConversation && $selectedConversation->id == $conversation->id ? 'active' : '' }} {{ $unreadCount > 0 ? 'has-unread' : '' }}"
                                     wire:click="selectConversation({{ $conversation->id }})">
                                    
                                    {{-- <div class="conversation-avatar">
                                        <i class="mdi {{ $otherUser && $otherUser->role == 'ppk' ? 'mdi-account-tie' : 'mdi-account-supervisor' }}"></i>
                                        @if($conversation->pengajuan)
                                            <div class="pengajuan-indicator">
                                                <i class="mdi mdi-file-document-outline"></i>
                                            </div>
                                        @endif
                                       
                                    </div> --}}
                                    
                                    <div class="conversation-content">
                                        <div class="conversation-header">
                                            <div class="participant-name {{ $unreadCount > 0 ? 'has-unread' : '' }}">
                                                 {{-- @if($unreadCount > 0)
                                                 <div class="unread-indicator">
                                                     <span class="unread-count">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                                                 </div>
                                                 @endif --}}
                                                 {{ $otherUser ? $otherUser->name : 'Komunikasi' }} <br> <small>{{ $otherUser->jabatan }}</small>

                                            </div>
                                            <div class="conversation-time">
                                                {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}
                                            </div>
                                        </div>
                                        
                                       
                                        
                                        @if($conversation->lastMessage)
                                            <div class="last-message {{ $unreadCount > 0 ? 'has-unread' : '' }}">
                                                @if($conversation->lastMessage->isFile())
                                                    <i class="mdi {{ $conversation->lastMessage->getFileIcon() }} me-1"></i>
                                                    {{ $conversation->lastMessage->file_name }}
                                                @else
                                                    {{ Str::limit($conversation->lastMessage->message, 40) }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- @if($unreadCount > 0)
                                        <div class="conversation-status">
                                            <div class="unread-dot"></div>
                                        </div>
                                    @endif --}}
                                </div>@empty                                <div class="empty-conversations">
                                    <div class="text-center py-3">
                                        @if(!empty($searchConversations))
                                            <i class="mdi mdi-message-search-outline mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="text-muted mb-1" style="font-size: 0.7rem;">Tidak ditemukan: "{{ $searchConversations }}"</p>
                                            <button wire:click="$set('searchConversations', '')" class="btn-clear-search">
                                                <i class="mdi mdi-refresh me-1"></i>Tampilkan Semua
                                            </button>
                                        @else
                                            <i class="mdi mdi-forum-outline mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="text-muted mb-0" style="font-size: 0.7rem;">Belum ada percakapan</p>
                                            <small class="text-muted" style="font-size: 0.65rem;">Pilih user di atas untuk memulai</small>
                                        @endif
                                    </div>
                                </div>@endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="col-lg-8 col-md-7 col-12">
                @if($selectedConversation)
                    <div class="chat-main">
                        <!-- Chat Header -->
                        <div class="chat-main-header">
                            @php
                                $otherUser = collect($selectedConversation->getParticipantUsers())->firstWhere('id', '!=', Auth::id());
                            @endphp                            <div class="d-flex align-items-center">
                                {{-- <div class="participant-avatar me-3">
                                    <i class="mdi {{ $otherUser && $otherUser->role == 'ppk' ? 'mdi-account-tie' : 'mdi-account-supervisor' }}"></i>
                                </div> --}}
                                <div class="participant-details">                            <h6 class="participant-name mb-0" style="font-size: 0.8rem;">{{ $otherUser ? $otherUser->name : 'Komunikasi' }}</h6>
                            <small class="participant-role" style="font-size: 0.65rem;">
                                {{ $otherUser ? ($otherUser->role == 'ppk' ? 'Pejabat Pembuat Komitmen' : 'Tim Pokja Pemilihan') : '' }}
                            </small>
                                </div>
                            </div>
                            
                            @if($selectedConversation->pengajuan)
                                <div class="pengajuan-context">
                                    <div class="pengajuan-info">
                                        <i class="mdi mdi-file-document-outline me-1"></i>
                                        <span class="fw-medium">{{ Str::limit($selectedConversation->pengajuan->nama_pengadaan, 40) }}</span>
                                    </div>
                                </div>
                            @endif</div>

                        <!-- Messages Area -->
                        <div class="messages-container">
                            @forelse($messages as $message)
                                <div class="message-wrapper {{ $message->user_id == Auth::id() ? 'own-message' : 'other-message' }}">
                                    <div class="message-bubble">
                                        @if($message->isFile())
                                            <!-- File Message -->
                                            <div class="file-message">
                                                <div class="file-info">
                                                    <div class="file-icon">
                                                        <i class="mdi {{ $message->getFileIcon() }}"></i>
                                                    </div>
                                                    <div class="file-details">
                                                        <div class="file-name">{{ $message->file_name }}</div>
                                                        <div class="file-size">{{ $message->getFormattedFileSize() }}</div>
                                                    </div>
                                                </div>
                                                @if($message->message && $message->message !== 'Mengirim file: ' . $message->file_name)
                                                    <div class="file-caption">{{ $message->message }}</div>
                                                @endif
                                                <div class="file-actions">
                                                    <button wire:click="downloadFile({{ $message->id }})" 
                                                            class="btn-download">
                                                        <i class="mdi mdi-download me-1"></i>Unduh File
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <!-- Text Message -->
                                            <div class="text-message">{{ $message->message }}</div>
                                        @endif
                                        
                                        <div class="message-meta">
                                            <div class="message-sender">{{ $message->user->name }}</div>
                                            <div class="message-time">{{ $message->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @empty                                <div class="empty-messages">
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-message-outline mb-2" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <h6 class="text-muted" style="font-size: 0.8rem;">Belum ada pesan</h6>
                                        <p class="text-muted mb-0" style="font-size: 0.7rem;">Mulai percakapan dengan mengirim pesan atau dokumen</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Message Input Area -->
                        <div class="message-input-area">
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                    <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <!-- File Upload Preview -->
                            @if($fileUpload)
                                <div class="file-preview">
                                    <div class="file-preview-content">
                                        <div class="file-preview-icon">
                                            <i class="mdi mdi-file-document"></i>
                                        </div>
                                        <div class="file-preview-info">
                                            <div class="file-preview-name">{{ $fileUpload->getClientOriginalName() }}</div>
                                            <div class="file-preview-size">{{ number_format($fileUpload->getSize()/1024, 1) }} KB</div>
                                        </div>
                                        <button type="button" class="file-preview-remove" wire:click="$set('fileUpload', null)">
                                            <i class="mdi mdi-close"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <form wire:submit.prevent="sendMessage" class="message-form">
                                <div class="input-group-modern">
                                    <!-- File Upload Button -->
                                    <label class="file-upload-btn" for="fileUpload">
                                        <i class="mdi mdi-paperclip"></i>
                                        <span class="tooltip-text">Lampirkan File</span>
                                        <input type="file" 
                                               id="fileUpload"
                                               wire:model="fileUpload" 
                                               style="display: none;"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar">
                                    </label>
                                    
                                    <!-- Message Input -->                                    <input type="text" 
                                           class="message-input" 
                                           placeholder="Ketik pesan atau lampirkan dokumen..."
                                           wire:model="newMessage"
                                           wire:keydown.enter.prevent="sendMessage">
                                    
                                    <!-- Send Button -->
                                    <button type="submit" 
                                            class="send-btn"
                                            {{ $isUploading ? 'disabled' : '' }}>
                                        @if($isUploading)
                                            <div class="spinner-border spinner-border-sm" role="status"></div>
                                        @else
                                            <i class="mdi mdi-send"></i>
                                        @endif
                                        <span class="tooltip-text">Kirim Pesan</span>
                                    </button>
                                </div>
                            </form>
                              <div class="input-help-text">
                                <i class="mdi mdi-information-outline me-1"></i>
                                PDF, DOC, XLS, Gambar, ZIP (Max 10MB)
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Conversation Selected -->
                    <div class="no-conversation">
                        <div class="text-center">                            <div class="no-conversation-icon">
                                <i class="mdi mdi-forum-outline" style="font-size: 3.5rem;"></i>
                            </div><h5 class="no-conversation-title" style="font-size: 1rem;">Sistem Komunikasi Digital</h5>                            <p class="no-conversation-text" style="font-size: 0.75rem;">
                                Pilih percakapan dari daftar di sebelah kiri untuk memulai komunikasi resmi
                            </p>
                            <div class="no-conversation-features">                                <div class="feature-item">
                                    <i class="mdi mdi-message-text-outline" style="font-size: 1.5rem; color: #007bff;"></i>
                                    <span style="font-size: 0.7rem;">Pesan Instan</span>
                                </div>                                <div class="feature-item">
                                    <i class="mdi mdi-file-document" style="font-size: 1.5rem; color: #6c757d;"></i>
                                    <span style="font-size: 0.7rem;">Berbagi Dokumen</span>
                                </div>                                <div class="feature-item">
                                    <i class="mdi mdi-check-circle" style="font-size: 1.5rem; color: #28a745;"></i>
                                    <span style="font-size: 0.7rem;">Aman & Terenkripsi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>    
    <style>    /* Modern Government Chat Component Styling - Full Height */
    .custom-chat-wrapper {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .govt-chat-container-inner {
        height: calc(100vh - 120px); /* Adjust based on header/footer height */
        min-height: 500px;
        border-radius: 8px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    /* Sidebar Styling */
    .chat-sidebar {
        background: linear-gradient(180deg, #2c5aa0 0%, #1e3c72 100%);
        height: 100%;
        display: flex;
        flex-direction: column;
    }      .sidebar-header {
        padding: 10px 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.1);
    }    .new-chat-section {
        padding: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        max-height: 180px;
        overflow-y: auto;
    }
      .user-search-container {
        padding: 0 8px;
    }
    
    .conversation-search-container {
        padding: 0 8px;
        margin-bottom: 8px;
    }
      .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 6px 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .search-input-group:focus-within {
        background: rgba(255, 255, 255, 0.15);
        border-color: #FFD700;
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        transform: translateY(-1px);
    }
    
    .search-icon {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        margin-right: 8px;
        transition: all 0.3s ease;
    }
    
    .search-input-group:focus-within .search-icon {
        color: #FFD700;
    }
    
    .user-search-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: white;
        font-size: 0.75rem;
        padding: 2px 0;
    }
    
    .user-search-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.7rem;
    }
    
    .search-clear-btn {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.6);
        cursor: pointer;
        padding: 2px;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 0.7rem;
    }
      .search-clear-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #FFD700;
    }
    
    .btn-clear-search {
        background: rgba(255, 215, 0, 0.2);
        color: #FFD700;
        border: 1px solid rgba(255, 215, 0, 0.3);
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.65rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 4px;
    }
    
    .btn-clear-search:hover {
        background: rgba(255, 215, 0, 0.3);
        border-color: #FFD700;
    }
      .conversations-section {
        flex: 1;
        overflow-y: auto;
        padding: 6px 0;
    }      .section-title {
        color: #FFD700;
        font-size: 0.7rem;
        font-weight: 600;
        margin-bottom: 6px;
        padding: 0 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .search-counter {
        color: rgba(255, 215, 0, 0.7);
        font-size: 0.6rem;
        font-weight: 400;
        text-transform: none;
        background: rgba(255, 215, 0, 0.1);
        padding: 2px 6px;
        border-radius: 8px;
        border: 1px solid rgba(255, 215, 0, 0.2);
    }
      .user-item, .conversation-item {
        padding: 6px 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        position: relative;
    }
    
    .user-item:hover {
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #FFD700;
    }
    
    .conversation-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-left-color: #FFD700;
    }
      .conversation-item.active {
        background: rgba(255, 215, 0, 0.2);
        border-left-color: #FFD700;
    }
    
    /* Unread message indicators */
    .conversation-item.has-unread {
        background: rgba(255, 255, 255, 0.08);
        border-left-color: #dc3545;
    }
    
    .conversation-item.has-unread:hover {
        background: rgba(255, 255, 255, 0.12);
    }.user-avatar, .conversation-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        position: relative;
    }
      .user-avatar i, .conversation-avatar i {
        font-size: 1rem;
        color: #FFD700;
    }
    
    /* Unread count badge on avatar */
    .unread-indicator {
        position: absolute;
        top: -3px;
        right: -3px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        min-width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 600;
        border: 2px solid #1e3c72;
        animation: pulse-unread 2s infinite;
    }
    
    @keyframes pulse-unread {
        0% { 
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% { 
            transform: scale(1.1);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0);
        }
        100% { 
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
    
    .unread-count {
        line-height: 1;
    }
      .pengajuan-indicator {
        position: absolute;
        bottom: -1px;
        right: -1px;
        width: 12px;
        height: 12px;
        background: #28a745;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid white;
    }
    
    .pengajuan-indicator i {
        font-size: 0.5rem;
        color: white;
    }
      .user-info, .conversation-content {
        flex: 1;
        color: white;
    }
    
    .user-name, .participant-name {
        font-weight: 600;
        margin-bottom: 2px;
    }
      .participant-name {
        font-weight: 600;
        margin-bottom: 2px;
    }
    
    .participant-name.has-unread {
        font-weight: 700;
        color: #FFD700;
    }
    
    .user-role, .participant-role {
        opacity: 0.8;
        font-size: 0.8rem;
    }
    
    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .conversation-time {
        font-size: 0.75rem;
        opacity: 0.7;
    }
    
    .pengajuan-badge {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.7rem;
        margin-bottom: 5px;
        display: inline-block;
    }
      .last-message {
        font-size: 0.8rem;
        opacity: 0.8;
        margin-top: 5px;
    }
    
    .last-message.has-unread {
        font-weight: 600;
        opacity: 1;
        color: #FFD700;
    }
    
    .user-action, .conversation-status {
        color: #FFD700;
        opacity: 0.7;
    }
    
    /* Unread dot indicator */
    .unread-dot {
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        animation: pulse-dot 1.5s infinite;
    }
    
    @keyframes pulse-dot {
        0% { 
            opacity: 1;
            transform: scale(1);
        }
        50% { 
            opacity: 0.5;
            transform: scale(1.2);
        }
        100% { 
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Main Chat Area */
    .chat-main {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }      .chat-main-header {
        background: white;
        padding: 8px 12px;
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
      .participant-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: linear-gradient(45deg, #2c5aa0, #1e3c72);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .participant-avatar i {
        font-size: 1rem;
        color: #FFD700;
    }
    
    .participant-name {
        font-size: 0.85rem;
        font-weight: 600;
        color: #CCDBF1FF;
    }
    
    .participant-role {
        color: #6c757d;
        font-size: 0.7rem;
    }
      .pengajuan-context {
        margin-top: 8px;
        padding: 6px 10px;
        background: linear-gradient(45deg, #e3f2fd, #f3e5f5);
        border-radius: 8px;
        border-left: 3px solid #2196f3;
    }
    
    .pengajuan-info {
        color: #1976d2;
        font-size: 0.75rem;
        font-weight: 500;
    }
      /* Messages Container */
    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        background: linear-gradient(to bottom, #f8f9fa, #ffffff);
    }
    
    .message-wrapper {
        margin-bottom: 8px;
        display: flex;
    }
    
    .message-wrapper.own-message {
        justify-content: flex-end;
    }
    
    .message-wrapper.other-message {
        justify-content: flex-start;
    }
      .message-bubble {
        max-width: 75%;
        padding: 8px 12px;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        font-size: 0.8rem;
    }
      .own-message .message-bubble {
        background: linear-gradient(135deg, #2c5aa0, #1e3c72);
        color: white;
        border-bottom-right-radius: 3px;
    }
    
    .other-message .message-bubble {
        background: white;
        color: #333;
        border: 1px solid #e9ecef;
        border-bottom-left-radius: 3px;
    }
    
    .text-message {
        line-height: 1.3;
        word-wrap: break-word;
    }
      .file-message {
        border-radius: 6px;
        overflow: hidden;
    }
    
    .file-info {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }
    
    .file-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: rgba(255, 215, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
    }
    
    .file-icon i {
        font-size: 1rem;
        color: #FFD700;
    }
      .file-name {
        font-weight: 600;
        margin-bottom: 1px;
        font-size: 0.75rem;
    }
    
    .file-size {
        font-size: 0.65rem;
        opacity: 0.7;
    }
    
    .file-caption {
        margin: 6px 0;
        padding-top: 6px;
        border-top: 1px solid rgba(255,255,255,0.2);
        font-size: 0.75rem;
    }
      .btn-download {
        background: rgba(255, 215, 0, 0.2);
        color: #FFD700;
        border: 1px solid #FFD700;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .btn-download:hover {
        background: #FFD700;
        color: #1e3c72;
    }
      .message-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 4px;
        font-size: 0.65rem;
        opacity: 0.7;
    }
      /* Message Input Area */
    .message-input-area {
        background: white;
        padding: 8px 12px;
        border-top: 1px solid #e9ecef;
    }
      .file-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px;
        margin-bottom: 8px;
    }
    
    .file-preview-content {
        display: flex;
        align-items: center;
    }
    
    .file-preview-icon {
        width: 28px;
        height: 28px;
        background: #e3f2fd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
    }
    
    .file-preview-icon i {
        color: #2196f3;
        font-size: 1rem;
    }
    
    .file-preview-info {
        flex: 1;
    }
    
    .file-preview-name {
        font-weight: 600;
        margin-bottom: 1px;
        font-size: 0.75rem;
    }
    
    .file-preview-size {
        font-size: 0.65rem;
        color: #6c757d;
    }
    
    .file-preview-remove {
        background: #dc3545;
        color: white;
        border: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.7rem;
    }
    
    .file-preview-remove:hover {
        background: #c82333;
    }
      .input-group-modern {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 20px;
        padding: 3px;
        transition: all 0.3s ease;
    }
    
    .input-group-modern:focus-within {
        border-color: #2c5aa0;
        box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
    }
      .file-upload-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #2c5aa0;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        margin-right: 6px;
        font-size: 0.9rem;
    }
    
    .file-upload-btn:hover {
        background: #1e3c72;
        transform: scale(1.05);
    }
    
    .message-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 8px 10px;
        font-size: 0.8rem;
        outline: none;
    }
    
    .message-input::placeholder {
        color: #6c757d;
        font-size: 0.75rem;
    }
    
    .send-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        margin-left: 6px;
        font-size: 0.9rem;
    }
    
    .send-btn:hover:not(:disabled) {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    
    .send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
      .tooltip-text {
        position: absolute;
        bottom: 38px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 0.6rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    
    .file-upload-btn:hover .tooltip-text,
    .send-btn:hover .tooltip-text {
        opacity: 1;
    }
      .input-help-text {
        text-align: center;
        color: #6c757d;
        font-size: 0.65rem;
        margin-top: 6px;
    }
      /* No Conversation State */
    .no-conversation {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        padding: 20px;
    }
    
    .no-conversation-icon {
        font-size: 3.5rem;
        color: #dee2e6;
        margin-bottom: 15px;
    }
    
    .no-conversation-title {
        color: #2c5aa0;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1rem;
    }
    
    .no-conversation-text {
        color: #6c757d;
        line-height: 1.4;
        margin-bottom: 20px;
        max-width: 350px;
        font-size: 0.75rem;
    }
    
    .no-conversation-features {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .feature-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: #6c757d;
        font-size: 0.7rem;
    }
    
    .feature-item i {
        font-size: 1.5rem;
        color: #2c5aa0;
        opacity: 0.7;
    }
    
    .empty-conversations, .empty-messages {
        color: rgba(255, 255, 255, 0.8);
    }    /* Responsive Design */
    @media (max-width: 991px) {
        .govt-chat-container-inner {
            height: calc(100vh - 100px);
            min-height: 400px;
        }
        
        .chat-sidebar {
            position: absolute;
            left: -100%;
            top: 0;
            width: 280px;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        
        .chat-sidebar.active {
            left: 0;
        }
        
        .chat-main-header {
            position: relative;
        }
        
        .mobile-sidebar-toggle {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #2c5aa0;
            color: white;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
    }    @media (max-width: 768px) {
        .govt-chat-container-inner {
            border-radius: 6px;
            height: calc(100vh - 80px);
            min-height: 350px;
        }
        
        .message-bubble {
            max-width: 85%;
            padding: 6px 10px;
            font-size: 0.75rem;
        }
        
        .chat-main-header {
            padding: 6px 10px;
        }
        
        .messages-container {
            padding: 8px;
        }
        
        .message-input-area {
            padding: 6px 10px;
        }
        
        .no-conversation {
            padding: 15px;
        }
        
        .no-conversation-features {
            gap: 15px;
        }
        
        .feature-item i {
            font-size: 1.2rem;
        }
        
        .feature-item {
            font-size: 0.7rem;
        }
        
        /* Responsive unread indicators */
        .unread-indicator {
            min-width: 14px;
            height: 14px;
            font-size: 0.55rem;
            top: -2px;
            right: -2px;
        }
        
        .unread-dot {
            width: 6px;
            height: 6px;
        }
    }    @media (max-width: 576px) {
        .govt-chat-container-inner {
            height: calc(100vh - 60px);
            min-height: 300px;
        }
        
        .participant-details {
            display: none;
        }
        
        .message-bubble {
            max-width: 90%;
            padding: 6px 8px;
            font-size: 0.7rem;
        }
        
        .no-conversation-icon {
            font-size: 2.5rem;
        }
        
        .no-conversation-title {
            font-size: 1rem;
        }
        
        .no-conversation-text {
            font-size: 0.8rem;
        }
        
        .user-name, .participant-name {
            font-size: 0.75rem;
        }
        
        .user-role, .participant-role {
            font-size: 0.65rem;
        }
        
        .section-title {
            font-size: 0.65rem;
        }
    }
      /* Custom Scrollbar */
    .conversations-section::-webkit-scrollbar,
    .messages-container::-webkit-scrollbar,
    .new-chat-section::-webkit-scrollbar {
        width: 4px;
    }
    
    .conversations-section::-webkit-scrollbar-track,
    .messages-container::-webkit-scrollbar-track,
    .new-chat-section::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .conversations-section::-webkit-scrollbar-thumb,
    .messages-container::-webkit-scrollbar-thumb,
    .new-chat-section::-webkit-scrollbar-thumb {
        background: rgba(255, 215, 0, 0.5);
        border-radius: 8px;
    }
    
    .conversations-section::-webkit-scrollbar-thumb:hover,
    .messages-container::-webkit-scrollbar-thumb:hover,
    .new-chat-section::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 215, 0, 0.7);
    }
    </style>    <script>
    document.addEventListener('livewire:initialized', () => {
        console.log('🏛️ Government Chat Component Initialized');
        
        // Auto scroll to bottom when new messages arrive
        function scrollToBottom() {
            const messagesContainer = document.querySelector('.messages-container');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
        
        // Initial scroll to bottom
        setTimeout(scrollToBottom, 500);
        
        // Listen for new messages
        Livewire.on('message-sent', () => {
            setTimeout(scrollToBottom, 100);
        });
        
        // Mobile sidebar toggle
        function createMobileSidebarToggle() {
            if (window.innerWidth <= 991) {
                const chatHeader = document.querySelector('.chat-main-header');
                const sidebar = document.querySelector('.chat-sidebar');
                
                if (chatHeader && sidebar && !document.querySelector('.mobile-sidebar-toggle')) {
                    const toggleBtn = document.createElement('button');
                    toggleBtn.className = 'mobile-sidebar-toggle';
                    toggleBtn.innerHTML = '<i class="mdi mdi-menu"></i>';
                    
                    toggleBtn.addEventListener('click', () => {
                        sidebar.classList.toggle('active');
                    });
                    
                    chatHeader.appendChild(toggleBtn);
                    
                    // Close sidebar when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                            sidebar.classList.remove('active');
                        }
                    });
                }
            }
        }
        
        // Initialize mobile features
        createMobileSidebarToggle();
        
        // Handle window resize
        window.addEventListener('resize', () => {
            createMobileSidebarToggle();
            
            // Remove mobile toggle on desktop
            if (window.innerWidth > 991) {
                const toggleBtn = document.querySelector('.mobile-sidebar-toggle');
                const sidebar = document.querySelector('.chat-sidebar');
                if (toggleBtn) toggleBtn.remove();
                if (sidebar) sidebar.classList.remove('active');
            }
        });
        
        // File upload preview enhancement
        const fileInput = document.getElementById('fileUpload');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    console.log('📎 File selected:', this.files[0].name);
                }
            });
        }
        
        // Add typing indicator (placeholder for future enhancement)
        const messageInput = document.querySelector('.message-input');
        if (messageInput) {
            let typingTimer;
            
            messageInput.addEventListener('keyup', () => {
                clearTimeout(typingTimer);
                // Future: Show typing indicator
                
                typingTimer = setTimeout(() => {
                    // Future: Hide typing indicator
                }, 1000);
            });
        }
        
        // Smooth animations for message bubbles
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.classList && node.classList.contains('message-wrapper')) {
                            node.style.opacity = '0';
                            node.style.transform = 'translateY(20px)';
                            
                            setTimeout(() => {
                                node.style.transition = 'all 0.3s ease';
                                node.style.opacity = '1';
                                node.style.transform = 'translateY(0)';
                            }, 100);
                        }
                    });
                }
            });
        });
        
        const messagesContainer = document.querySelector('.messages-container');
        if (messagesContainer) {
            observer.observe(messagesContainer, { childList: true });
        }
        
        // Initialize chat system
        // Initialize chat system
        console.log('✅ Government Chat System Ready for Communication');
        
        // Auto-dismiss flash messages
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
        
        // Smooth scroll to chat jika ada with_user parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('with_user')) {
            setTimeout(() => {
                const chatContainer = document.querySelector('.messages-container');
                if (chatContainer) {
                    chatContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 1000);
        }
        
        // Keyboard shortcuts for search
        document.addEventListener('keydown', function(e) {
            // Ctrl+F for user search
            if (e.ctrlKey && e.key === 'f' && !e.shiftKey) {
                e.preventDefault();
                const userSearchInput = document.getElementById('userSearchInput');
                if (userSearchInput) {
                    userSearchInput.focus();
                }
            }
            
            // Ctrl+Shift+F for conversation search
            if (e.ctrlKey && e.shiftKey && e.key === 'F') {
                e.preventDefault();
                const conversationSearchInput = document.getElementById('conversationSearchInput');
                if (conversationSearchInput) {
                    conversationSearchInput.focus();
                }
            }
            
            // ESC to clear focused search
            if (e.key === 'Escape') {
                const activeElement = document.activeElement;
                if (activeElement && (activeElement.id === 'userSearchInput' || activeElement.id === 'conversationSearchInput')) {
                    activeElement.blur();
                }
            }
        });
        
        // Search input enhancements
        function enhanceSearchInputs() {
            const searchInputs = document.querySelectorAll('.user-search-input');
            searchInputs.forEach(input => {
                // Add loading indicator
                input.addEventListener('input', () => {
                    const parent = input.closest('.search-input-group');
                    const icon = parent.querySelector('.search-icon');
                    if (icon) {
                        icon.className = 'mdi mdi-loading mdi-spin search-icon';
                        setTimeout(() => {
                            icon.className = 'mdi mdi-magnify search-icon';
                        }, 500);
                    }
                });
            });
        }
        
        // Initialize search enhancements
        setTimeout(enhanceSearchInputs, 1000);
    });
    </script>
</div>
