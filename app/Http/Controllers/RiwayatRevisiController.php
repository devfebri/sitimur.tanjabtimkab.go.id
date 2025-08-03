<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanFile;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatRevisiController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanFile::with(['pengajuan.user', 'pengajuan.metodePengadaan'])
            ->where('revisi_ke', '>', 0) // Hanya file yang direvisi
            ->where('status', '!=', 99); // Tidak termasuk file yang dihapus

        // Filter berdasarkan role user
        if (Auth::user()->role == 'ppk') {
            // PPK hanya melihat revisi file pengajuannya sendiri
            $query->whereHas('pengajuan', function($q) {
                $q->where('user_id', Auth::user()->id);
            });
        } elseif (Auth::user()->role == 'pokjapemilihan') {
            // Pokja hanya melihat revisi file pengajuan yang dia reviu
            $query->whereHas('pengajuan', function($q) {
                $userId = Auth::user()->id;
                $q->where(function($subQ) use ($userId) {
                    $subQ->where('pokja1_id', $userId)
                         ->orWhere('pokja2_id', $userId)
                         ->orWhere('pokja3_id', $userId);
                });
            });
        } elseif (Auth::user()->role == 'verifikator') {
            // Verifikator melihat semua revisi file yang sudah diverifikasi
            $query->where('verifikator_status', '>', 0);
        }
        // Admin dan role lain bisa melihat semua

        // Filter berdasarkan parameter
        if ($request->filter == 'recent') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($request->filter == 'mine') {
            if (Auth::user()->role == 'ppk') {
                $query->whereHas('pengajuan', function($q) {
                    $q->where('user_id', Auth::user()->id);
                });
            } elseif (Auth::user()->role == 'verifikator') {
                $query->where('verifikator_updated', '!=', null);
            } elseif (Auth::user()->role == 'pokjapemilihan') {
                $query->where(function($q) {
                    $q->where('pokja1_updated', '!=', null)
                      ->orWhere('pokja2_updated', '!=', null)
                      ->orWhere('pokja3_updated', '!=', null);
                });
            }
        }

        $riwayatRevisi = $query->orderBy('created_at', 'desc')->paginate(15);

        // Tambahkan data tambahan untuk setiap item
        $riwayatRevisi->getCollection()->transform(function ($item) {
            // Tentukan jenis revisi dan user yang merevisi
            $jenisRevisi = 'Revisi File';
            $user = null;
            $status = 'Aktif';
            $keterangan = null;

            // Cari user yang melakukan revisi berdasarkan timestamp update terbaru
            $latestUpdate = null;
            $latestField = null;

            if ($item->verifikator_updated) {
                $latestUpdate = $item->verifikator_updated;
                $latestField = 'verifikator';
            }

            if ($item->pokja1_updated && (!$latestUpdate || $item->pokja1_updated > $latestUpdate)) {
                $latestUpdate = $item->pokja1_updated;
                $latestField = 'pokja1';
            }

            if ($item->pokja2_updated && (!$latestUpdate || $item->pokja2_updated > $latestUpdate)) {
                $latestUpdate = $item->pokja2_updated;
                $latestField = 'pokja2';
            }

            if ($item->pokja3_updated && (!$latestUpdate || $item->pokja3_updated > $latestUpdate)) {
                $latestUpdate = $item->pokja3_updated;
                $latestField = 'pokja3';
            }

            // Set user dan keterangan berdasarkan field yang terakhir update
            if ($latestField == 'verifikator') {
                $user = User::find($item->pengajuan->verifikator_id);
                $jenisRevisi = 'Revisi Verifikator';
                $keterangan = $item->verifikator_pesan;
                $status = $item->verifikator_status == 1 ? 'Disetujui' : 
                         ($item->verifikator_status == 3 ? 'Perlu Perbaikan' : 'Proses');
            } elseif (in_array($latestField, ['pokja1', 'pokja2', 'pokja3'])) {
                $pokjaField = substr($latestField, -1); // Ambil angka dari pokja1/2/3
                $user = User::find($item->pengajuan->{'pokja' . $pokjaField . '_id'});
                $jenisRevisi = 'Revisi Pokja ' . $pokjaField;
                $keterangan = $item->{$latestField . '_pesan'};
                $pokjaStatus = $item->{$latestField . '_status'};
                $status = $pokjaStatus == 1 ? 'Disetujui' : 
                         ($pokjaStatus == 3 ? 'Perlu Perbaikan' : 'Proses');
            }

            // Fallback jika tidak ada user yang ditemukan
            if (!$user) {
                $user = $item->pengajuan->user; // User pembuat pengajuan
                $jenisRevisi = 'Revisi File';
            }

            // Tambahkan properti tambahan
            $item->jenis_revisi = $jenisRevisi;
            $item->user = $user;
            $item->status = $status;
            $item->keterangan = $keterangan;

            return $item;
        });

        return view('dashboard.riwayatrevisi', compact('riwayatRevisi'));
    }

    public function downloadRevision($id)
    {
        $revisi = PengajuanFile::findOrFail($id);
        
        // Cek authorization
        if (Auth::user()->role == 'ppk' && $revisi->pengajuan->user_id != Auth::user()->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        $filePath = public_path($revisi->file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName = $revisi->nama_file . '_revisi_' . $revisi->revisi_ke . '.' . pathinfo($revisi->file_path, PATHINFO_EXTENSION);
        
        return response()->download($filePath, $fileName);
    }
}
