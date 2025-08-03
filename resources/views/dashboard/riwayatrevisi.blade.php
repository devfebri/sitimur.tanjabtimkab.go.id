@extends('layouts.master')

@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group pull-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route(auth()->user()->role.'_dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Revisi File</li>
                </ol>
            </div>
            <h4 class="page-title">Riwayat Revisi File</h4>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h4 class="mt-0 header-title">
                                    <i class="mdi mdi-history text-primary"></i> Riwayat Revisi File
                                </h4>
                                <p class="text-muted mb-0">Daftar semua perubahan dan revisi file yang telah dilakukan pada pengajuan.</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                        <i class="mdi mdi-filter-variant"></i> Filter Pengajuan
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="?filter=all">Semua Pengajuan</a>
                                        <a class="dropdown-item" href="?filter=recent">Terbaru</a>
                                        <a class="dropdown-item" href="?filter=mine">Pengajuan Saya</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($riwayatRevisi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Pengajuan</th>
                                        <th width="15%">File</th>
                                        <th width="15%">Jenis Revisi</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Direvisi Oleh</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($riwayatRevisi as $index => $revisi)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-bold text-primary">{{ Str::limit($revisi->pengajuan->nama_pengadaan, 40) }}</span>
                                                <small class="text-muted">{{ $revisi->pengajuan->jenis_pengadaan ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="mdi {{ $revisi->getFileIcon() }} text-info mr-2"></i>
                                                <div>
                                                    <span class="font-weight-medium">{{ Str::limit($revisi->nama_file, 20) }}</span>
                                                    <br><small class="text-muted">{{ $revisi->getFormattedFileSize() }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $revisi->getRevisionTypeColor() }} badge-pill">
                                                <i class="mdi {{ $revisi->getRevisionTypeIcon() }} mr-1"></i>
                                                {{ $revisi->jenis_revisi }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $revisi->getStatusColor() }} badge-pill">
                                                {{ $revisi->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-medium">{{ $revisi->user->name }}</span>
                                                <small class="text-muted">{{ $revisi->user->role == 'ppk' ? 'PPK' : ($revisi->user->role == 'pokjapemilihan' ? 'Pokja Pemilihan' : 'Verifikator') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="font-weight-medium">{{ $revisi->created_at->format('d/m/Y') }}</span>
                                                <small class="text-muted">{{ $revisi->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($revisi->file_path)
                                                <a href="{{ route(auth()->user()->role.'_download_revision', $revisi->id) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Download File">
                                                    <i class="mdi mdi-download"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route(auth()->user()->role.'_pengajuanopen', $revisi->pengajuan_id) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Lihat Pengajuan">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                @if($revisi->keterangan)
                                                <button type="button" 
                                                        class="btn btn-outline-secondary btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#detailModal{{ $revisi->id }}"
                                                        title="Detail Revisi">
                                                    <i class="mdi mdi-information-outline"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info">
                                    Menampilkan {{ $riwayatRevisi->firstItem() ?? 0 }} sampai {{ $riwayatRevisi->lastItem() ?? 0 }} 
                                    dari {{ $riwayatRevisi->total() }} entri
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                {{ $riwayatRevisi->links() }}
                            </div>
                        </div>

                        @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="mdi mdi-file-document-outline" style="font-size: 4rem; color: #dee2e6;"></i>
                            </div>
                            <h5 class="text-muted">Belum Ada Riwayat Revisi</h5>
                            <p class="text-muted mb-4">Riwayat revisi file akan muncul di sini setelah ada perubahan pada dokumen pengajuan.</p>
                            <a href="{{ route(auth()->user()->role.'_dashboard') }}" class="btn btn-primary">
                                <i class="mdi mdi-arrow-left mr-1"></i> Kembali ke Dashboard
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modals -->
@if($riwayatRevisi->count() > 0)
@foreach($riwayatRevisi as $revisi)
@if($revisi->keterangan)
<div class="modal fade" id="detailModal{{ $revisi->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-file-document-edit mr-2"></i>
                    Detail Revisi - {{ Str::limit($revisi->nama_file, 30) }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Pengajuan:</label>
                            <p>{{ $revisi->pengajuan->nama_pengadaan }}</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">File:</label>
                            <p>{{ $revisi->nama_file }}</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Jenis Revisi:</label>
                            <p>
                                <span class="badge badge-{{ $revisi->getRevisionTypeColor() }}">
                                    {{ $revisi->jenis_revisi }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Direvisi Oleh:</label>
                            <p>{{ $revisi->user->name }} ({{ ucfirst($revisi->user->role) }})</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Tanggal:</label>
                            <p>{{ $revisi->created_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Status:</label>
                            <p>
                                <span class="badge badge-{{ $revisi->getStatusColor() }}">
                                    {{ $revisi->status }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-muted">Keterangan Revisi:</label>
                    <div class="alert alert-light border-left-primary">
                        <p class="mb-0">{{ $revisi->keterangan }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @if($revisi->file_path)
                <a href="{{ route(auth()->user()->role.'_download_revision', $revisi->id) }}" 
                   class="btn btn-primary">
                    <i class="mdi mdi-download mr-1"></i> Download File
                </a>
                @endif
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endif

@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Auto refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>
@endsection
