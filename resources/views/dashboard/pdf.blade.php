<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ringkas Pengajuan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .content {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PAKET {{ $pengajuan->id }}</h2>
        {{-- <h3>Sistem Informasi Tender</h3> --}}
    </div>

    <div class="content">
        <table class="table">
            <tr>
                <th width="30%">Nomor Pengajuan</th>
                <td>{{ $pengajuan->id }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $pengajuan->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Nama Pengaju</th>
                <td>{{ $pengajuan->user->name }}</td>
            </tr>
            <tr>
                <th>OPD</th>
                <td>{{ $pengajuan->user->unit_kerja }}</td>
            </tr>
            <tr>
                <th>Metode Pengadaan</th>
                <td>{{ $pengajuan->metodePengadaan->nama_metode_pengadaan }}</td>
            </tr>
            <tr>
                <th>Nama Paket</th>
                <td>{{ $pengajuan->nama_paket }}</td>
            </tr>
            <tr>
                <th>Pagu Anggaran</th>
                <td>Rp {{ number_format($pengajuan->pagu_anggaran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>HPS</th>
                <td>Rp {{ number_format($pengajuan->pagu_hps, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $pengajuan->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <th>Tanggal Siap Tayang</th>
                <td>{{ $pengajuan->status == 31 ? $pengajuan->updated_at->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Status Pengajuan</th>
                <td>
                    @if($pengajuan->status == 0)
                        Menunggu Verifikasi
                    @elseif($pengajuan->status == 11)
                        Menunggu Persetujuan Kepala UKPBJ
                    @elseif($pengajuan->status == 21)
                        Dikirim ke Pokja Pemilihan
                    @elseif($pengajuan->status == 31)
                        Selesai Direviu
                    @else
                        Status Lainnya
                    @endif
                </td>
            </tr>
        </table>

        @if($pengajuan->pokja1_id || $pengajuan->pokja2_id || $pengajuan->pokja3_id)
        <h4>Tim Pokja Pemilihan</h4>
        <table class="table">
            <tr>
                <th>Nama Pokja</th>
                <th>Status Review</th>
            </tr>
            @if($pengajuan->pokja1_id)
            <tr>
                <td>{{ $pengajuan->pokja1->name }}</td>
                <td>
                    @if($pengajuan->pokja1_status_akhir == 0)
                        Belum Direviu
                    @elseif($pengajuan->pokja1_status_akhir == 1)
                        Sedang Direviu
                    @elseif($pengajuan->pokja1_status_akhir == 2)
                        Selesai Direviu
                    @endif
                </td>
            </tr>
            @endif
            @if($pengajuan->pokja2_id)
            <tr>
                <td>{{ $pengajuan->pokja2->name }}</td>
                <td>
                    @if($pengajuan->pokja2_status_akhir == 0)
                        Belum Direviu
                    @elseif($pengajuan->pokja2_status_akhir == 1)
                        Sedang Direviu
                    @elseif($pengajuan->pokja2_status_akhir == 2)
                        Selesai Direviu
                    @endif
                </td>
            </tr>
            @endif
            @if($pengajuan->pokja3_id)
            <tr>
                <td>{{ $pengajuan->pokja3->name }}</td>
                <td>
                    @if($pengajuan->pokja3_status_akhir == 0)
                        Belum Direviu
                    @elseif($pengajuan->pokja3_status_akhir == 1)
                        Sedang Direviu
                    @elseif($pengajuan->pokja3_status_akhir == 2)
                        Selesai Direviu
                    @endif
                </td>
            </tr>
            @endif
        </table>
        @endif

        
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
        <p>SITIMUR - Sistem Informasi Tender Terintegrasi Mandiri UKPBJ</p>
    </div>
</body>
</html>
