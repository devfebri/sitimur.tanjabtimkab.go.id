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
        border-radius: 8px;
        max-width: 80%;
    }
    .comment.received {
        margin-right: auto;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    .comment.sent {
        margin-left: auto;
        background-color: #e3effd;
        border: 1px solid #cce4fb;
    }
    .comment.sent .comment-avatar {
        background-color: #0d6efd;
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
        font-weight: bold;
        margin-bottom: 0;
    }
    .comment-time {
        font-size: 12px;
        color: #6c757d;
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
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    .comment.received .comment-attachment {
        margin-left: 50px;
    }
    .comment.sent .comment-attachment {
        margin-right: 50px;
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
                                </p>
                            </div>
                            <a href="{{ $returnRoute }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left mr-1"></i> Kembali ke Pengajuan
                            </a>
                        </div>
                        
                        <!-- Chat/Comments Area -->
                        <div class="chat-box" id="chatBox">
                            <div id="messageContainer">
                                <!-- Example Messages -->
                                <!-- Sent message example (from current user) -->
                                <div class="comment sent">
                                    <div class="comment-header">
                                        <div class="comment-avatar">
                                            P
                                        </div>
                                        <div class="comment-info">
                                            <p class="comment-name">PPK Budi (Anda)</p>
                                            <small class="comment-time">Oct 19, 2023 09:30 AM</small>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        Selamat pagi, mohon review dokumen pengadaan yang telah saya lampirkan.
                                    </div>
                                    <div class="comment-attachment">
                                        <i class="mdi mdi-file-pdf-box"></i>
                                        <a href="#">Dokumen_Pengadaan_Rev1.pdf</a>
                                    </div>
                                </div>

                                <!-- Received message example (from other user) -->
                                <div class="comment received">
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
                                        <img src="https://via.placeholder.com/200x150" alt="Screenshot Revisi" class="attachment-preview">
                                    </div>
                                </div>

                                <!-- Another sent message -->
                                <div class="comment sent">
                                    <div class="comment-header">
                                        <div class="comment-avatar">
                                            P
                                        </div>
                                        <div class="comment-info">
                                            <p class="comment-name">PPK Budi (Anda)</p>
                                            <small class="comment-time">Oct 19, 2023 10:30 AM</small>
                                        </div>
                                    </div>
                                    <div class="comment-text">
                                        Terima kasih, akan saya tindaklanjuti perubahannya.
                                    </div>
                                </div>

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
                                        <label for="fileInput" class="file-upload-button">
                                            <i class="mdi mdi-paperclip"></i> Lampirkan File
                                        </label>
                                        <input type="file" id="fileInput" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-send"></i> Kirim
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
    // Initial load of messages
    loadMessages();

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

    // Remove file handler
    window.removeFile = function() {
        $('#fileInput').val('');
        $('#filePreview').hide();
        $('#fileName').text('');
        $('#imagePreview').addClass('d-none').attr('src', '');
    }

    // Handle form submission
    $('#commentForm').submit(function(e) {
        e.preventDefault();
        
        var message = $('#messageInput').val();
        var file = $('#fileInput')[0].files[0];
        
        if (!message.trim() && !file) return; // Don't send empty messages without files

        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', message);
        if (file) {
            formData.append('file', file);
        }

        $.ajax({
            url: '{{ route($userRole === "ppk" ? "ppk_pengajuan.chat.send" : "pokjapemilihan_pengajuan.chat.send", ["id" => $pengajuan->id]) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#messageInput').val(''); // Clear input
                removeFile(); // Clear file input
                loadMessages(); // Reload messages
                scrollToBottom();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Error sending message');
            }
        });
    });

    function loadMessages() {
        $.get('{{ route($userRole === "ppk" ? "ppk_pengajuan.chat.get-new" : "pokjapemilihan_pengajuan.chat.get-new", ["id" => $pengajuan->id]) }}', function(response) {
            response.comments.forEach(function(comment) {
                addMessageToChat(comment);
            });
            scrollToBottom();
        });
    }

    function addMessageToChat(comment) {
        let fileHtml = '';
        if (comment.file_path) {
            const fileName = comment.file_name || comment.file_path.split('/').pop();
            const isImage = /\.(jpg|jpeg|png|gif)$/i.test(comment.file_path);
            
            if (isImage) {
                fileHtml = `
                    <div class="comment-attachment">
                        <img src="${comment.file_path}" alt="Attached Image" class="attachment-preview">
                    </div>
                `;
            } else {
                fileHtml = `
                    <div class="comment-attachment">
                        <i class="mdi mdi-file-document-outline"></i>
                        <a href="${comment.file_path}" target="_blank">${fileName}</a>
                    </div>
                `;
            }
        }

        const messageHtml = `
            <div class="comment" data-comment-id="${comment.id}">
                <div class="comment-header">
                    <div class="comment-avatar">
                        ${comment.sender_initial}
                    </div>
                    <div class="comment-info">
                        <p class="comment-name">${comment.sender_name}</p>
                        <small class="comment-time">${comment.time}</small>
                    </div>
                </div>
                <div class="comment-text">
                    ${comment.message}
                </div>
                ${fileHtml}
            </div>
        `;
        $('#messageContainer').append(messageHtml);
    }

    function scrollToBottom() {
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    $('#commentForm').on('submit', function(e) {
        e.preventDefault();
        const message = $('#messageInput').val().trim();
        
        if (!message) return;

        $.ajax({
            url: '/chats/send',
            method: 'POST',
            data: {
                message: message,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#messageInput').val('');
                loadMessages();
            }
        });
    });

    // Initial load and polling
    loadMessages();
    setInterval(loadMessages, 5000);
});
</script>
@endsection






