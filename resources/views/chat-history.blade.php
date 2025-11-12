@extends('layouts.app')

@section('title', 'Chat History - Verifikator')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row mb-4">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h2 font-weight-bold text-primary mb-0">
                        <i class="fas fa-comments me-2"></i>Chat History
                    </h1>
                    <p class="text-muted mt-2 mb-0">Riwayat percakapan dengan PPK yang sedang verifikasi</p>
                </div>
                <a href="{{ route('verifikator_dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari nama paket atau PPK...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="verifikasi">Verifikasi</option>
                                <option value="pokja">Pokja Pemilihan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="sortFilter">
                                <option value="latest">Pesan Terbaru</option>
                                <option value="oldest">Pesan Tertua</option>
                                <option value="unread">Pesan Belum Dibaca</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($pengajuans->count() > 0)
        <div class="row">
            <div class="col-lg-10">
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase font-weight-bold">Total Pengajuan</h6>
                                <h3 class="text-primary font-weight-bold">{{ $pengajuans->total() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase font-weight-bold">Total Pesan</h6>
                                <h3 class="text-success font-weight-bold">
                                    @php
                                        $totalMessages = collect($chatStats)->sum('total_messages');
                                    @endphp
                                    {{ $totalMessages }}
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-muted text-uppercase font-weight-bold">Belum Dibaca</h6>
                                <h3 class="text-danger font-weight-bold">
                                    @php
                                        $totalUnread = collect($chatStats)->sum('unread_messages');
                                    @endphp
                                    {{ $totalUnread }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List of pengajuan with chat -->
                <div class="list-group shadow-sm" id="chatList">
                    @foreach($pengajuans as $pengajuan)
                        @php
                            $stats = $chatStats[$pengajuan->id] ?? [];
                            $totalMessages = $stats['total_messages'] ?? 0;
                            $unreadMessages = $stats['unread_messages'] ?? 0;
                            $lastMessage = $stats['last_message'] ?? null;
                            $lastMessageTime = $stats['last_message_time'] ?? null;
                            $statusText = $pengajuan->status < 20 ? 'Verifikasi' : 'Pokja Pemilihan';
                            $statusBgClass = $pengajuan->status < 20 ? 'bg-warning' : 'bg-info';
                        @endphp

                        <div class="list-group-item list-group-item-action py-3 border-bottom chat-item transition-all"
                             data-pengajuan-id="{{ $pengajuan->id }}"
                             data-status="{{ strtolower($statusText) }}"
                             data-search="{{ strtolower($pengajuan->nama_paket . ' ' . $pengajuan->user->name) }}"
                             data-unread="{{ $unreadMessages }}">

                            <div class="row align-items-center h-100">
                                <div class="col-lg-8">
                                    <!-- Pengajuan Info -->
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="avatar-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width: 50px; height: 50px; border-radius: 50%; font-weight: bold; font-size: 1.2rem;">
                                            {{ strtoupper(substr($pengajuan->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <h5 class="mb-1 font-weight-bold text-truncate">
                                                {{ $pengajuan->nama_paket }}
                                                @if($unreadMessages > 0)
                                                    <span class="badge badge-danger ms-2 badge-pulse">
                                                        {{ $unreadMessages }}
                                                    </span>
                                                @endif
                                            </h5>
                                            <p class="mb-1 text-muted small">
                                                <strong>PPK:</strong> {{ $pengajuan->user->name }}<br>
                                                <strong>No. Paket:</strong> {{ $pengajuan->nomor_paket ?? '-' }}
                                            </p>
                                            <div class="mt-2">
                                                <span class="badge {{ $statusBgClass }} text-white">
                                                    {{ $statusText }} ({{ $pengajuan->status }})
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Last Message Preview -->
                                    @if($lastMessage)
                                        <div class="small text-muted ms-5 ps-3 border-left-3 border-secondary">
                                            <i class="fas fa-comment-dots me-1 text-secondary"></i>
                                            <strong>{{ $lastMessage->user->name }}:</strong>
                                            <span class="d-inline-block text-truncate" style="max-width: 300px;">
                                                {{ Str::limit($lastMessage->message ?? '[File: ' . basename($lastMessage->file_path) . ']', 80) }}
                                            </span>
                                            <br>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $lastMessageTime }}</small>
                                        </div>
                                    @else
                                        <div class="small text-muted ms-5 ps-3 text-secondary">
                                            <i class="fas fa-comment-slash me-1"></i><em>Belum ada percakapan</em>
                                        </div>
                                    @endif
                                </div>

                                <!-- Message Count & Action -->
                                <div class="col-lg-4">
                                    <div class="d-flex justify-content-between align-items-center h-100 flex-wrap gap-2">
                                        <div class="text-center order-1 order-lg-0">
                                            <div class="h5 mb-0 font-weight-bold text-primary">{{ $totalMessages }}</div>
                                            <small class="text-muted">Pesan</small>
                                        </div>
                                        <a href="{{ route('verifikator_pengajuan.chat', ['id' => $pengajuan->id]) }}"
                                           class="btn btn-sm btn-primary order-0 order-lg-1">
                                            <i class="fas fa-comments me-1"></i>Buka Chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-5">
                    {{ $pengajuans->links() }}
                </div>

                <!-- No Results -->
                <div id="noResults" style="display: none;">
                    <div class="card border-0 shadow-sm text-center py-4">
                        <div class="card-body">
                            <i class="fas fa-search text-muted" style="font-size: 2rem;"></i>
                            <p class="card-text text-muted mt-3">Tidak ada percakapan yang sesuai dengan kriteria pencarian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="card-title font-weight-bold">Belum Ada Percakapan</h5>
                        <p class="card-text text-muted mb-0">
                            Anda belum memiliki percakapan dengan PPK manapun. <br>
                            Percakapan akan muncul di sini setelah PPK mengirim pesan dalam proses verifikasi.
                        </p>
                        <a href="{{ route('verifikator_dashboard') }}" class="btn btn-primary mt-4">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .border-left-3 {
        border-left: 3px solid !important;
    }

    .min-width-0 {
        min-width: 0;
    }

    .chat-item {
        transition: all 0.3s ease;
    }

    .chat-item:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    }

    .avatar-circle {
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .badge-pulse {
        animation: pulse-badge 2s infinite;
    }

    @keyframes pulse-badge {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        50% {
            transform: scale(1.1);
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0);
        }
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 600;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Status badge colors */
    .bg-warning {
        background-color: #ffc107 !important;
        color: #333 !important;
    }

    .bg-info {
        background-color: #17a2b8 !important;
        color: white !important;
    }

    .transition-all {
        transition: all 0.2s ease;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-lg-4, .col-lg-8 {
            max-width: 100%;
            flex: 0 0 100%;
        }

        .list-group-item {
            padding: 1rem 0.5rem !important;
        }

        .avatar-circle {
            width: 40px !important;
            height: 40px !important;
            font-size: 0.9rem !important;
        }

        .btn-sm {
            width: 100%;
            margin-top: 0.5rem;
        }
    }
</style>

@if($pengajuans->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortFilter = document.getElementById('sortFilter');
        const chatList = document.getElementById('chatList');
        const chatItems = document.querySelectorAll('.chat-item');
        const noResults = document.getElementById('noResults');

        function filterAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusVal = statusFilter.value.toLowerCase();
            const visibleItems = [];
            let visibleCount = 0;

            chatItems.forEach(item => {
                const search = item.getAttribute('data-search');
                const status = item.getAttribute('data-status');
                const unread = parseInt(item.getAttribute('data-unread'));

                // Apply filters
                let matches = true;
                if (searchTerm && !search.includes(searchTerm)) {
                    matches = false;
                }
                if (statusVal && status !== statusVal) {
                    matches = false;
                }

                if (matches) {
                    item.style.display = '';
                    visibleItems.push({
                        element: item,
                        unread: unread
                    });
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Apply sorting
            if (sortFilter.value === 'unread') {
                visibleItems.sort((a, b) => b.unread - a.unread);
                visibleItems.forEach(item => {
                    chatList.appendChild(item.element);
                });
            }

            // Show/hide no results message
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterAndSort);
        statusFilter.addEventListener('change', filterAndSort);
        sortFilter.addEventListener('change', filterAndSort);
    });
</script>
@endif
@endsection
