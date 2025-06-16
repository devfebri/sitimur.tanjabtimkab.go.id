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
                    <h4 class="page-title text-center">Formulir Pengajuan Tender</h4>

                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-12">
                <div class="card m-b-30">

                    <div class="card-body">
                        <form action="{{ route('ppk_kirim_pengajuan') }}" method="POST" enctype="multipart/form-data">
                            @method('POST')
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Kode RUP</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="kode_rup" id="kode_rup">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Nama Paket</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="nama_paket" id="nama_paket">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Perangkat Daerah</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="perangkat_daerah" id="perangkat_daerah">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Rekening Kegiatan</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="rekening_kegiatan" id="rekening_kegiatan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Sumber Dana</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="sumber_dana" id="sumber_dana">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Pagu Anggaran</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="pagu_anggaran" id="pagu_anggaran">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Pagu HPS</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="pagu_hps" id="pagu_hps">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Jenis Pengadaan</label>
                                <div class="col-sm-8">
                                    <select name="jenis_pengadaan" id="jenis_pengadaan" class="form-control">
                                        <option value="">-Pilih-</option>
                                        <option value="Pengadaan Barang">Pengadaan Barang</option>
                                        <option value="Pekerjaan Konstruksi">Pekerjaan Konstruksi</option>
                                        <option value="Jasa Konsultasi">Jasa Konsultasi</option>
                                        <option value="Jasa Lainnya">Jasa Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Metode Pengadaan</label>
                                <div class="col-sm-8">
                                    <select name="metode_pengadaan" id="metode_pengadaan" class="form-control">
                                        <option value="">-Pilih-</option>
                                        <option value="Seleksi">Seleksi</option>
                                        <option value="Tender">Tender</option>
                                    </select>
                                </div>
                            </div>

                            <div id="metode_seleksi" style="display: none;">
                                <h5 class="text-center">Dokumen Metode Seleksi</h5>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Surat Permohonan</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="surat_permohonan" id="surat_permohonan">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">KAK</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="kak" id="kak">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Bill Of Quantity (BoQ)</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="boq" id="boq">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">SK aanwijzer</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="sk_aanwijzer" id="sk_aanwijzer">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">RKA/DPA/Paket Pekerjaan & Screenshoot RUP</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="rka_dpa_rup" id="rka_dpa_rup">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Screenshoot Pembuatan Paket di LPSE</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="ss_paket_lpse" id="ss_paket_lpse">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Rancangan Umum Kontrak, SSKK dan SSUK</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="rancangan_kontrak" id="rancangan_kontrak">
                                    </div>
                                </div>


                            </div>
                            <div id="metode_tender" style="display: none;">

                                <h5 class="text-center">Dokumen Metode Tender</h5>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Surat Permohonan</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="surat_permohonan" id="surat_permohonan">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Spesifikasi Teknis</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="kak" id="kak">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Bill Of Quantity (BoQ)</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="boq" id="boq">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">SK aanwijzer</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="sk_aanwijzer" id="sk_aanwijzer">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">RKA/DPA/Paket Pekerjaan & Screenshoot RUP</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="rka_dpa_rup" id="rka_dpa_rup">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Screenshoot Pembuatan Paket di LPSE</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="ss_paket_lpse" id="ss_paket_lpse">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Rancangan Umum Kontrak, SSKK dan SSUK</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="rancangan_kontrak" id="rancangan_kontrak">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">RK3</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="rk3" id="rk3">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Gambar</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="gambar" id="gambar">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">TKDN</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="tkdn" id="tkdn">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label">Berita Acara Review Inspektorat (Jika Merupakan Paket Strategis)</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" type="file" name="berita_acara_inspektorat" id="berita_acara_inspektorat">
                                    </div>
                                </div>


                            </div>
                            <button type="submit" class="btn btn-primary float-right">Simpan</button>
                        </form>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->
</div> <!-- Page content Wrapper -->

@endsection

