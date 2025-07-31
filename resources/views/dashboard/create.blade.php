@extends('layouts.master')

@section('content')

 <div id="loading_dokumen" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center; padding-top:200px;">

     <div class="spinner-border text-primary" role="status">
         <span class="sr-only">Loading...</span>
     </div>
     <p class="mt-2">Mohon tunggu...</p>
 </div>


<div class="container py-4">

    <div class="text-center mb-4">
        <h3 class="font-weight-bold">Formulir Pengajuan Tender</h3>
        <p class="text-muted">Silakan lengkapi data berikut dengan benar</p>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form id="form_pengajuan" action="{{ route('ppk_simpan_step2') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin mengirim pengajuan ini? Pastikan semua data sudah benar.');">


                @csrf

                <!-- Step 1 -->
                <div class="step step-1">
                    <h5 class="mb-3">Informasi Paket</h5>
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label>Kode RUP</label>
                            <input type="text" class="form-control" name="kode_rup" id="kode_rup" value="{{ $pengajuan->kode_rup ?? '' }}" required>
                            <input type="hidden" class="form-control" name="id" id="id" value="{{ $pengajuan->id ?? '' }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Nama Paket</label>
                            <input type="text" class="form-control" name="nama_paket" id="nama_paket" value="{{ $pengajuan->nama_paket ?? '' }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Perangkat Daerah</label>
                            <input type="text" class="form-control" name="perangkat_daerah" id="perangkat_daerah" value="{{ $pengajuan->perangkat_daerah ?? '' }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Rekening Kegiatan</label>
                            <input type="text" class="form-control" name="rekening_kegiatan" id="rekening_kegiatan" value="{{ $pengajuan->rekening_kegiatan ?? '' }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Sumber Dana</label>
                            <input type="text" class="form-control" name="sumber_dana" id="sumber_dana" value="{{ $pengajuan->sumber_dana ?? '' }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Pagu Anggaran</label>
                            <input type="text" class="form-control rupiah" name="pagu_anggaran" id="pagu_anggaran" value="{{ optional($pengajuan)->pagu_anggaran ? 'Rp ' . number_format(optional($pengajuan)->pagu_anggaran, 0, ',', '.') : '' }}" required>



                        </div>

                        <div class="form-group col-md-6">
                            <label>Pagu HPS</label>
                            <input type="text" class="form-control rupiah" name="pagu_hps" id="pagu_hps" value="{{ optional($pengajuan)->pagu_hps ? 'Rp ' . number_format(optional($pengajuan)->pagu_hps, 0, ',', '.') : '' }}" required>



                        </div>

                    </div>



                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Jenis Pengadaan</label>
                            <select name="jenis_pengadaan" id="jenis_pengadaan" class="form-control" required>
                                <option value="">-Pilih-</option>
                                <option value="Pengadaan Barang" @selected(($pengajuan->jenis_pengadaan ?? '') == 'Pengadaan Barang')>Pengadaan Barang</option>
                                <option value="Pekerjaan Konstruksi" @selected(($pengajuan->jenis_pengadaan ?? '') == 'Pekerjaan Konstruksi')>Pekerjaan Konstruksi</option>
                                <option value="Jasa Konsultasi" @selected(($pengajuan->jenis_pengadaan ?? '') == 'Jasa Konsultasi')>Jasa Konsultasi</option>
                                <option value="Jasa Lainnya" @selected(($pengajuan->jenis_pengadaan ?? '') == 'Jasa Lainnya')>Jasa Lainnya</option>


                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Metode Pengadaan</label>
                            <select name="metode_pengadaan_id" id="metode_pengadaan_id" class="form-control">
                                <option value="">-Pilih-</option>
                                 @foreach ($metodepengadaan as $id => $mp)
                                 <option value="{{ $id }}" @selected(($pengajuan->metode_pengadaan_id ?? '') == $id)>{{ $mp }}</option>
                                 @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary next-step float-right">Lanjut ke Upload Dokumen</button>


                </div>

                <!-- Step 2 -->
                <div class="step step-2" style="display:none;">
                    <h5 class="mb-3">Upload Dokumen</h5>

                    <div id="dokumen_berkas">
                    </div>
                    

                    <button type="button" class="btn btn-secondary prev-step">Kembali</button>
                    <button type="submit" class="btn btn-success float-right">Ajukan Paket</button>
                </div>

            </form>


        </div>
    </div>

</div>
@endsection

