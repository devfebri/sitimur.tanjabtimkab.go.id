@extends('layouts.master')
@section('css')
<style>
    .badge-primary-soft {
        background: #e3eafd;
        color: #2a3f54;
    }

    .badge-success-soft {
        background: #e6f4ea;
        color: #256029;
    }

    .badge-danger-soft {
        background: #fdeaea;
        color: #a94442;
    }

    .badge-warning-soft {
        background: #fff8e1;
        color: #8a6d3b;
    }

    .badge-secondary-soft {
        background: #f2f2f2;
        color: #555;
    }

</style>
@endsection


@section('content')


<div class="page-content-wrapper ">

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <a href="javascript:history.back()" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>

                    @if(auth()->user()->role=='kepalaukpbj' && $data->status==11)
                    <div>
                        <button id="disposisiPokja" class="btn btn-success btn-lg mr-2 shadow-sm">
                            <i class="fa fa-share-square"></i> Disposisi ke Pokja Pemilihan
                        </button>
                        <button id="tolakPengajuan" class="btn btn-danger btn-lg shadow-sm">
                            <i class="fa fa-times-circle"></i> Tolak Pengajuan
                        </button>
                    </div>
                    @elseif(auth()->user()->role=='verifikator' && $data->status==0)
                    <div>

                        <button id="tolakPengajuan" class="btn btn-danger btn-lg shadow-sm">
                            <i class="fa fa-times-circle"></i> Tolak Pengajuan
                        </button>
                    </div>

                    @endif
                </div>
            </div>
        </div>





        <div class="row">

            {{-- @if($data->pokja1_id || $data->pokja2_id || $data->pokja3_id)
                <div class="col-12">
                    <table class="table text-center  table-responsive-sm table-sm">
                        <tr>
                            <td colspan="3" class="bg-white header-title "><b>Pokja yang dipilih</b></td>
                        </tr>
                        <tr>
                            @if($data->pokja1_status_akhir==0)
                            <td class="table-primary table-sm" width="33%">
                            @elseif($data->pokja1_status_akhir==1)
                            <td class="table-warning table-sm" width="33%">
                            @elseif($data->pokja1_status_akhir==2)
                            <td class="table-success table-sm" width="33%">
                            @else
                            <td class="table-danger table-sm" width="33%">
                            @endif
                                <a class="waves-effect mo-mb-2" data-container="body" data-toggle="popover" data-placement="top" data-content="Nama : {{ $data->pokja1->name }} <br> NIP : {{ $data->pokja1->nip }} <br> Jabatan: {{ $data->pokja1->jabatan }}" data-html="true">
                                    <b>{{ $data->pokja1->name }}</b>

                                    @if($data->pokja1_status_akhir==0)
                                        <p><i>Belum Direviu</i></p>
                                    @elseif($data->pokja1_status_akhir==1)
                                        <p><i>Sedang Direviu</i></p>
                                    @elseif($data->pokja1_status_akhir==2)
                                        <p><i>Selesai Direviu</i></p>
                                    @else
                                        <p><i>Errors</i></p>
                                    @endif
                                </a>
                            </td>
                            @if($data->pokja2_status_akhir==0)
                                <td class="table-primary table-sm" width="33%">
                                @elseif($data->pokja2_status_akhir==1)
                                <td class="table-warning table-sm" width="33%">
                                @elseif($data->pokja2_status_akhir==2)
                                <td class="table-success table-sm" width="33%">
                                @else
                                <td class="table-danger table-sm" width="33%">
                                @endif
                                    <a class="waves-effect mo-mb-2" data-container="body" data-toggle="popover" data-placement="top" data-content="Nama : {{ $data->pokja2->name }} <br> NIP : {{ $data->pokja2->nip }} <br> Jabatan: {{ $data->pokja2->jabatan }}" data-html="true">
                                        <b>{{ $data->pokja2->name }}</b>

                                        @if($data->pokja2_status_akhir==0)
                                            <p><i>Belum Direviu</i></p>
                                        @elseif($data->pokja2_status_akhir==1)
                                            <p><i>Sedang Direviu</i></p>
                                        @elseif($data->pokja2_status_akhir==2)
                                            <p><i>Selesai Direviu</i></p>
                                        @else
                                            <p><i>Errors</i></p>
                                        @endif
                                    </a>
                                </td>
                            @if($data->pokja3_status_akhir==0)
                            <td class="table-primary table-sm" width="33%">
                                @elseif($data->pokja3_status_akhir==1)
                            <td class="table-warning table-sm" width="33%">
                                @elseif($data->pokja3_status_akhir==2)
                            <td class="table-success table-sm" width="33%">
                                @else
                            <td class="table-danger table-sm" width="33%">
                                @endif
                                <a class="waves-effect mo-mb-2" data-container="body" data-toggle="popover" data-placement="top" data-content="Nama : {{ $data->pokja3->name }} <br> NIP : {{ $data->pokja3->nip }} <br> Jabatan: {{ $data->pokja3->jabatan }}" data-html="true">
                                    <b>{{ $data->pokja3->name }}</b>

                                    @if($data->pokja3_status_akhir==0)
                                    <p><i>Belum Direviu</i></p>
                                    @elseif($data->pokja3_status_akhir==1)
                                    <p><i>Sedang Direviu</i></p>
                                    @elseif($data->pokja3_status_akhir==2)
                                    <p><i>Selesai Direviu</i></p>
                                    @else
                                    <p><i>Errors</i></p>
                                    @endif
                                </a>
                            </td>


                        </tr>
                    </table>
                </div>
            @endif --}}
            <div class="col-lg-7 col-sm-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Detail Pengajuan </h4>
                        <table class="table ">
                            <tr>
                                <td width="35%">Kode RUP</td>
                                <td width="3%">:</td>
                                <td>{{ $data->kode_rup }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Nama Paket</td>
                                <td width="3%">:</td>
                                <td>{{ $data->perangkat_daerah }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Rekening Kegiatan</td>
                                <td width="3%">:</td>
                                <td>{{ $data->rekening_kegiatan }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Sumber Dana</td>
                                <td width="3%">:</td>
                                <td>{{ $data->sumber_dana }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Pagu Anggaran</td>
                                <td width="3%">:</td>
                                <td>Rp {{ number_format($data->pagu_anggaran,0) }}</td>

                            </tr>
                            <tr>
                                <td width="35%">Pagu HPS</td>
                                <td width="3%">:</td>
                                <td>Rp {{ number_format($data->pagu_hps,0) }}</td>

                            </tr>
                            <tr>
                                <td width="35%">Jenis Pengadaan</td>
                                <td width="3%">:</td>
                                <td>{{ $data->jenis_pengadaan }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Metode Pengadaan</td>
                                <td width="3%">:</td>
                                <td>{{ $data->metodePengadaan->nama_metode_pengadaan }}</td>
                            </tr>


                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
            <div class="col-lg-5 col-sm-12">
                <div class="card m-b-20">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Riwayat Status Paket Tender</h4>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-sm">
                                    <tr>
                                        <td><b><i class="mdi mdi-clock text-primary "></i> Tanggal Pengajuan</b> <br>&emsp;<i>{{ $data->created_at->format('d F Y') }}</i></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b><i class="mdi mdi-clock text-primary"></i> Tanggal Pengembalian Verifikator</b> <br>&emsp;
                                            <i>{{ $tglpengembalian?->created_at ? $tglpengembalian->created_at->format('d F Y') : '-' }}</i>
                                        </td>
                                    </tr>
                                    <tr>
                                       <td>
                                           <b><i class="mdi mdi-clock text-primary"></i> Tanggal Revisi</b> <br>&emsp;
                                           <i>{{ $createdAtRevisiTerakhir?->format('d F Y') ?? '-' }}</i>
                                       </td>
                                    </tr>
                                    <tr>
                                        <td><b><i class="mdi mdi-map-marker text-danger"></i> Posisi Paket Saat Ini</b> <br>&emsp;
                                            <i>
                                                @if($data->status==0)
                                                  Verifikator
                                                @elseif($data->status==11)
                                                Kepala UKPBJ
                                                @elseif($data->status==12)
                                                Verifikator
                                                @elseif($data->status==13)
                                                Verifikator
                                                @elseif($data->status==14)
                                                PPK

                                                @elseif($data->status==21)
                                                Pokja Pemilihan
                                                @elseif($data->status==22)
                                                Kepala UKPBJ
                                                @elseif($data->status==31)
                                                Pokja Pemilihan
                                                @elseif($data->status==32)
                                                Pokja Pemilihan
                                                @elseif($data->status==33)
                                                 Pokja Pemilihan
                                                 @elseif($data->status==34)
                                                 PPK
                                                 @elseif($data->status==88)
                                                 PPK


                                                @else
                                                Error
                                                @endif


                                            </i>
                                        </td>
                                    </tr>
                                    <tr>
                                        @if($data->status==0)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-primary"></i> Status Terakhir</b> <br>&emsp; <i>Menunggu Verifikator</i></td>
                                        @elseif($data->status==11)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Status Terakhir</b> <br>&emsp; <i>Menunggu Kepala UKPBJ</i></td>
                                        @elseif($data->status==12)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b> <br>&emsp; <i>Tidak Disetujui Verifikator <br>&emsp;{{ $data->updated_at->format('d/m/Y h:i:s') }}</i></td>


                                        @elseif($data->status==13)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Status Terakhir</b> <br>&emsp; <i>Menunggu Verifikasi Ulang</i></td>
                                        @elseif($data->status==14)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Status Terakhir</b> <br>&emsp; <small><i>File dikembalikan pada PPK</i></small></td>
                                        @elseif($data->status==21)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Status Terakhir</b> <br>&emsp; <i>Menunggu Reviu Pokja</i></td>
                                        @elseif($data->status==22)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b> <br>&emsp; <i>Tidak Disetujui Kepala UKPBJ <br>&emsp;{{ $data->updated_at->format('d/m/Y h:i:s') }}</i></td>

                                        @elseif($data->status==31)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Status Terakhir</b> <br>&emsp; <i>Siap Ditayangkan <br> &emsp;{{ $data->updated_at->format('d/m/Y h:i:s') }}</i></i></td>

                                        @elseif($data->status==32)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b> <br>&emsp; <i>Tidak Disetujui Pokja Pemilihan <br> &emsp;{{ $data->updated_at->format('d/m/Y h:i:s') }}</i></td>

                                        @elseif($data->status==33)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Status Terakhir</b> <br>&emsp; <i>Menunggu Verifikasi Ulang</i></td>
                                        @elseif($data->status==34)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Status Terakhir</b> <br>&emsp; <small><i>File dikembalikan pada PPK</i></small></td>
                                        @elseif($data->status==88)
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b> <br>&emsp; <small><i>System stops auto-submission, no updates for 3 days</i></small></td>
                                        @else
                                        <td><b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b> <br>&emsp; <i>Status Error</i></td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="btn-group w-100" role="group">

                                                {{-- Chat Buttons untuk PPK --}}
                                                @if(auth()->user()->role == 'ppk')
                                                    {{-- Button Chat Verifikator (Status < 20) --}}
                                                    @if($data->status < 20)
                                                        <a href="{{ route('ppk_pengajuan.chat', [$data->id]) }}"
                                                           class="btn btn-info btn-sm me-1" title="Chat dengan Verifikator">
                                                            <i class="mdi mdi-chat-processing me-1"></i>Chat
                                                        </a>
                                                    @endif

                                                    {{-- Button Chat Pokja (Status >= 20) --}}
                                                    @if($data->status >= 20)
                                                        <a href="{{ route('ppk_pengajuan.chat', [$data->id]) }}"
                                                           class="btn btn-success btn-sm me-1" title="Chat dengan Pokja Pemilihan">
                                                            <i class="mdi mdi-chat-multiple me-1"></i>Chat
                                                        </a>
                                                    @endif

                                                {{-- Chat Button untuk Verifikator --}}
                                                @elseif(auth()->user()->role == 'verifikator')
                                                    <a href="{{ route('verifikator_pengajuan.chat', [$data->id]) }}"
                                                       class="btn btn-info btn-sm me-1" title="Chat dengan PPK">
                                                        <i class="mdi mdi-chat me-1"></i>Chat
                                                    </a>

                                                {{-- Chat Button untuk Pokja Pemilihan --}}
                                                @elseif(auth()->user()->role == 'pokjapemilihan')
                                                    <a href="{{ route('pokjapemilihan_pengajuan.chat', [$data->id]) }}"
                                                       class="btn btn-success btn-sm me-1" title="Chat dengan PPK">
                                                        <i class="mdi mdi-chat-multiple me-1"></i>Chat
                                                    </a>
                                                @endif
                                                <a href="{{ route(auth()->user()->role.'_pengajuan_open_downloadpdf',[$data->id]) }}" class="btn btn-info btn-sm"> PDF</a>
                                                {{-- <a href="#" class="btn btn-warning btn-sm"> PDF</a> --}}

                                                <a href="{{ route(auth()->user()->role.'_pengajuan_open_downloadexcel',[$data->id]) }}" class="btn btn-info btn-sm"> Excel</a>



                                            </div>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-4 text-primary">
                            <i class="fas fa-users-cog"></i> Pokja yang Dipilih
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="thead-light">
                                    <tr class="text-center">
                                        <th style="width: 10px;">No</th>
                                        <th>Nama Pokja</th>
                                        <th>Jabatan</th>
                                        <th>Keterangan</th> <!-- Opsional -->
                                    </tr>
                                </thead>
                                <tbody id="table-pokja-terpilih">
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Budi</td>
                                        <td>Pokja Pengadaan Barang</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Budi</td>
                                        <td>Pokja Pengadaan Barang</td>
                                        <td>Sesuai</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Budi</td>
                                        <td>Pokja Pengadaan Barang</td>
                                        <td>Perlu Perbaikan</td>

                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> --}}




            </div> <!-- end col -->
        </div> <!-- end row -->

        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <h4 class="mt-0 header-title"> File Pengajuan
                            @if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')

                            <button type="submit" class="btn btn-warning mb-2 ml-2  float-right btn-sm update-status col-12 col-lg-2" disabled data-status="3" >
                                Perlu Perbaikan
                            </button>
                            <button type="submit" class="btn btn-success mb-2 ml-2  float-right btn-sm update-status col-12 col-lg-2" disabled data-status="1" >
                                Sesuai
                            </button>

                            <textarea name="keterangan" id="keterangan" cols="30" rows="1" class="float-right col-sm-12 col-lg-3 form-control" placeholder="Pesan Status"></textarea>
                            @endif
                        </h4>

                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">

                                <table id="datatable2" class="table table-striped table-bordered table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            @if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')
                                            <th>
                                                <div id="checkAll">
                                                    <input type="checkbox" class="checkall" />
                                                </div>
                                            </th>
                                            @endif
                                            <th>No</th>
                                            <th>Nama File</th>
                                            {{-- <th>Status Verifikator</th> --}}
                                            {{-- <th>Status Pokja Pemilihan</th> --}}
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>


                                </table>
                            </div>
                        </div>


                    </div>                    @if(auth()->user()->role=='pokjapemilihan')
                    <div class="card-footer">
                        @php
                            $pokjaKe = null;
                            if($data->pokja1_id == auth()->user()->id) $pokjaKe = 1;
                            elseif($data->pokja2_id == auth()->user()->id) $pokjaKe = 2;
                            elseif($data->pokja3_id == auth()->user()->id) $pokjaKe = 3;

                            $statusAkhir = $pokjaKe ? $data->{'pokja' . $pokjaKe . '_status_akhir'} : 0;
                        @endphp

                        @if($statusAkhir == 2)
                            <a href="#" id="tandai-selesai-reviu" class="btn btn-success btn-sm btn-block disabled" tabindex="-1" aria-disabled="true" disabled>Reviu Telah Selesai</a>
                        @else
                            <a href="#" id="tandai-selesai-reviu" class="btn btn-primary btn-sm btn-block disabled" tabindex="-1" aria-disabled="true" disabled>Tandai Selesai Reviu</a>
                        @endif
                    </div>
                    @endif
                </div>

            </div>
            @if(auth()->user()->role!='pokjapemilihan')
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Riwayat Pengajuan</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm text-center align-middle" style="font-size: 13px;">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode RUP</th>
                                        <th>Nama Paket</th>
                                        <th>Perangkat Daerah</th>
                                        <th>Rekening Kegiatan</th>
                                        <th>Sumber Dana</th>
                                        <th>Pagu Anggaran</th>
                                        <th>Pagu HPS</th>
                                        <th>Jenis Pengadaan</th>
                                        <th>Tanggal dibuat</th>
                                        <th>Posisi Paket</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($history as $key => $item)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $item->kode_rup }}</td>
                                        <td>{{ $item->nama_paket }}</td>
                                        <td>{{ $item->perangkat_daerah }}</td>
                                        <td>{{ $item->rekening_kegiatan }}</td>
                                        <td>{{ $item->sumber_dana }}</td>
                                        <td>Rp {{ number_format($item->pagu_anggaran,0) }}</td>
                                        <td>Rp {{ number_format($item->pagu_hps,0) }}</td>
                                        <td>{{ $item->jenis_pengadaan }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                         <td>
                                             @if($item->status == 0)
                                             <span class="badge badge-pill badge-primary">Verifikator</span>
                                             @elseif($item->status == 11)
                                             <span class="badge badge-pill badge-primary">Kepala UKPBJ</span>
                                             @elseif($item->status == 12)
                                             <span class="badge badge-pill badge-danger">Verifikator</span><br>
                                             <small><i>Pengajuan tidak disetujui</i></small>
                                             @elseif($item->status == 13)
                                             <span class="badge badge-pill badge-primary">Verifikator</span>
                                             @elseif($item->status == 14)
                                             <span class="badge badge-pill badge-warning">PPK</span>
                                             @elseif($item->status == 21)
                                             <span class="badge badge-pill badge-primary">Pokja Pemilihan</span>
                                             @elseif($item->status == 22)
                                             <span class="badge badge-pill badge-primary">Kepala UKPBJ</span>
                                             @elseif($item->status == 31)
                                             <span class="badge badge-pill badge-primary">Pokja Pemilihan</span>
                                             @elseif($item->status == 32)
                                             <span class="badge badge-pill badge-primary">Pokja Pemilihan</span>
                                             @elseif($item->status == 33)
                                             <span class="badge badge-pill badge-primary">Pokja Pemilihan</span>
                                             @elseif($item->status == 34)
                                             <span class="badge badge-pill badge-warning">PPK</span>
                                             @else
                                             <span class="badge badge-pill badge-primary">Error</span>
                                             @endif
                                         </td>

                                        <td>
                                            <a href="{{ route(auth()->user()->role.'_pengajuanopen',$item->id) }}" class="btn btn-outline-primary btn-sm">
                                                 Open
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </div>

    </div><!-- container -->


</div> <!-- Page content Wrapper -->

<!-- Modal Edit File Pengajuan -->
<div class="modal fade" id="editFileModal" tabindex="-1" role="dialog" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form-edit-file" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFileModalLabel">Edit File Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="file_id" name="file_id">
                    <div class="form-group">
                        <label for="nama_file">Nama File</label>
                        <input type="text" class="form-control" id="nama_file" name="nama_file" readonly>
                    </div>

                    {{-- <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea class="form-control" id="pesan" name="pesan" readonly></textarea>
                    </div> --}}
                    {{-- @endif
                    @if(auth()->user()->role=='ppk') --}}
                    <div class="form-group">
                        <label for="file_upload">Ganti File</label>
                        <input type="file" class="form-control" id="file_upload" name="file_upload" required>
                    </div>
                    {{-- @endif --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal Disposisi Pokja -->
<div class="modal fade" id="disposisiModal" tabindex="-1" role="dialog" aria-labelledby="disposisiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="form-disposisi-pokja">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="disposisiModalLabel">
            <i class="fa fa-users"></i> Pilih Pokja Pemilihan (Maksimal 3)
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div id="pokja-alert" class="alert alert-danger d-none"></div>
          <div class="mb-2">
              <input type="text" id="search-pokja" class="form-control" placeholder="Cari Pokja (Nama, NIP, Jabatan)...">
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
              <thead class="thead-light">
                <tr>
                  <th style="width:40px"></th>
                  <th>NIP</th>
                  <th>Nama Pokja</th>
                  <th>Jabatan</th>
                </tr>
              </thead>
              <tbody id="pokja-list">
                <tr>
                  <td colspan="4">Memuat data...</td>
                </tr>
              </tbody>
            </table>
            <div id="pokja-pagination"></div>
          </div>
          <div class="text-right mt-2">
            <small class="text-muted">Terpilih: <span id="selected-count">0</span> / 3</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Kirim ke Pokja</button>
        </div>
      </div>
    </form>
  </div>
</div>






@stop

@section('javascript')

@if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')
<script>
    let idData = [];
    let Otable;    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        var data_id = '{{ $data->id }}';
        var url = "{{ route(auth()->user()->role.'_pengajuan_files_approval', $data->id) }}";
        let dStatus = $(this).attr('data-status');
        let dketerangan = $('#keterangan').val();
        alertify.confirm('Data pengajuan ini akan diupdate, Apa anda yakin ?', function() {
            $.ajax({
                data: {
                    id: idData
                    , status: dStatus
                    , keterangan: dketerangan
                , }, //function yang dipakai agar value pada form-control seperti input, textarea, select dll dapat digunakan pada URL query string ketika melakukan ajax request
                url: url, //url simpan data
                type: "POST", //karena simpan kita pakai method POST
                dataType: 'json'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function() { //jika berhasil
                    // console.log(data);
                    var oTable = $('#datatable2').dataTable(); //inialisasi datatable
                    $('#keterangan').val('');
                    oTable.fnDraw(false); //reset datatable
                    $('#checkAll').html('');
                    $('#checkAll').html(`<input type="checkbox" class="checkall" >`);
                    idData = [];
                    $(".update-status").prop('disabled', true);
                    $('#lengthcek').html(' (' + idData.length + ')');
                    alertify.success('Data Berhasil Diperbaharui !!');

                    @if(auth()->user()->role=='pokjapemilihan')
                    // Update status tombol setelah approval
                    setTimeout(function() {
                        checkCanMarkComplete();
                    }, 500);
                    @endif
                }
                , error: function(data) { //jika error tampilkan error pada console
                    console.log('Error:', data);
                    $('#tombol-simpan').html('Simpan');
                }
            });
        }, function() {
            alertify.error('Cancel')
        });
    });

</script>

<script src="{{ asset('js/checked.js') }}"></script>
@endif

<script>
      $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    // DataTable untuk File Pengajuan (datatable2) dengan AJAX
    $('#datatable2').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: "{{ route(auth()->user()->role.'_pengajuan_files', $data->id) }}",
        columns: [
            @if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')

            {
                data: 'checkedd',
                name: 'checkedd'
            },
            @endif
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_file', name: 'nama_file' },
            @if(auth()->user()->role=='verifikator')
            { data: 'verifikator_status', name: 'verifikator_status' },
            @elseif(auth()->user()->role=='pokjapemilihan')
            { data: 'statusnew', name: 'statusnew'},
            @else
            { data: 'statusnew', name: 'statusnew' },


            @endif

            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        createdRow: function(row, data, dataIndex) {
        },

    });

    // DataTable untuk Riwayat Pengajuan (datatable1) tetap bisa pakai AJAX juga jika ingin
    $('#datatable1').DataTable();


    @if(auth()->user()->role=='ppk')

    // Edit File Pengajuan
    $('body').on('click', '.edit-post', function() {
        var file_id = $(this).data('id');
        var url = "{{ route('ppk_pengajuan_open_edit', ['id' => ':id']) }}".replace(':id', file_id);


        $.get(url, function(data) {
            $('#file_id').val(data.id);
            $('#nama_file').val(data.nama_file);
            $('#status').val(data.status);
            $('#pesan').val(data.pesan);

            $('#editFileModal').modal('show');
        });
    });

    // Submit form edit file
    $('#form-edit-file').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var file_id = $('#file_id').val();
        // alert(file_id);
        var url = "{{ route('ppk_pengajuan_open_update', ['id' => ':id']) }}".replace(':id', file_id);

        // Disable tombol simpan agar tidak bisa klik dua kali
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editFileModal').modal('hide');
                $('#datatable2').DataTable().ajax.reload();
                alertify.success('File pengajuan berhasil diperbarui.');

            },
            error: function(xhr) {
                var errorMessage = 'Terjadi kesalahan, silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            }
        });
    });
    @endif

    // Tolak Pengajuan
    @if(auth()->user()->role=='verifikator' || auth()->user()->role=='kepalaukpbj')
     $('#tolakPengajuan').on('click', function(e) {
        e.preventDefault();
        alertify.confirm(
            '<textarea id="pesan_akhir" class="form-control" rows="3" placeholder="Tulis alasan penolakan..." style="resize:vertical"></textarea>',
            function() {
                var pesan = $('#pesan_akhir').val();
                if (!pesan) {
                    alertify.error('Pesan penolakan wajib diisi!');
                    return false;
                }
                $.post("{{ route(auth()->user()->role.'_pengajuan_tolak', $data->id) }}", {
                    _token: '{{ csrf_token() }}',
                    pesan_akhir: pesan
                }, function(res) {
                    alertify.success(res.pesan);
                    location.reload();
                }).fail(function() {
                    alertify.error('Gagal menolak pengajuan.');
                });
            },
            function() {
                alertify.error('Aksi dibatalkan');
            }
        ).set('labels', {ok:'Tolak', cancel:'Batal'});
    });
    @endif

    window.routeGetPokja = "{{ route('kepalaukpbj_getPokja') }}";
    window.routeKirimPokja = "{{ route('kepalaukpbj_kirimPokja') }}";
    window.pengajuanId = "{{ $data->id }}";
    window.csrfToken = "{{ csrf_token() }}";