@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        // Tampilkan dokumen sesuai metode dan disable input file yang tidak aktif
        $('#metode_pengadaan').change(function() {
            var metode = $(this).val();
            if (metode == 'Seleksi') {
                $('#metode_seleksi').show();
                $('#metode_tender').hide();
                $('#metode_seleksi input[type="file"]').prop('disabled', false);
                $('#metode_tender input[type="file"]').prop('disabled', true);
            } else if (metode == 'Tender') {
                $('#metode_tender').show();
                $('#metode_seleksi').hide();
                $('#metode_tender input[type="file"]').prop('disabled', false);
                $('#metode_seleksi input[type="file"]').prop('disabled', true);
            } else {
                $('#metode_seleksi').hide();
                $('#metode_tender').hide();
                $('#metode_seleksi input[type="file"]').prop('disabled', true);
                $('#metode_tender input[type="file"]').prop('disabled', true);
            }
        }).trigger('change'); // trigger agar kondisi awal sesuai

        // Validasi form
        $("form").validate({
            ignore: []
            , rules: {
                kode_rup: {
                    required: true
                }
                , nama_paket: {
                    required: true
                }
                , perangkat_daerah: {
                    required: true
                }
                , rekening_kegiatan: {
                    required: true
                }
                , sumber_dana: {
                    required: true
                }
                , pagu_anggaran: {
                    required: true
                }
                , pagu_hps: {
                    required: true
                }
                , jenis_pengadaan: {
                    required: true
                }
                , metode_pengadaan: {
                    required: true
                }
                , surat_permohonan: {
                    required: true
                }
                , kak: {
                    required: true
                }
                , boq: {
                    required: true
                }
                , sk_aanwijzer: {
                    required: true
                }
                , rka_dpa_rup: {
                    required: true
                }
                , ss_paket_lpse: {
                    required: true
                }
                , rancangan_kontrak: {
                    required: true
                }
                , rk3: {
                    required: function() {
                        return $('#metode_pengadaan').val() == 'Tender';
                    }
                }
                , gambar: {
                    required: function() {
                        return $('#metode_pengadaan').val() == 'Tender';
                    }
                }
                , tkdn: {
                    required: function() {
                        return $('#metode_pengadaan').val() == 'Tender';
                    }
                }
                , berita_acara_inspektorat: {
                    required: function() {
                        return $('#metode_pengadaan').val() == 'Tender';
                    }
                }
            }
            , messages: {
                kode_rup: "Kode RUP wajib diisi"
                , nama_paket: "Nama Paket wajib diisi"
                , perangkat_daerah: "Perangkat Daerah wajib diisi"
                , rekening_kegiatan: "Rekening Kegiatan wajib diisi"
                , sumber_dana: "Sumber Dana wajib diisi"
                , pagu_anggaran: "Pagu Anggaran wajib diisi"
                , pagu_hps: "Pagu HPS wajib diisi"
                , jenis_pengadaan: "Jenis Pengadaan wajib dipilih"
                , metode_pengadaan: "Metode Pengadaan wajib dipilih"
                , surat_permohonan: "Surat Permohonan wajib diupload"
                , kak: "KAK/Spesifikasi Teknis wajib diupload"
                , boq: "BoQ wajib diupload"
                , sk_aanwijzer: "SK aanwijzer wajib diupload"
                , rka_dpa_rup: "RKA/DPA/RUP wajib diupload"
                , ss_paket_lpse: "Screenshoot Paket LPSE wajib diupload"
                , rancangan_kontrak: "Rancangan Kontrak wajib diupload"
                , rk3: "RK3 wajib diupload"
                , gambar: "Gambar wajib diupload"
                , tkdn: "TKDN wajib diupload"
                , berita_acara_inspektorat: "Berita Acara Inspektorat wajib diupload"
            }
            , errorElement: 'small'
            , errorClass: 'text-danger'
            , highlight: function(element) {
                $(element).addClass('is-invalid');
            }
            , unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
            , submitHandler: function(form) {
                form.submit(); // submit form jika valid
            }
        });
    });

</script>
@endsection
