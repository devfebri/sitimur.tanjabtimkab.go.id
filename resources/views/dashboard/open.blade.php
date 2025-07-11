@extends('layouts.master')

@section('content')


<div class="page-content-wrapper ">

    <div class="container-fluid">


        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <a href="javascript:history.back()" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
        <!-- end page title end breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="card m-b-20">
                    <div class="row text-center" style="font-size: 12px;">
                        <div class="col-lg-4 col-sm-12" @if($data->verifikator_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->verifikator_status==2) style="background-color: rgb(255, 59, 59);" @elseif($data->verifikator_status==3) style="background-color: yellow;" @endif>
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
                                @elseif($data->verifikator_status==3)
                                    {{ $data->verifikator->name }} <br>
                                    <span class="badge badge-pill badge-primary"><b><i>Dikembalikan</i></b></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4  col-sm-12" @if($data->kepalaukpbj_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->kepalaukpbj_status==2) style="background-color: rgb(255, 59, 59);" @elseif($data->kepalaukpbj_status==3) style="background-color: yellow;" @endif>

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
                                @elseif($data->kepalaukpbj_status==3)
                                {{ $data->kepalaukpbj->name }} <br>
                                <span class="badge badge-pill badge-primary"><b><i>Dikembalikan</i></b></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12" @if($data->pokjapemilihan_status==1) style="background-color: rgb(89, 255, 89);" @elseif($data->pokjapemilihan_status==2) style="background-color: rgb(255, 59, 59);" @elseif($data->pokjapemilihan_status==3) style="background-color: yellow;" @endif>


                            <div class="card-body" >
                                  <b>Pokja Pemilihan</b> <br>
                                  @if($data->pokjapemilihan_status==0)
                                  <span class="badge badge-pill badge-primary"><b><i>Diajukan</i></b></span>
                                  @elseif($data->pokjapemilihan_status==1)
                                  {{ $data->pokjapemilihan->name }} <br>
                                  <span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>
                                  @elseif($data->pokjapemilihan_status==2)
                                  {{ $data->pokjapemilihan->name }} <br>
                                  <span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>
                                  @elseif($data->pokjapemilihan_status==3)
                                  {{ $data->pokjapemilihan->name }} <br>
                                  <span class="badge badge-pill badge-primary"><b><i>Dikembalikan</i></b></span>

                                  @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @if(auth()->user()->role=='kepalaukpbj' && $data->kepalaukpbj_status==0 && $data->verifikator_status==1)
            <div class="col-12 m-b-20 text-center">
                <button id="disposisiPokja" href="#" class="btn btn-success btn-lg">Disposisi ke Pokja Pemilihan</button>
                <button id="tolakPengajuan" href="#" class="btn  btn-lg btn-danger">Tolak Pengajuan</button>
            </div>
            @endif
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
                                    @if($data->status==0)
                                    <span class="badge badge-pill badge-primary"><b><i>Proses</i></b></span>
                                    @elseif($data->status==1)
                                    <span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>
                                    @elseif($data->status==2)
                                    <span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>
                                    @elseif($data->status==3)
                                    <span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>
                                    @endif
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
                        <h4 class="mt-0 header-title"> File Pengajuan
                            @if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')
                            <button type="submit" class="btn btn-danger mb-2 ml-2  float-right btn-sm update-status col-12 col-lg-2" disabled data-status="2" id="blm-lngkp">
                                Tidak Disetujui
                            </button>
                            <button type="submit" class="btn btn-warning mb-2 ml-2  float-right btn-sm update-status col-12 col-lg-2" disabled data-status="3" id="dikembalikan">
                                Dikembalikan
                            </button>


                            <button type="submit" class="btn btn-success mb-2 ml-2  float-right btn-sm update-status col-12 col-lg-2" disabled data-status="1" id="sdh-lngkp">

                                Disetujui
                            </button>
                            
                            <textarea name="keterangan" id="keterangan" cols="30" rows="1" class="float-right col-sm-12 col-lg-3 form-control" placeholder="Keterangan"></textarea>
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
                                            <th>Status Verifikator</th>
                                            <th>Status Pokja Pemilihan</th>
                                            <th>Status</th>
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
                   
                    <div class="form-group">
                        <label for="pesan">Pesan</label>
                        <textarea class="form-control" id="pesan" name="pesan" readonly></textarea>
                    </div>
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

@stop

@section('javascript')

@if(auth()->user()->role=='verifikator' || auth()->user()->role=='pokjapemilihan')
<script>
    let idData = [];
    let Otable;

    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        var data_id = '{{ $data->id }}';
        var url = "{{ route(auth()->user()->role.'_pengajuan_files_approval',':data_id') }}";
        url = url.replace(':data_id', data_id);
        let dStatus = $(this).attr('data-status');
        let dketerangan = $('#keterangan').val();
        console.log(dketerangan);
        alertify.confirm('Data pengajuan ini akan diupdate, Apa anda yakin ?', function() {
            // console.log(idData);
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
                    oTable.fnDraw(false); //reset datatable
                    $('#checkAll').html('');
                    $('#checkAll').html(`<input type="checkbox" class="checkall" >`);
                    idData = [];
                    $(".update-status").prop('disabled', true);
                    $('#lengthcek').html(' (' + idData.length + ')');
                    alertify.success('Data Berhasil Diperbaharui !!');

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
            { data: 'status_verifikator', name: 'status_verifikator' },
            { data: 'status_pokjapemilihan', name: 'status_pokjapemilihan' },
            { data: 'statuss', name: 'statuss'},
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
    @endif

    // Disposisi ke Pokja Pemilihan
    $('#disposisiPokja').on('click', function(e) {
        e.preventDefault();
        $.post("{{ route('kepalaukpbj_pengajuan_disposisi', $data->id) }}", {_token: '{{ csrf_token() }}'}, function(res) {
            alert(res.pesan);
            location.reload();
        });
    });

    // Tolak Pengajuan
    $('#tolakPengajuan').on('click', function(e) {
        e.preventDefault();
        $.post("{{ route('kepalaukpbj_pengajuan_tolak', $data->id) }}", {_token: '{{ csrf_token() }}'}, function(res) {
            alert(res.pesan);
            location.reload();
        });
    });
</script>
@stop
