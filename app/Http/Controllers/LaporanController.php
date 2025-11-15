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
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $carbonStart = Carbon::parse($startDate);
        $carbonEnd = Carbon::parse($endDate);
        
        $query = Pengajuan::with(['user', 'metodePengadaan'])
            ->whereBetween('created_at', [
                $carbonStart->startOfDay(), 
                $carbonEnd->endOfDay()
            ]);

        $pengajuans = $query->orderBy('created_at', 'desc')->get();

        // Generate title
        if ($carbonStart->format('Y-m-d') === $carbonEnd->format('Y-m-d')) {
            $title = 'Laporan Tanggal ' . $carbonStart->format('d F Y');
        } else {
            $title = 'Laporan Periode ' . $carbonStart->format('d F Y') . ' s/d ' . $carbonEnd->format('d F Y');
        }

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
        $startDate = $request->input('start_date', now()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $carbonStart = Carbon::parse($startDate);
        $carbonEnd = Carbon::parse($endDate);
        
        $query = Pengajuan::with(['user', 'metodePengadaan'])
            ->whereBetween('created_at', [
                $carbonStart->startOfDay(), 
                $carbonEnd->endOfDay()
            ]);

        $pengajuans = $query->orderBy('created_at', 'desc')->get();

        // Generate title
        if ($carbonStart->format('Y-m-d') === $carbonEnd->format('Y-m-d')) {
            $title = 'Laporan Tanggal ' . $carbonStart->format('d F Y');
        } else {
            $title = 'Laporan Periode ' . $carbonStart->format('d F Y') . ' s/d ' . $carbonEnd->format('d F Y');
        }

        $stats = [
            'total' => $pengajuans->count(),
            'menunggu_verifikator' => $pengajuans->where('status', 0)->count(),
            'menunggu_kepala' => $pengajuans->whereIn('status', [11])->count(),
            'menunggu_pokja' => $pengajuans->whereIn('status', [21])->count(),
            'ditolak' => $pengajuans->whereIn('status', [12, 22, 32])->count(),
            'selesai' => $pengajuans->where('status', 31)->count(),
            'total_pagu' => $pengajuans->sum('pagu_anggaran'),
        ];

        $pdf = \PDF::loadView('dashboard.laporan_pdf', compact('pengajuans', 'stats', 'title'));
        
        $filename = 'laporan_' . str_replace('-', '', $startDate) . '_' . str_replace('-', '', $endDate) . '.pdf';
        
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
