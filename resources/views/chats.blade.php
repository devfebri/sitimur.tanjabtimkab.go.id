{{-- filepath: resources/views/chats.blade.php --}}
@extends('layouts.master')

@section('css')
    <style>
        /* Minimal styling for Custom Chat */
        .page-content-wrapper {
            background: #f8f9fa;
        }
    </style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-bold">💬 Chat System</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Chat</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-lg-10">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">
                            <i class="mdi mdi-account me-1"></i>{{ $userRole == 'ppk' ? 'PPK' : 'Pokja Pemilihan' }}
                        </span>
                        <span class="text-muted">{{ $userName }}</span>
                    </div>
                    <div class="text-muted small">
                        <i class="mdi mdi-clock-outline me-1"></i>{{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
          <!-- Custom Chat Component -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                @livewire('custom-chat', ['pengajuanId' => request()->query('pengajuan')])
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('javascript')
    <script>
        console.log('Custom Chat loaded');
        
        document.addEventListener('livewire:initialized', () => {
            console.log('Livewire initialized for Custom Chat');
        });
    </script>
@endsection




