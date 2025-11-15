<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('dashboard.laporan');
    }

    public function getLaporan(Request $request)
    {
        $type = $request->input('type', 'harian'); // harian, bulanan, tahunan
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $query = Pengajuan::with(['user', 'metodePengadaan']);

        switch ($type) {
            case 'harian':
                $carbonDate = Carbon::parse($date);
                $query->whereDate('created_at', $carbonDate->format('Y-m-d'));
                $title = 'Laporan Harian - ' . $carbonDate->format('d F Y');
                break;

            case 'bulanan':
                $carbonDate = Carbon::parse($date);
                $query->whereYear('created_at', $carbonDate->year)
                      ->whereMonth('created_at', $carbonDate->month);
                $title = 'Laporan Bulanan - ' . $carbonDate->format('F Y');
                break;

            case 'tahunan':
                $carbonDate = Carbon::parse($date);
                $query->whereYear('created_at', $carbonDate->year);
                $title = 'Laporan Tahunan - ' . $carbonDate->year;
                break;

            default:
                $query->whereDate('created_at', now()->format('Y-m-d'));
                $title = 'Laporan Harian - ' . now()->format('d F Y');
        }

        $pengajuans = $query->orderBy('created_at', 'desc')->get();

        // Statistik
        $stats = [
            'total' => $pengajuans->count(),
            'menunggu_verifikator' => $pengajuans->where('status', 0)->count(),
            'menunggu_kepala' => $pengajuans->whereIn('status', [11])->count(),
            'menunggu_pokja' => $pengajuans->whereIn('status', [21])->count(),
            'ditolak' => $pengajuans->whereIn('status', [12, 22, 32])->count(),
            'selesai' => $pengajuans->where('status', 31)->count(),
            'total_pagu' => $pengajuans->sum('pagu_anggaran'),
        ];

        return response()->json([
            'success' => true,
            'title' => $title,
            'data' => $pengajuans,
            'stats' => $stats
        ]);
    }

    public function exportPdf(Request $request)
    {
        $type = $request->input('type', 'harian');
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $query = Pengajuan::with(['user', 'metodePengadaan']);

        switch ($type) {
            case 'harian':
                $carbonDate = Carbon::parse($date);
                $query->whereDate('created_at', $carbonDate->format('Y-m-d'));
                $title = 'Laporan Harian - ' . $carbonDate->format('d F Y');
                break;

            case 'bulanan':
                $carbonDate = Carbon::parse($date);
                $query->whereYear('created_at', $carbonDate->year)
                      ->whereMonth('created_at', $carbonDate->month);
                $title = 'Laporan Bulanan - ' . $carbonDate->format('F Y');
                break;

            case 'tahunan':
                $carbonDate = Carbon::parse($date);
                $query->whereYear('created_at', $carbonDate->year);
                $title = 'Laporan Tahunan - ' . $carbonDate->year;
                break;
        }

        $pengajuans = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'total' => $pengajuans->count(),
            'menunggu_verifikator' => $pengajuans->where('status', 0)->count(),
            'menunggu_kepala' => $pengajuans->whereIn('status', [11])->count(),
            'menunggu_pokja' => $pengajuans->whereIn('status', [21])->count(),
            'ditolak' => $pengajuans->whereIn('status', [12, 22, 32])->count(),
            'selesai' => $pengajuans->where('status', 31)->count(),
            'total_pagu' => $pengajuans->sum('pagu_anggaran'),
        ];

        $pdf = \PDF::loadView('dashboard.laporan_pdf', compact('pengajuans', 'stats', 'title', 'type'));
        
        $filename = 'laporan_' . $type . '_' . str_replace('-', '', $date) . '.pdf';
        
        return $pdf->download($filename);
    }

    private function getStatusText($status)
    {
        $statuses = [
            0 => 'Menunggu Verifikator',
            11 => 'Menunggu Kepala UKPBJ',
            12 => 'Tidak Disetujui Verifikator',
            13 => 'Menunggu Verifikasi Ulang',
            14 => 'File dikembalikan ke PPK',
            21 => 'Menunggu Reviu Pokja',
            22 => 'Tidak Disetujui Kepala UKPBJ',
            31 => 'Siap Ditayangkan',
            32 => 'Tidak Disetujui Pokja Pemilihan',
            33 => 'Menunggu Verifikasi Ulang',
            34 => 'File dikembalikan ke PPK',
            88 => 'System stops - No updates for 3 days'
        ];
        return $statuses[$status] ?? 'Unknown Status';
    }
}
