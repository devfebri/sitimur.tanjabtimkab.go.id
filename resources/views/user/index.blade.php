@extends('layouts.master')
@section('css')
<!-- Dropzone css -->

<link href="{{ asset('template/assets/plugins/timepicker/tempusdominus-bootstrap-4.css') }}" rel="stylesheet" />
<link href="{{ asset('template/assets/plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/clockpicker/jquery-clockpicker.min.css') }}" rel="stylesheet" />
<link href="{{ asset('template/assets/plugins/colorpicker/asColorPicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('template/assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('template/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('template/assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
@endsection

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
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Data User
                            @if(auth()->user()->role=='admin')
                            <button type="button" class="btn btn-primary mb-2  float-right btn-sm" id="tombol-tambah">
                                Tambah Data
                            </button>
                            @endif
                            {{-- <a href="/admin/importuser" class="btn btn-primary mb-2 mr-2 float-right btn-sm" >
                                Import User
                            </a> --}}
                        </h4>
                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">
                                <table id="datatable1" class="table table-striped table-bordered table-hover table-sm text-center" style="font-size: 13px" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>

                                            <th>No</th>
                                            <th>Username</th>
                                            <th>Name</th>
                                            <th>Hak Akses</th>
                                            <th>No HP</th>
                                            <th>Jabatan</th>
                                            <th>Pangkat</th>
                                            <th>Jenis Kelamin</th>
                                            <th>NIP</th>
                                            <th>NIK</th>
                                            <th>Status</th>
                                            @if(auth()->user()->role=='admin')
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div><!-- container -->


</div> <!-- Page content Wrapper -->