@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="{{ asset('template/assets/plugins/select2/select2.min.js') }}" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('.rupiah').on('keyup', function() {
            let value = $(this).val().replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            $(this).val(rupiah ? 'Rp ' + rupiah : '');
        });
    // Ketika tombol "Lanjut Step 2" diklik

    

        $('.next-step').click(function() {
            var valid = true;

            $('.step-1').find('input, select').each(function() {
                if ($(this).attr('name') === 'id') return;
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    valid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!valid) {
                alert('Lengkapi semua data terlebih dahulu.');
                return;
            }

            var data = {
                id: $('#id').val(),
                kode_rup: $('#kode_rup').val(),
                nama_paket: $('#nama_paket').val(),
                perangkat_daerah: $('#perangkat_daerah').val(),
                rekening_kegiatan: $('#rekening_kegiatan').val(),
                sumber_dana: $('#sumber_dana').val(),
                pagu_anggaran: $('#pagu_anggaran').val(),
                pagu_hps: $('#pagu_hps').val(),
                jenis_pengadaan: $('#jenis_pengadaan').val(),
                metode_pengadaan_id: $('#metode_pengadaan_id').val(),
                _token: '{{ csrf_token() }}'
            };

            $.post('{{ route("ppk_simpan_step1") }}', data, function(res) {
                if (res.success) {
                    // alert(res.id);
                    $('#id').val(res.id);

                    // Sembunyikan Step 1
                    $('.step-1').hide();

                    // Tampilkan loading_dokumen + kosongkan dokumen_berkas
                    $('#dokumen_berkas').hide().empty();
                    $('#loading_dokumen').show();

                    // Setelah AJAX selesai baru tampilkan Step 2
                    if (res.id) {
                        var url1 = "{{ route(auth()->user()->role.'_metode_pengadaan_berkas',':id') }}";
                         url1 = url1.replace(':id', res.id);
                         url1 += '?metode=' + res.metode;


                        

                        $.ajax({
                            url: url1,
                            type: 'GET',
                            dataType: 'json',
                            success: function(res) {
                                $('#dokumen_berkas').empty(); // kosongkan + hilangkan loader
                                $.each(res.data, function(i, berkas) {
                                    var multiple = berkas.multiple == 1 ? 'multiple' : '';
                                    var html = `
                                        <div class="mb-3 row align-items-center">
                                            <label for="${berkas.slug}" class="col-sm-4 col-form-label">
                                                ${berkas.nama_berkas} <span class="text-danger">*</span>
                                            </label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <input type="file" class="form-control" name="${berkas.slug}${multiple ? '[]' : ''}" id="${berkas.slug}" ${multiple ? 'multiple' : '' } >
                                                    <button id="btnUpload" data-id="${berkas.id}" class="btn btn-primary btn-sm">
                                                        Upload
                                                    </button>
                                                </div>`;
                                                if(berkas.pengajuan_files_id){
                                                    html += `
                                                    <small class="text-success">${berkas.nama_berkas} sudah di upload : <i><a href="{{ asset('${berkas.file_path}') }}" target="_blank" >download</a></i></small>

                                                    `;
                                                }
                                                html+=`
                                            </div>
                                        </div>
                                    `;
                                    $('#dokumen_berkas').append(html);
                                });
                                
                                

                                // Setelah semua siap, tampilkan dokumen & Step 2, sembunyikan loading
                                $('#loading_dokumen').hide();
                                $('#dokumen_berkas').show();
                                $('.step-2').show();
                            },
                            error: function() {
                                $('#loading_dokumen').hide();
                                // fileInput.after('<small class="text-danger">Gagal memuat dokumen, silakan coba lagi.</small>');


                                $('.step-2').show();
                            }
                        });
                    } else {
                        $('#loading_dokumen').hide();
                        $('.step-2').show();
                    }
                } else {
                    alert('Gagal simpan data');
                }
            });

            

        });



        $('.prev-step').click(function(){
            $('.step-2').hide();
            $('.step-1').show();
            
        });

        // Event upload file satu per satu
       $(document).on('click', '#btnUpload', function(e) {
            e.preventDefault();

            var btn = $(this);
            var inputGroup = btn.closest('.input-group');
            var input = inputGroup.find('input[type="file"]');
            var files = input[0].files;
            var berkas_id = btn.data('id');
          
            

            var pengajuan_id = $('#id').val();

            // Hapus notifikasi sebelumnya
            inputGroup.next('.upload-feedback').remove();

            if (!pengajuan_id) {
                alert('Silakan isi data Step 1 terlebih dahulu!');
                return;
            }
            if (!files.length) {
                inputGroup.after('<small class="text-danger upload-feedback">Pilih file terlebih dahulu.</small>');
                return;
            }

            var formData = new FormData();
            formData.append('file', files[0]);
            formData.append('berkas_id', berkas_id);
            formData.append('pengajuan_id', pengajuan_id);
            formData.append('_token', '{{ csrf_token() }}');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading...');

            $.ajax({
                url: '{{ route("ppk_upload_berkas_ajax") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.success) {
                        inputGroup.after('<small class="text-success upload-feedback">✅ Upload berhasil.</small>');
                        input.val('');
                         cekKelengkapanUpload();

                    } else {
                        inputGroup.after('<small class="text-danger upload-feedback">❌ ' + (res.message || 'Upload gagal.') + '</small>');
                    }
                },
                error: function(xhr) {
                    let msg = '❌ Terjadi kesalahan saat upload.';
                    if (xhr.status === 409 && xhr.responseJSON?.message) {
                        msg = '❌ ' + xhr.responseJSON.message;
                    }
                    inputGroup.after('<small class="text-danger upload-feedback">' + msg + '</small>');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Upload');
                     cekKelengkapanUpload();

                }
            });
        });



        // Disable tombol submit Kirim Pengajuan di awal
        $('.step-2 button[type="submit"]').prop('disabled', true);

        // Fungsi cek kelengkapan upload
        function cekKelengkapanUpload() {
            var pengajuan_id = $('#id').val();
            var metode_pengadaan_id = $('#metode_pengadaan_id').val();
            if (!pengajuan_id || !metode_pengadaan_id) return;

            $.post('{{ route("ppk_cek_upload_berkas") }}', {
                pengajuan_id: pengajuan_id,
                metode_pengadaan_id: metode_pengadaan_id,
                _token: '{{ csrf_token() }}'
            }, function(res) {
                if (res.complete) {
                    $('.step-2 button[type="submit"]').prop('disabled', false);
                } else {
                    $('.step-2 button[type="submit"]').prop('disabled', true);
                }
            });
        }

        // Cek saat Step 2 tampil
        $('.next-step').on('click', function() {
            cekKelengkapanUpload();
        });

        // Cek saat file diupload
        // $(document).on('change', 'input[type="file"]', function() {
        //     cekKelengkapanUpload();
        // });



    });
</script>

@endsection
