@extends('layouts.master')

@section('content')


<div class="page-content-wrapper ">

    <div class="container-fluid">


        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">

                    {{-- <div class="btn-group float-right">
                        <ol class="breadcrumb hide-phone p-0 m-0">
                            <li class="breadcrumb-item"><a href="#">Annex</a></li>
                            <li class="breadcrumb-item active">Sart</li>
                        </ol>
                    </div> --}}
                    {{-- <h4 class="page-title">Satyalancana Karya Satya</h4> --}}
                    <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
                   

                </div>
            </div>
        </div>
        <!-- end page title end breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="row text-center" style="font-size: 12px;">
                        <div class="col-lg-4 col-sm-12" @if($data->verifikator_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->verifikator_status==2) style="background-color: rgb(255, 59, 59);" @endif>


                            <div class="card-body" >
                                <b>Verifikator</b>  <br>
                                @if($data->verifikator_status==0)
                                    <span class="badge badge-pill badge-primary"><b><i>Diajukan</i></b></span>
                                @elseif($data->verifikator_status==1)
                                    {{ $data->verifikator->name }} <br>
                                    <span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>
                                @elseif($data->verifikator_status==2)
                                    {{ $data->verifikator->name }} <br>
                                    <span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4  col-sm-12" @if($data->kepalaukpbj_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->kepalaukpbj_status==2) style="background-color: rgb(255, 59, 59);" @endif>
                            <div class="card-body" >
                                <b>Kepala UKPBJ</b> <br>
                                @if($data->kepalaukpbj_status==0)
                                <span class="badge badge-pill badge-primary"><b><i>Diajukan</i></b></span>
                                @elseif($data->kepalaukpbj_status==1)
                                {{ $data->kepalaukpbj->name }} <br>
                                <span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>
                                @elseif($data->kepalaukpbj_status==2)
                                {{ $data->kepalaukpbj->name }} <br>
                                <span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12" @if($data->pokjapemilihan_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->pokjapemilihan_status==2) style="background-color: rgb(255, 59, 59);" @endif>
                            <div class="card-body" >
                                  <b>Pokja Pemilihan</b> <br>
                                  @if($data->pokjapemilihan_status==0)
                                  <span class="badge badge-pill badge-primary"><b><i>Diajukan</i></b></span>
                                  @elseif($data->pokjapemilihan_status==1)
                                  {{ $data->pokjapemilihan->name }} <br>
                                  <span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>
                                  @elseif($data->pokjapemilihan_status==2)
                                  {{ $data->pokjapemilihan->name }} <br>
                                  @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7 col-sm-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Detail Pengajuan </h4>
                        <table class="table table-bordered table-striped">
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
                                <td>{{ $data->pagu_anggaran }}</td>
                            </tr>
                            <tr>
                                <td width="35%">Pagu HPS</td>
                                <td width="3%">:</td>
                                <td>{{ $data->pagu_hps }}</td>
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
                            <tr>
                                <td width="20%">Status</td>
                                <td width="3%">:</td>
                                <td>
                                    <span class="badge badge-pill badge-primary"><b><i>Diajukan</i></b></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
            <div class="col-lg-5 col-sm-12">
                <div class="card m-b-20">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Informasi Pegawai</h4>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td width="35%">Nama</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Jenis Kelamin</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->jk }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Telpon</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->nohp }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">NIP</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->nip }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">NIK</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->nik }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Jabatan</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->jabatan }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Pangkat</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->jabatan }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Email</td>
                                        <td width="3%">:</td>
                                        <td>{{ $data->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td width="35%">Status User</td>
                                        <td width="3%">:</td>
                                        <td>
                                            @if($data->user->akses == '1')
                                            <span class="badge badge-pill badge-primary"><b><i>Aktif</i></b></span>
                                            @else
                                            <span class="badge badge-pill badge-danger"><b><i>Tidak Aktif</i></b></span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
             
            </div> <!-- end col -->
        </div> <!-- end row -->
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                        <h4 class="mt-0 header-title"> File Pengajuan
                        </h4>
                                <table id="datatable2" class="table table-striped table-bordered table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama File</th>
                                            <th>Status</th>
                                            <th>Pesan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                        <h4 class="mt-0 header-title">Riwayat Pengajuan
                        </h4>
                                <table id="datatable1" class="table table-striped table-bordered table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead>
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
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>


                    </div>
                </div>

            </div>
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
                    {{-- @if(auth()->user()->role=='verifikator') --}}
                    <div class="form-group">
                        <label for="statuss">Status File</label>
                        <select class="form-control" id="statuss" name="statuss">
                            <option value="0">Diproses</option>
                            <option value="1">Disetujui</option>
                            <option value="2">Tidak Disetujui</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea class="form-control" id="pesan" name="pesan"></textarea>
                    </div>
                    {{-- @endif
                    @if(auth()->user()->role=='ppk') --}}
                    <div class="form-group">
                        <label for="file_upload">Ganti File (opsional)</label>
                        <input type="file" class="form-control" id="file_upload" name="file_upload">
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

@stop

@section('javascript')
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
        ajax: "{{ route('ppk_pengajuan_files', $data->id) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_file', name: 'nama_file' },
            { data: 'status', name: 'status', render: function(data) {
                return data == 'belum diperiksa'
                    ? '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>'
                    : data == 'diproses'
                    ? '<span class="badge badge-pill badge-warning"><b><i>Diproses</i></b></span>'
                    : data == 'disetujui'
                    ? '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>'
                    : data == 'tidak disetujui'
                    ? '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>'
                    : '<span class="badge badge-pill badge-secondary"><b><i>Status Tidak Dikenal</i></b></span>';
            }},
            { data: 'pesan', name: 'pesan', orderable: false, searchable: false,render:function(data){
                return data==null ? '<b><i>Null</i></b>' : data;
            } },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        createdRow: function(row, data, dataIndex) {
        },
        drawCallback: function(settings) {
            // Proses penomoran nama_file yang sama
            var nameCount = {};
            $('#datatable2 tbody tr').each(function() {
                var $td = $(this).find('td').eq(1); // kolom nama_file
                var originalName = $td.text().trim();
                if (!nameCount[originalName]) {
                    nameCount[originalName] = 1;
                    $td.text(originalName); // nama pertama tetap
                } else {
                    nameCount[originalName]++;
                    // Pisahkan nama dan ekstensi
                    var dotIdx = originalName.lastIndexOf('.');
                    var nameOnly = dotIdx !== -1 ? originalName.substring(0, dotIdx) : originalName;
                    var ext = dotIdx !== -1 ? originalName.substring(dotIdx) : '';
                    $td.text(nameOnly + '_' + nameCount[originalName] + ext);
                }
            });
        }
    });

    // DataTable untuk Riwayat Pengajuan (datatable1) tetap bisa pakai AJAX juga jika ingin
    $('#datatable1').DataTable();

   

// Edit File Pengajuan
$('body').on('click', '.edit-post', function() {
    var file_id = $(this).data('id');
    var url = "{{ route('ppk_pengajuan_open_edit', ':id') }}".replace(':id', file_id);


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
    var url = "{{ route('ppk_pengajuan_open_update', ':id') }}".replace(':id', file_id);;

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#editFileModal').modal('hide');
            $('#datatable2').DataTable().ajax.reload();
            alert('File pengajuan berhasil diperbarui.');
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
</script>
@stop
