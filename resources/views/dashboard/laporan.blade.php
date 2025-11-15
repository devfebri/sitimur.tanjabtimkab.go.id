@extends('layouts.master')

@section('css')
<link href="{{ asset('template/assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stat-card.primary { border-left-color: #5b73e8; }
    .stat-card.success { border-left-color: #1cbb8c; }
    .stat-card.warning { border-left-color: #fcb92c; }
    .stat-card.danger { border-left-color: #ff3d60; }
    .stat-card.info { border-left-color: #4fc6e1; }
</style>
@endsection

@section('content')
<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Laporan Pengajuan</h4>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mt-0 header-title mb-4">Filter Laporan</h5>
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Tipe Laporan</label>
                                        <select class="form-control" id="laporanType" name="type">
                                            <option value="harian">Harian</option>
                                            <option value="bulanan">Bulanan</option>
                                            <option value="tahunan">Tahunan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" id="datePickerWrapper">
                                    <div class="form-group">
                                        <label>Pilih Tanggal</label>
                                        <input type="text" class="form-control" id="datePicker" name="date" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fa fa-search"></i> Tampilkan Laporan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row" id="statsSection" style="display:none;">
            <div class="col-md-4">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Pengajuan</h6>
                                <h3 class="mb-0" id="stat-total">0</h3>
                            </div>
                            <div>
                                <i class="mdi mdi-file-document-box mdi-48px text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Menunggu Verifikator</h6>
                                <h3 class="mb-0" id="stat-verifikator">0</h3>
                            </div>
                            <div>
                                <i class="mdi mdi-clock-outline mdi-48px text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Selesai</h6>
                                <h3 class="mb-0" id="stat-selesai">0</h3>
                            </div>
                            <div>
                                <i class="mdi mdi-check-circle mdi-48px text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Menunggu Pokja</h6>
                                <h3 class="mb-0" id="stat-pokja">0</h3>
                            </div>
                            <div>
                                <i class="mdi mdi-account-group mdi-48px text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Ditolak</h6>
                                <h3 class="mb-0" id="stat-ditolak">0</h3>
                            </div>
                            <div>
                                <i class="mdi mdi-close-circle mdi-48px text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Pagu Anggaran</h6>
                                <h4 class="mb-0" id="stat-pagu">Rp 0</h4>
                            </div>
                            <div>
                                <i class="mdi mdi-currency-usd mdi-48px text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="row" id="tableSection" style="display:none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mt-0 header-title" id="tableTitle">Laporan Pengajuan</h5>
                            <button type="button" class="btn btn-danger btn-sm" id="exportPdfBtn">
                                <i class="fa fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="laporanTable" class="table table-striped table-bordered table-sm" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode RUP</th>
                                        <th>Nama Paket</th>
                                        <th>Perangkat Daerah</th>
                                        <th>Pagu Anggaran</th>
                                        <th>Status</th>
                                        <th>PPK</th>
                                    </tr>
                                </thead>
                                <tbody id="laporanTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('template/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script>
$(document).ready(function() {
    let currentType = 'harian';
    let currentDate = '{{ now()->format("Y-m-d") }}';

    // Initialize datepicker
    function initDatePicker(type) {
        $('#datePicker').datepicker('destroy');
        
        if (type === 'harian') {
            $('#datePicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            }).datepicker('setDate', new Date());
        } else if (type === 'bulanan') {
            $('#datePicker').datepicker({
                format: 'mm/yyyy',
                viewMode: 'months',
                minViewMode: 'months',
                autoclose: true
            }).datepicker('setDate', new Date());
        } else if (type === 'tahunan') {
            $('#datePicker').datepicker({
                format: 'yyyy',
                viewMode: 'years',
                minViewMode: 'years',
                autoclose: true
            }).datepicker('setDate', new Date());
        }
    }

    initDatePicker('harian');

    // Change type handler
    $('#laporanType').on('change', function() {
        currentType = $(this).val();
        initDatePicker(currentType);
    });

    // Date change handler
    $('#datePicker').on('changeDate', function(e) {
        const date = e.date;
        if (currentType === 'harian') {
            currentDate = date.getFullYear() + '-' + 
                         String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                         String(date.getDate()).padStart(2, '0');
        } else if (currentType === 'bulanan') {
            currentDate = date.getFullYear() + '-' + 
                         String(date.getMonth() + 1).padStart(2, '0') + '-01';
        } else {
            currentDate = date.getFullYear() + '-01-01';
        }
    });

    // Submit form
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadLaporan();
    });

    // Export PDF
    $('#exportPdfBtn').on('click', function() {
        window.location.href = '{{ route("admin_laporan_export_pdf") }}?type=' + currentType + '&date=' + currentDate;
    });

    function loadLaporan() {
        $.ajax({
            url: '{{ route("admin_laporan_data") }}',
            type: 'GET',
            data: {
                type: currentType,
                date: currentDate
            },
            beforeSend: function() {
                $('#statsSection').hide();
                $('#tableSection').hide();
            },
            success: function(response) {
                if (response.success) {
                    // Update stats
                    $('#stat-total').text(response.stats.total);
                    $('#stat-verifikator').text(response.stats.menunggu_verifikator);
                    $('#stat-selesai').text(response.stats.selesai);
                    $('#stat-pokja').text(response.stats.menunggu_pokja);
                    $('#stat-ditolak').text(response.stats.ditolak);
                    $('#stat-pagu').text('Rp ' + response.stats.total_pagu.toLocaleString('id-ID'));

                    // Update title
                    $('#tableTitle').text(response.title);

                    // Update table
                    let tbody = '';
                    if (response.data.length > 0) {
                        response.data.forEach((item, index) => {
                            tbody += '<tr>';
                            tbody += '<td>' + (index + 1) + '</td>';
                            tbody += '<td>' + new Date(item.created_at).toLocaleDateString('id-ID') + '</td>';
                            tbody += '<td>' + item.kode_rup + '</td>';
                            tbody += '<td>' + item.nama_paket + '</td>';
                            tbody += '<td>' + item.perangkat_daerah + '</td>';
                            tbody += '<td>Rp ' + parseInt(item.pagu_anggaran).toLocaleString('id-ID') + '</td>';
                            tbody += '<td>' + getStatusBadge(item.status) + '</td>';
                            tbody += '<td>' + (item.user ? item.user.name : '-') + '</td>';
                            tbody += '</tr>';
                        });
                    } else {
                        tbody = '<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>';
                    }
                    $('#laporanTableBody').html(tbody);

                    // Show sections
                    $('#statsSection').fadeIn();
                    $('#tableSection').fadeIn();
                }
            },
            error: function() {
                alertify.error('Gagal memuat data laporan');
            }
        });
    }

    function getStatusBadge(status) {
        const badges = {
            0: '<span class="badge badge-primary">Menunggu Verifikator</span>',
            11: '<span class="badge badge-info">Menunggu Kepala UKPBJ</span>',
            12: '<span class="badge badge-danger">Tidak Disetujui Verifikator</span>',
            14: '<span class="badge badge-warning">File Dikembalikan</span>',
            21: '<span class="badge badge-info">Menunggu Pokja</span>',
            22: '<span class="badge badge-danger">Tidak Disetujui Kepala</span>',
            31: '<span class="badge badge-success">Siap Ditayangkan</span>',
            32: '<span class="badge badge-danger">Tidak Disetujui Pokja</span>',
            34: '<span class="badge badge-warning">File Dikembalikan</span>',
            88: '<span class="badge badge-dark">System Stops</span>'
        };
        return badges[status] || '<span class="badge badge-secondary">Unknown</span>';
    }
});
</script>
@endsection