</script>

<script src="{{ asset('js/disposisipokja.js') }}"></script>
<script>
@if(auth()->user()->role=='pokjapemilihan')
    // Fungsi untuk mengecek apakah tombol dapat diklik
    function checkCanMarkComplete() {
        $.ajax({
            url: "{{ route('pokjapemilihan_check_can_mark_complete', $data->id) }}",
            type: 'GET',
            success: function(response) {
                console.log('Response from server:', response);

                const button = $('#tandai-selesai-reviu');

                if (response.can_complete) {
                    // Button dapat diklik
                    button.removeClass('disabled btn-success')
                          .removeAttr('aria-disabled')
                          .attr('tabindex', '0')
                          .prop('disabled', false)
                          .addClass('btn-primary')
                          .text(response.button_text);
                } else {
                    // Button tidak dapat diklik
                    if (response.button_text === 'Reviu Telah Selesai') {
                        button.addClass('disabled btn-success')
                              .removeClass('btn-primary')
                              .attr('aria-disabled', 'true')
                              .attr('tabindex', '-1')
                              .prop('disabled', true)
                              .text(response.button_text);
                    } else {
                        button.addClass('disabled btn-primary')
                              .removeClass('btn-success')
                              .attr('aria-disabled', 'true')
                              .attr('tabindex', '-1')
                              .prop('disabled', true)
                              .text(response.button_text);
                    }
                }
            },
            error: function(xhr) {
                console.error('Error checking button status:', xhr);
            }
        });
    }

    // Panggil fungsi saat DataTable selesai load
    $('#datatable2').on('xhr.dt', function (e, settings, json, xhr) {
        // Panggil fungsi untuk mengecek status tombol
        checkCanMarkComplete();
    });

    // Panggil fungsi saat halaman pertama kali load
    $(document).ready(function() {
        checkCanMarkComplete();
    });