<!-- Modal -->
<div class="modal fade" id="tambah-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>


            </div>

            <form class="needs-validation" id="form-tambah-edit" name="form-tambah-edit">
                <input type="hidden" name="id" id="id">
                <div class="card-body">
                    <div class="form-group row">
                        <label for="username" class="col-sm-4 col-form-label">Username <small class="text-center" style="color:red;">*</small></label>

                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="username" id="username"   required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">Nama Lengkap <small class="text-center" style="color:red;">*</small></label>

                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="name" id="name" required>
                        </div>
                    </div>
                    


                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Hak Akses <small class="text-center" style="color:red;">*</small></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="role" id="role" required>
                                <option value="">-pilih-</option>
                                <option value="admin">admin</option>
                                <option value="ppk">PPK</option>
                                <option value="verifikator">Verfikator</option>
                                <option value="kepalaukpbj">Kepala UKPBJ</option>
                                <option value="pokjapemilihan">Pokja Pemilihan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">Password <small class="text-center" style="color:red;display:none;" id="pwwjb">*</small></label>

                        <div class="col-sm-8">
                            <input class="form-control" type="password" name="password" id="password"  >
                            {{-- <small class="text-danger"><i>default = "password"</i></small> --}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label">Password Confirmation <small class="text-center" style="color:red;display:none;" id="pwcfwjb">*</small></label>


                        <div class="col-sm-8">
                            <input class="form-control" type="password" name="password_confirmation" id="password_confirmation"  >
                            {{-- <small class="text-danger"><i>default = "password"</i></small> --}}


                        </div>
                    </div>



                    <div class="form-group row" style="display: none;" id="formstatus">
                        <label class="col-sm-4 col-form-label">Status Akun <small class="text-center" style="color:red;" >*</small></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="statuss" id="statuss" style="width: 100%; height:36px;" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="tombol-simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>




@stop

@section('javascript')

<script src="{{ asset('js/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('template/assets/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route(auth()->user()->role.'_user') }}",
            scrollX: true,
            columns: [
                {
                    data: null,
                    sortable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1
                    },
                },
                { data: 'username', name: 'username' },
                { data: 'name', name: 'name' },
                { data: 'role', name: 'role' },
                { data: 'nohp', name: 'nohp' },
                { data: 'jabatan', name: 'jabatan' },
                { data: 'pangkat', name: 'pangkat' },
                { data: 'jk', name: 'jk' },
                { data: 'nip', name: 'nip' },
                { data: 'nik', name: 'nik' },
                { data: 'status', name: 'status' },
                @if(auth()->user()->role == 'admin')
                { data: 'action', name: 'action' },
                @endif
            ]
        });

        $('#unit_id').select2();
        $('#pegawai_pin').select2();

        // Tombol tambah data
        $('#tombol-tambah').click(function() {
            $('#id').val('');
            $('#form-tambah-edit').trigger("reset");
            $('#modal-judul').html("Tambah Pegawai");
            $('#tambah-edit-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#pwwjb').show();
            $('#pwcfwjb').show();
            $('#password_confirmation').attr('required', true);
            $('#password').attr('required', true);
            $('#username').attr('disabled', false);
        });

        // Validasi form
        if ($("#form-tambah-edit").length > 0) {
            $("#form-tambah-edit").validate({
                rules: {
                    username: {
                        required: true,
                        remote: {
                            url: "{{ route('usercheckUsername') }}",

                            type: "post",
                            data: {
                                username: function() { return $("#username").val(); },
                                id: function() { return $("#id").val(); },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    },
                    name: { required: true },
                    role: { required: true },
                    password: {
                        required: function() {
                            return $('#id').val() === '';
                        },
                        minlength: 6
                    },
                    password_confirmation: {
                        equalTo: "#password"
                    }
                },
                messages: {
                    username: {
                        required: "Username wajib diisi",
                        remote: "Username sudah digunakan"
                    },
                    name: "Nama wajib diisi",
                    role: "Role wajib dipilih",
                    password: {
                        required: "Password wajib diisi",
                        minlength: "Password minimal 6 karakter"
                    },
                    password_confirmation: {
                        equalTo: "Konfirmasi password tidak sama"
                    }
                },
                submitHandler: function(form) {
                    var actionType = $('#tombol-simpan').val();
                    $('#tombol-simpan').html('Sending..');
                    $.ajax({
                        data: $('#form-tambah-edit').serialize(),
                        url: "{{ route(auth()->user()->role.'_usercreate') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function(data) {
                            $('#form-tambah-edit').trigger("reset");
                            $('#tambah-edit-modal').modal('hide');
                            $('#tombol-simpan').html('Simpan');
                            table.ajax.reload(null, false);
                        },
                        error: function(data) {
                            $('#tombol-simpan').html('Simpan');
                        }
                    });
                }
            });
        }

        // Hapus data
        $('body').on('click', '.delete', function() {
            var dataid = $(this).attr('data-id');
            var url = "{{ route(auth()->user()->role.'_userdelete', ':dataid') }}";
            url = url.replace(':dataid', dataid);

            alertify.confirm('Seluruh data yang berkaitan di user ini akan ikut terhapus, apa anda yakin ?', function() {
                $.ajax({
                    url: url,
                    type: 'delete',
                    success: function(data) {
                        setTimeout(function() {
                            table.ajax.reload(null, false);
                            $('#tombol-hapus').text('Yakin');
                        });
                        alertify.success('Data berhasil dihapus');
                    }
                });
            }, function() {
                alertify.error('Cancel');
            });
        });

        // Edit data
        $('body').on('click', '.edit-post', function() {
            var data_id = $(this).data('id');
            $.get('user/' + data_id + '/edit', function(data) {
                $('#modal-judul').html("Edit User");
                $('#tombol-simpan').val("edit-post");
                $('#tambah-edit-modal').modal('show');
                $('#id').val(data.id);
                $('#username').val(data.username).attr('disabled', true);
                $('#name').val(data.name);
                $('#role').val(data.role).change();
                $('#hak').val(data.sisa_cuti);
                $('#gapok').val(data.gapok);
                $('#pegawai_pin').val(data.pegawai_pin).change();
                $('#formstatus').show();
                $('#unit_id').val(data.unit_new_id).change();
                $('#statuss').val(data.akses).change();
                $('#hak_cuti_lainnya').val(data.hak_cuti_lainnya).change();
                $('#pwwjb').hide();
                $('#pwcfwjb').hide();
                $('#password_confirmation').attr('required', false);
                $('#password').attr('required', false);
            });
        });

    });
</script>
@endsection

