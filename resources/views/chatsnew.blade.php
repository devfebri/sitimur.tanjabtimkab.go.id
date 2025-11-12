{{-- filepath: resources/views/chats.blade.php --}}
@extends('layouts.master')

@section('css')
<style>
    .chat-box {
        max-height: 500px;
        overflow-y: auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .comment {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 12px;
        max-width: 80%;
        position: relative;
    }
    .comment.received {
        margin-right: auto;
        margin-left: 20px;
        background-color: #f0f2f5;
        border: none;
        border-top-left-radius: 4px;
    }
    .comment.sent {
        margin-left: auto;
        margin-right: 20px;
        background-color: #e3f2fd;
        border: none;
        border-top-right-radius: 4px;
    }
    .comment.sent .comment-avatar {
        background-color: #1976d2;
    }
    .comment.received .comment-avatar {
        background-color: #455a64;
    }
    .comment-header {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    .comment.sent .comment-header {
        flex-direction: row-reverse;
    }
    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #2a3f54;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-weight: bold;
    }
    .comment.sent .comment-avatar {
        margin-right: 0;
        margin-left: 10px;
    }
    .comment-info {
        flex-grow: 1;
    }
    .comment.sent .comment-info {
        text-align: right;
    }
    .comment-name {
        font-weight: 600;
        margin-bottom: 0;
        color: #1a1a1a;
    }
    .comment.sent .comment-name {
        color: #1976d2;
    }
    .comment.received .comment-name {
        color: #455a64;
    }
    .comment-time {
        font-size: 11px;
        color: #757575;
        margin-top: 2px;
    }
    .comment-text {
        margin-left: 50px;
    }
    .comment.sent .comment-text {
        margin-left: 0;
        margin-right: 50px;
    }
    .comment-attachment {
        margin-top: 10px;
        padding: 10px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .comment.received .comment-attachment {
        margin-left: 50px;
        border: 1px solid #e0e0e0;
    }
    .comment.sent .comment-attachment {
        margin-right: 50px;
        border: 1px solid #bbdefb;
    }
    .comment-attachment i {
        margin-right: 5px;
        color: #6c757d;
    }
    .attachment-preview {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
    }
    .file-upload-wrapper {
        position: relative;
        margin-top: 10px;
    }
    .file-upload-button {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload-button:hover {
        background: #e9ecef;
    }
    .file-upload-button i {
        margin-right: 5px;
    }
    .file-preview {
        margin-top: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 4px;
        display: none;
    }
    .file-preview img {
        max-width: 100px;
        max-height: 100px;
    }
    .remove-file {
        color: #dc3545;
        cursor: pointer;
        margin-left: 10px;
    }
    .typing-area {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 20px;
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Loading states */
    .loading-state {
        position: relative;
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .mdi-loading {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Message animation */
    .comment {
        animation: slideIn 0.3s ease-out;
        opacity: 0;
        animation-fill-mode: forwards;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* New message indicator */
    .new-messages-indicator {
        text-align: center;
        padding: 8px;
        background-color: #e3f2fd;
        color: #1976d2;
        border-radius: 20px;
        margin: 10px 0;
        cursor: pointer;
        display: none;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="mt-0 header-title">Diskusi - {{ $pengajuan->nama_paket }}</h4>
                                <p class="text-muted mb-0">
                                    <small>{{ $pengajuan->nomor_surat }}</small>
                                    @if($chatType === 'verifikator')
                                        <span class="badge badge-info ml-2">Chat Verifikator (PPK + Verifikator)</span>
                                    @elseif($chatType === 'pokja')
                                        <span class="badge badge-success ml-2">Chat Pokja Pemilihan (PPK + Pokja1/2/3)</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ $returnRoute }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left mr-1"></i> Kembali ke Pengajuan
                            </a>
                        </div>

                        <!-- Chat/Comments Area -->
                        <div class="chat-box" id="chatBox">
                            <div id="messageContainer">
                                <div id="initialMessageContainer">
                                    <!-- Messages will be loaded here by jQuery -->
                                    <p class="text-center text-muted loading-message">Memuat pesan...</p>
                                </div>


                                <!-- Received message example (from other user) -->
                                {{-- <div class="comment received">
                                    <div class="comment-header">
                                        <div class="comment-avatar">
                                            K
                                        </div>
                                        <div class="comment-info">
                                            <p class="comment-name">Ketua Pokja</p>
                                            <small class="comment-time">Oct 19, 2023 10:15 AM</small>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        Baik Pak, akan saya review. Untuk screenshot hasil revisi bisa dilihat di bawah ini:
                                    </div>
                                    <div class="comment-attachment">
                                        <i class="mdi mdi-file-pdf-box"></i>
                                        <a href="#">Dokumen_Pengadaan_Rev1.pdf</a>
                                    </div>
                                </div> --}}

                                <!-- Another sent message -->
                                {{-- @if(auth()->user()->id==$chatMessages) --}}

                                 {{-- <div class="comment sent">
                                    <div class="comment-header">
                                        <div class="comment-avatar">
                                            P
                                        </div>
                                        <div class="comment-info">
                                            <p class="comment-name">PPK Budi (Anda)</p>
                                            <small class="comment-time">dasdsa</small>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        sdaa
                                    </div>
                                </div> --}}



                                <!-- Real Messages will be loaded here -->
                            </div>
                        </div>

                        <!-- Comment Form -->
                        <div class="mt-3">
                            <form id="commentForm" class="form" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea class="form-control" id="messageInput" rows="3" placeholder="Tulis pesan..."></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <!-- File Upload -->
                                    <div class="file-upload-wrapper">
                                        <label for="fileInput" class="file-upload-button" id="fileUploadBtn">
                                            <i class="mdi mdi-paperclip"></i> Lampirkan File
                                        </label>
                                        <input type="file" id="fileInput" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </div>

                                    <button type="submit" class="btn btn-primary" id="sendButton">
                                        <span class="normal-state">
                                            <i class="mdi mdi-send"></i> Kirim
                                        </span>
                                        <span class="loading-state d-none">
                                            <i class="mdi mdi-loading mdi-spin"></i> Mengirim...
                                        </span>
                                    </button>
                                </div>

                                <!-- File Preview -->
                                <div id="filePreview" class="file-preview mt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-file mr-2"></i>
                                        <span id="fileName"></span>
                                        <i class="mdi mdi-close remove-file" onclick="removeFile()"></i>
                                    </div>
                                    <img id="imagePreview" class="mt-2 d-none">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    // Load initial messages
    loadInitialMessages();

    // Set up polling for new messages
    setInterval(loadMessages, 5000); // Poll every 5 seconds

    // File input change handler
    $('#fileInput').change(function() {
        const file = this.files[0];
        if (file) {
            $('#filePreview').show();
            $('#fileName').text(file.name);

            // Preview for images
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').addClass('d-none');
            }
        }
    });

    // Handle Enter key in textarea (Ctrl+Enter or Shift+Enter to send)
    $('#messageInput').keydown(function(e) {
        // Check if Enter is pressed with Ctrl or Shift
        if ((e.ctrlKey || e.shiftKey) && (e.key === 'Enter' || e.keyCode === 13)) {
            e.preventDefault();
            $('#commentForm').submit();
        }
    });

    // Remove file handler
    window.removeFile = function() {
        $('#fileInput').val('');
        $('#filePreview').hide();
        $('#fileName').text('');
        $('#imagePreview').addClass('d-none').attr('src', '');
    }

    let isSubmitting = false;

    // Handle form submission
    $('#commentForm').submit(function(e) {
        e.preventDefault();

        if (isSubmitting) return; // Prevent multiple submissions

        var message = $('#messageInput').val();
        var file = $('#fileInput')[0].files[0];

        if (!message.trim() && !file) return; // Don't send empty messages without files

        // Update button state
        isSubmitting = true;
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.find('.normal-state').addClass('d-none');
        submitBtn.find('.loading-state').removeClass('d-none');

        // Disable file upload during submission
        $('#fileUploadBtn').addClass('disabled').css('pointer-events', 'none');

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', message);
        if (file) {
            formData.append('file', file);
        }

        // Add temporary message
        const tempMessage = {
            id: 'temp_' + Date.now(),
            user_id: {{ auth()->id() }},
            sender_name: '{{ auth()->user()->name }}',
            sender_initial: '{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}',
            message: message,
            time: new Date().toLocaleString(),
            file_path: file ? URL.createObjectURL(file) : null,
            file_name: file ? file.name : null
        };
        addMessageToChat(tempMessage);
        scrollToBottom();

        // Clear input fields immediately
        $('#messageInput').val('');
        removeFile();

        @if($userRole === 'ppk')
            var sendUrl = '{{ route("ppk_pengajuan.chat.send", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'pokjapemilihan')
            var sendUrl = '{{ route("pokjapemilihan_pengajuan.chat.send", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'verifikator')
            var sendUrl = '{{ route("verifikator_pengajuan.chat.send", ["id" => $pengajuan->id]) }}';
        @else
            var sendUrl = '{{ route("ppk_pengajuan.chat.send", ["id" => $pengajuan->id]) }}';
        @endif

        $.ajax({
            url: sendUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Remove temporary message
                $('[data-comment-id="' + tempMessage.id + '"]').remove();

                // Add actual message
                if (response.comment) {
                    addMessageToChat(response.comment);
                    lastMessageId = response.comment.id;
                }
                scrollToBottom();

                // Reset form state after successful submission
                resetFormState();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);

                // Show error notification
                const errorMsg = 'Gagal mengirim pesan. ' + (xhr.responseJSON?.message || 'Silakan coba lagi.');
                $('<div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">')
                    .text(errorMsg)
                    .append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>')
                    .insertAfter('#commentForm');

                // Remove temporary message
                $('[data-comment-id="' + tempMessage.id + '"]').slideUp(300, function() {
                    $(this).remove();
                });

                // Reset form state
                resetFormState();

                // Restore message in textarea for retry
                $('#messageInput').val(message);
            },
            complete: function() {
                isSubmitting = false;
            }
        });
    });

    // Format date time helper
    function formatDateTime(dateStr) {
        try {
            const date = new Date(dateStr);
            if (isNaN(date.getTime())) {
                return dateStr; // Return original if invalid
            }
            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateStr;
        }
    }

    let lastMessageId = 0;
    let loadedMessageIds = new Set();

    // Load initial messages when page loads
    function loadInitialMessages() {
        @if($userRole === 'ppk')
            var url = '{{ route("ppk_pengajuan.chat.get", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'pokjapemilihan')
            var url = '{{ route("pokjapemilihan_pengajuan.chat.get", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'verifikator')
            var url = '{{ route("verifikator_pengajuan.chat.get", ["id" => $pengajuan->id]) }}';
        @else
            var url = '{{ route("ppk_pengajuan.chat.get", ["id" => $pengajuan->id]) }}';
        @endif

        $.get(url)
            .done(function(response) {
                $('#initialMessageContainer').empty();
                $('#messageContainer').empty();

                if (response.messages && response.messages.length > 0) {
                    response.messages.forEach(function(message) {
                        addMessageToChat(message);
                        loadedMessageIds.add(message.id);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                } else {
                    $('#messageContainer').html('<p class="text-center text-muted">Belum ada pesan.</p>');
                }
                scrollToBottom();
            })
            .fail(function(xhr, status, error) {
                console.error('Error loading initial messages:', error);
                $('#messageContainer').html('<p class="text-center text-danger">Gagal memuat pesan. Silakan refresh halaman.</p>');
            });
    }

    // Load new messages periodically
    function loadMessages() {
        if (lastMessageId === undefined) return; // Skip if initial messages haven't loaded yet

        @if($userRole === 'ppk')
            var pollUrl = '{{ route("ppk_pengajuan.chat.get-new", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'pokjapemilihan')
            var pollUrl = '{{ route("pokjapemilihan_pengajuan.chat.get-new", ["id" => $pengajuan->id]) }}';
        @elseif($userRole === 'verifikator')
            var pollUrl = '{{ route("verifikator_pengajuan.chat.get-new", ["id" => $pengajuan->id]) }}';
        @else
            var pollUrl = '{{ route("ppk_pengajuan.chat.get-new", ["id" => $pengajuan->id]) }}';
        @endif

        $.get(pollUrl, {
            last_id: lastMessageId
        })
        .done(function(response) {
            if (response.messages && response.messages.length > 0) {
                let hasNewMessages = false;

                response.messages.forEach(function(message) {
                    if (!loadedMessageIds.has(message.id) &&
                        !document.querySelector(`[data-comment-id="${message.id}"]`)) {
                        addMessageToChat(message);
                        loadedMessageIds.add(message.id);
                        lastMessageId = Math.max(lastMessageId, message.id);
                        hasNewMessages = true;
                    }
                });

                if (hasNewMessages) {
                    scrollToBottom();
                }
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error loading new messages:', error);
        });
    }

    function addMessageToChat(message) {
        if (!message) return; // Skip if message is null/undefined

        const isCurrentUser = message.user_id == {{ auth()->id() }};
        const senderName = message.user ? message.user.name : message.sender_name;
        const senderInitial = senderName ? senderName.charAt(0).toUpperCase() : '?';
        const messageId = message.id || 'temp_' + Date.now();

        let messageHtml = `
            <div class="comment ${isCurrentUser ? 'sent' : 'received'}" data-comment-id="${messageId}">
                <div class="comment-header">
                    <div class="comment-avatar">
                        ${senderInitial}
                    </div>
                    <div class="comment-info">
                        <p class="comment-name">${senderName}${isCurrentUser ? ' (Anda)' : ''}</p>
                        <small class="comment-time">${formatDateTime(message.created_at || message.time)}</small>
                    </div>
                </div>
                <div class="comment-text">
                    ${message.message || ''}
                </div>`;

        if (message.file_path) {
            const fileName = message.file_name || message.file_path.split('/').pop();

            // Build proper URL based on file path format
            let fileUrl;
            if (message.file_path.startsWith('http://') || message.file_path.startsWith('https://')) {
                // Absolute URL
                fileUrl = message.file_path;
            } else if (message.file_path.startsWith('/storage/')) {
                // Already has /storage/ prefix
                fileUrl = message.file_path;
            } else {
                // Relative path - add /storage/ prefix
                fileUrl = '/storage/' + message.file_path;
            }

            const isImage = /\.(jpg|jpeg|png|gif|JPG|PNG)$/i.test(fileName);

            messageHtml += `
                <div class="comment-attachment">
                    ${isImage ?
                        `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" alt="Attached Image" class="attachment-preview" onerror="this.style.display='none'"></a>` :
                        `<i class="mdi mdi-file-document-outline"></i>
                         <a href="${fileUrl}" target="_blank">${fileName}</a>`
                    }
                </div>
            `;
        }

        messageHtml += `</div>`;

        // Only append if the message doesn't already exist
        if (!document.querySelector(`[data-comment-id="${messageId}"]`)) {
            $('#messageContainer').append(messageHtml);
        }
    }

    function resetFormState() {
        const submitBtn = $('#sendButton');
        submitBtn.prop('disabled', false);
        submitBtn.find('.loading-state').addClass('d-none');
        submitBtn.find('.normal-state').removeClass('d-none');
        $('#fileUploadBtn').removeClass('disabled').css('pointer-events', 'auto');
    }

    function scrollToBottom(animate = true) {
        const chatBox = document.getElementById('chatBox');
        if (animate) {
            $(chatBox).animate({ scrollTop: chatBox.scrollHeight }, 300);
        } else {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    // Scroll to bottom when images are loaded to account for their height
    $(document).on('load', '.attachment-preview', function() {
        scrollToBottom(true);
    });

    // Show new messages indicator when user is not at bottom
    let isNearBottom = true;
    $('#chatBox').on('scroll', function() {
        const chatBox = $(this)[0];
        isNearBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 100;

        if (isNearBottom) {
            $('.new-messages-indicator').fadeOut(200);
        }
    });

    // Click handler for new messages indicator
    $('.new-messages-indicator').click(function() {
        scrollToBottom(true);
        $(this).fadeOut(200);
    });
});
</script>
@endsection






