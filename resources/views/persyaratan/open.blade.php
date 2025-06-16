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
                    {{-- title --}}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Metode Pengadaan {{ $data->nama_metode_pengadaan }}</h4>
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
                                            <th>Berkas Upload</th>
                                            <th>Multiple Upload</th>
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
                        <label for="nama_berkas" class="col-sm-4 col-form-label">Nama Berkas <small class="text-center" style="color:red;">*</small></label>
                        <div class="col-sm-8">
                            <input class="form-control" type="text" name="nama_berkas" id="nama_berkas" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="multiple">Multiple Upload <small class="text-center" style="color:red;">*</small></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="multiple" id="multiple" required>
                                <option value="">-pilih-</option>
                                <option value="1">Active</option>
                                <option value="0" selected>Inactive</option>

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label" for="statuss">Status <small class="text-center" style="color:red;">*</small></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="statuss" id="statuss" required>
                                <option value="">-pilih-</option>
                                <option value="1" selected>Active</option>
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

    var metode_pengadaan_id = "{{ $data->id }}";

    var table = $('#datatable1').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin_persyaratanopen', $data->id) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_berkas', name: 'nama_berkas' },
            { data: 'multiple', name: 'multiple'},
            { data: 'status', name: 'status'},
            @if(auth()->user()->role == 'admin')
            { data: 'action', name: 'action', orderable: false, searchable: false },
            @endif
        ]
    });

    // Tombol tambah data
    $('#tombol-tambah').click(function() {
        $('#id').val('');
        $('#form-tambah-edit').trigger("reset");
        $('#modal-judul').html("Tambah Berkas");
        $('#tambah-edit-modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    // Validasi & submit form (tambah/edit)
    if ($("#form-tambah-edit").length > 0) {
        $("#form-tambah-edit").validate({
            rules: {
                nama_berkas: { required: true },
                multiple: { required: true },
                statuss: { required: true }
            },
            messages: {
                nama_berkas: "Nama berkas wajib diisi",
                multiple: "Multiple wajib dipilih",
                statuss: "Status wajib dipilih"
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function(form) {
                var id = $('#id').val();
                var url = "{{ route('admin_persyaratan_berkas_store') }}";
                var data = $(form).serialize() + '&metode_pengadaan_id=' + metode_pengadaan_id;
                $('#tombol-simpan').html('Menyimpan...');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        $('#form-tambah-edit').trigger("reset");
                        $('#tambah-edit-modal').modal('hide');
                        $('#tombol-simpan').html('Simpan');
                        table.ajax.reload(null, false);
                    },
                    error: function(data) {
                        $('#tombol-simpan').html('Simpan');
                        alert('Gagal simpan data!');
                    }
                });
                return false;
            }
        });
    }

    // Edit data
    $('body').on('click', '.edit-post', function() {
        var data_id = $(this).data('id');
        var url = "{{ route('admin_persyaratan_berkas_edit', ':data_id') }}";
        url = url.replace(':data_id', data_id);

        $.get(url, function(data) {
            $('#id').val(data.id);
            $('#nama_berkas').val(data.nama_berkas);
            $('#multiple').val(data.multiple);
            $('#statuss').val(data.status);
            $('#modal-judul').html("Edit Berkas");
            $('#tambah-edit-modal').modal('show');
        });
    });

    // Hapus data
    $('body').on('click', '.delete-post', function() {
        var data_id = $(this).data('id');
        var url = "{{ route('admin_persyaratan_berkas_delete', ':data_id') }}";
        url = url.replace(':data_id', data_id);
        if (confirm('Yakin ingin menghapus data ini?')) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    alert('Gagal hapus data!');
                }
            });
        }
    });
});
</script>
@endsection

