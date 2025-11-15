<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .stats {
            margin: 20px 0;
            display: table;
            width: 100%;
        }
        .stat-box {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 16.66%;
        }
        .stat-box .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-info { background-color: #17a2b8; color: white; }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PENGAJUAN TENDER</h2>
        <h3>{{ $title }}</h3>
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Total Pengajuan</div>
            <div class="value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Menunggu Verifikator</div>
            <div class="value">{{ $stats['menunggu_verifikator'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Menunggu Pokja</div>
            <div class="value">{{ $stats['menunggu_pokja'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Selesai</div>
            <div class="value">{{ $stats['selesai'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Ditolak</div>
            <div class="value">{{ $stats['ditolak'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Total Pagu</div>
            <div class="value" style="font-size: 10px;">Rp {{ number_format($stats['total_pagu'], 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Kode RUP</th>
                <th width="25%">Nama Paket</th>
                <th width="18%">Perangkat Daerah</th>
                <th width="13%">Pagu Anggaran</th>
                <th width="13%">Status</th>
                <th width="10%">PPK</th>
            </tr>
        </thead>
        <tbody>
            @if($pengajuans->count() > 0)
                @foreach($pengajuans as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->kode_rup }}</td>
                    <td>{{ $item->nama_paket }}</td>
                    <td>{{ $item->perangkat_daerah }}</td>
                    <td class="text-right">Rp {{ number_format($item->pagu_anggaran, 0, ',', '.') }}</td>
                    <td>
                        @if($item->status == 0)
                            <span class="badge badge-primary">Menunggu Verifikator</span>
                        @elseif($item->status == 11)
                            <span class="badge badge-info">Menunggu Kepala</span>
                        @elseif($item->status == 12)
                            <span class="badge badge-danger">Ditolak Verifikator</span>
                        @elseif($item->status == 14)
                            <span class="badge badge-warning">Dikembalikan</span>
                        @elseif($item->status == 21)
                            <span class="badge badge-info">Menunggu Pokja</span>
                        @elseif($item->status == 22)
                            <span class="badge badge-danger">Ditolak Kepala</span>
                        @elseif($item->status == 31)
                            <span class="badge badge-success">Siap Ditayangkan</span>
                        @elseif($item->status == 32)
                            <span class="badge badge-danger">Ditolak Pokja</span>
                        @elseif($item->status == 34)
                            <span class="badge badge-warning">Dikembalikan</span>
                        @else
                            <span class="badge">Status {{ $item->status }}</span>
                        @endif
                    </td>
                    <td>{{ $item->user ? $item->user->name : '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }} (Admin)</p>
        <p>SITIMUR - Sistem Informasi Tender Tanjung Jabung Timur</p>
    </div>
</body>
</html>