@endif
    $('#tandai-selesai-reviu').on('click', function(e) {
        if ($(this).hasClass('disabled')) {
            e.preventDefault();
            return false;
        }

        e.preventDefault();

        // Konfirmasi sebelum menandai selesai reviu
        alertify.confirm(
            'Apakah Anda yakin ingin menandai reviu sebagai selesai?<br><small class="text-muted">Setelah dikonfirmasi, Anda tidak dapat mengubah status file lagi.</small>',
            function() {
                // Disable tombol untuk mencegah double click
                $('#tandai-selesai-reviu').addClass('disabled').prop('disabled', true).text('Memproses...');

                $.ajax({
                    url: "{{ route('pokjapemilihan_selesai_reviu', $data->id) }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alertify.success(response.message);

                            // Reload DataTable
                            $('#datatable2').DataTable().ajax.reload();

                            // Jika semua pokja sudah selesai, reload halaman
                            if (response.all_pokja_selesai) {
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                // Tetap disable tombol karena pokja ini sudah selesai
                                $('#tandai-selesai-reviu').text('Reviu Telah Selesai').removeClass('btn-primary').addClass('btn-success');
                            }
                        } else {
                            alertify.error(response.message || 'Terjadi kesalahan');
                            // Re-enable tombol jika ada error
                            $('#tandai-selesai-reviu').removeClass('disabled').prop('disabled', false).text('Tandai Selesai Reviu');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan, silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alertify.error(errorMessage);

                        // Re-enable tombol jika ada error
                        $('#tandai-selesai-reviu').removeClass('disabled').prop('disabled', false).text('Tandai Selesai Reviu');
                    }
                });
            },
            function() {
                alertify.error('Aksi dibatalkan');
            }        ).set('labels', {ok:'Ya, Selesai', cancel:'Batal'});
    });


</script>

@stop
