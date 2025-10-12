<?php

namespace App\Http\Controllers;

use App\Models\MetodePengadaan;
use App\Models\MetodePengadaanBerkas;
use App\Models\Pengajuan;
use App\Models\PengajuanFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PengajuanController extends Controller
{
    // INI DATA INDEX
    public function index()
    {
        
        $metodepengadaan = MetodePengadaan::selectRaw('id,nama_metode_pengadaan')->where('status',1)->pluck('nama_metode_pengadaan','id');
        

        return view('dashboard.index',compact('metodepengadaan'));
    }
    public function getData(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'kode_rup',
            2 => 'nama_paket',
            3 => 'perangkat_daerah',
            4 => 'rekening_kegiatan',
            5 => 'sumber_dana',
            6 => 'pagu_anggaran',
            7 => 'pagu_hps',
            8 => 'jenis_pengadaan',
        ];

        if(Auth::user()->role=='ppk'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','status', 'verifikator_status_akhir', 'kepalaukpbj_status','verifikator_updated_akhir','kepalaukpbj_updated','created_at')
                ->where('user_id', Auth::user()->id)
                ->where('status', '!=', 9)
                ->orderBy('created_at','desc'); // Exclude status 9 (draft)
        } elseif (Auth::user()->role == 'verifikator'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','verifikator_status_akhir','verifikator_updated_akhir','kepalaukpbj_updated','created_at')
                 ->where('status', '!=', 9)
                ->orderBy('created_at', 'desc'); 
        } elseif (Auth::user()->role == 'kepalaukpbj') {
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status','verifikator_updated_akhir','kepalaukpbj_updated','created_at')
                ->where('verifikator_status_akhir', 1)
                ->where('status', '!=', 9)
                ->orderBy('created_at', 'desc');
        } elseif(Auth::user()->role == 'pokjapemilihan'){
            $userId = Auth::user()->id;
            
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status', 'verifikator_updated_akhir', 'kepalaukpbj_updated', 'created_at','status','pokja1_status_akhir', 'pokja2_status_akhir', 'pokja3_status_akhir', 'pokja1_id', 'pokja2_id', 'pokja3_id')
                ->where('verifikator_status_akhir', 1)
                ->where('status', '!=', 9)
                ->where(function($q) use ($userId) {
                    $q->where('pokja1_id', $userId)
                      ->orWhere('pokja2_id', $userId)
                      ->orWhere('pokja3_id', $userId);
                })
                ->orderBy('created_at', 'desc');
                
        } else{
            $query = \DB::table('pengajuans')
                ->select(
                    'id',
                    'kode_rup',
                    'nama_paket',
                    'perangkat_daerah',
                    'rekening_kegiatan',
                    'sumber_dana',
                    'pagu_anggaran',
                    'pagu_hps',
                    'jenis_pengadaan',
                    'status',
                    'verifikator_updated_akhir',
                    'kepalaukpbj_updated',
                    'pokja1_id',
                    'pokja2_id',
                    'pokja3_id',
                    'created_at'
                )
                // ->where(function ($q) {
                //     $q->where('status', 21)
                //         ->orWhere('status', 31);
                // })
                // ->where(function ($q) {
                //     $userId = auth()->user()->id;
                //     $q->where('pokja1_id', $userId)
                //         ->orWhere('pokja2_id', $userId)
                //         ->orWhere('pokja3_id', $userId);
                // })
                ->orderBy('created_at', 'desc');
        }
        
        

        // Search
        $search = $request->input('search.value');
        if ($search && strlen($search) >= 3) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_rup', 'like', "%{$search}%")
                    ->orWhere('nama_paket', 'like', "%{$search}%")
                    ->orWhere('perangkat_daerah', 'like', "%{$search}%")
                    ->orWhere('rekening_kegiatan', 'like', "%{$search}%")
                    ->orWhere('sumber_dana', 'like', "%{$search}%")
                    ->orWhere('pagu_anggaran', 'like', "%{$search}%")
                    ->orWhere('pagu_hps', 'like', "%{$search}%")
                    ->orWhere('jenis_pengadaan', 'like', "%{$search}%");
            });
        }

        // Total records sebelum filter
        $recordsTotal = $query->count();

        // Total records setelah filter (tanpa offset/limit)
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Ordering
        if ($request->input('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';
            $orderDir = $request->input('order.0.dir') ?? 'asc';
            $query->orderBy($orderColumn, $orderDir);
        }

        // Paging
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $query->offset($start)->limit($length)->get();

        // Format data untuk DataTables
        $result = [];
        $no = $start + 1;
        foreach ($data as $row) {

            // Tombol aksi
            $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
            if (Auth::user()->role == 'ppk' && $row->verifikator_status_akhir == 0 && $row->kepalaukpbj_status == 0) {
                $button .= '<button class="btn btn-danger btn-sm delete-post" data-id="' . $row->id . '">Hapus</button>';
            }

            // Status HTML untuk PPK
            if (Auth::user()->role == 'ppk') {
                if ($row->status == 0) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-primary"></i> Verifikator</b>
                        <br>&emsp; <i>Menunggu Verifikator</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda sedang menunggu proses verifikasi oleh verifikator.</small>';
                } elseif ($row->status == 11) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Kepala UKPBJ</b>
                        <br>&emsp; <i>Menunggu Kepala UKPBJ</i>
                        <br><small class="text-muted">&emsp;Pengajuan telah disetujui verifikator dan menunggu persetujuan Kepala UKPBJ.</small>';
                } elseif ($row->status == 12) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Verifikator</b>
                        <br>&emsp; <i>Tidak Disetujui Verifikator</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda tidak disetujui oleh verifikator. Silakan cek kembali dokumen Anda.</small>';
                } elseif ($row->status == 13) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> Verifikator</b>
                        <br>&emsp; <i>Menunggu Verifikasi Ulang</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda perlu diverifikasi ulang oleh verifikator.</small>';
                } elseif ($row->status == 14) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> PPK</b>
                        <br>&emsp; <small><i>File dikembalikan pada PPK</i></small>
                        <br><small class="text-muted">&emsp;Beberapa file perlu diperbaiki oleh PPK sebelum proses dilanjutkan.</small>';
                } elseif ($row->status == 21) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Pokja</b>
                        <br>&emsp; <i>Menunggu Reviu Pokja</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda sedang direviu oleh Pokja.</small>';
                } elseif ($row->status == 22) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Kepala UKPBJ</b>
                        <br>&emsp; <i>Tidak Disetujui Kepala UKPBJ</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda tidak disetujui oleh Kepala UKPBJ.</small>';
                } elseif ($row->status == 31) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-success"></i> Pokja</b>
                        <br>&emsp; <i>Selesai</i>
                        <br><small class="text-muted">&emsp;Proses pengajuan telah selesai. Terima kasih atas partisipasi Anda.</small>';
                } elseif ($row->status == 32) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Pokja</b>
                        <br>&emsp; <i>Tidak Disetujui Pokja Pemilihan</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda tidak disetujui oleh Pokja.</small>';
                } elseif ($row->status == 33) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> PPK</b>
                        <br>&emsp; <i>Menunggu Verifikasi Ulang</i>
                        <br><small class="text-muted">&emsp;Pengajuan Anda perlu diverifikasi ulang .</small>';
                } elseif ($row->status == 34) {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-warning"></i> PPK</b>
                        <br>&emsp; <small><i>File dikembalikan pada PPK</i></small>
                        <br><small class="text-muted">&emsp;Beberapa file perlu diperbaiki oleh PPK sebelum proses dilanjutkan.</small>';
                } else {
                    $status = '<b><i class="mdi mdi-checkbox-blank-circle text-danger"></i> Status Terakhir</b>
                        <br>&emsp; <i>Status Error</i>
                        <br><small class="text-muted">&emsp;Terjadi kesalahan status. Silakan hubungi admin.</small>';
                }
            } elseif(Auth::user()->role=="verifikator"){
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';

                if ($row->verifikator_status_akhir == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->verifikator_status_akhir == 1) {
                    $status = '<span class="badge badge-pill badge-success">Diteruskan Kepada Kepala UKPBJ</span>';
                } elseif ($row->verifikator_status_akhir == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->verifikator_status_akhir == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }
            } elseif(Auth::user()->role=='kepalaukpbj'){
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';


                if ($row->kepalaukpbj_status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->kepalaukpbj_status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->kepalaukpbj_status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->kepalaukpbj_status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }else{
                    $status = '<span class="badge badge-pill badge-warning">Errors</span>';
                }
            } elseif (Auth::user()->role == 'pokjapemilihan') {
                $pokjaKe = null;
                if ($row->pokja1_id == Auth::user()->id) {
                    $pokjaKe = 1;
                } elseif ($row->pokja2_id == Auth::user()->id) {
                    $pokjaKe = 2;
                } elseif ($row->pokja3_id == Auth::user()->id) {
                    $pokjaKe = 3;
                }
                // cek pokja ke berapa
                $statusAkhir = null;
                if ($pokjaKe) {
                    // $statuspokja = Pengajuan::where('id', $row->id)
                    //     ->where('pokja' . $pokjaKe . '_id', Auth::user()->id)
                    //     ->where('pokja' . $pokjaKe . '_status_akhir', 0)
                    //     ->first();
                    $statusAkhir = $row->{'pokja' . $pokjaKe . '_status_akhir'};
                }
                // dd($pokjaKe);
                if ($statusAkhir == 0) {
                    $button = '<button class="btn btn-primary btn-sm open-post" data-status="mulai"  data-id="' . $row->id . '">Mulai Reviu</button>';
                    $status = '<span class="badge badge-pill badge-primary">Belum Direviu</span>';
                } elseif ($statusAkhir == 1) {
                    $button = '<button class="btn btn-primary btn-sm open-post"  data-status="lanjut"  data-id="' . $row->id . '">Lanjut Reviu</button>';
                    $status = '<span class="badge badge-pill badge-warning">Sedang Direviu</span>';
                } elseif ($statusAkhir == 2) {
                    $button = '<button class="btn btn-primary btn-sm open-post"  data-status="lanjut"  data-id="' . $row->id . '">Open</button>';
                    $status = '<span class="badge badge-pill badge-success">Selesai Direviu</span>';
                }else {
                    $status = '<span class="badge badge-pill badge-danger">Errors</span>';
                }
            } else{
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if ($row->status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Verifikator</span>';
                } elseif ($row->status == 11) {
                    $status = '<span class="badge badge-pill badge-primary">Kepala UKPBJ</span>';
                } elseif ($row->status == 12) {
                    $status = '<span class="badge badge-pill badge-primary">Verifikator</span>';
                } elseif ($row->status == 13) {
                    $status = '<span class="badge badge-pill badge-primary">Verifikator</span>';
                } elseif ($row->status == 14) {
                    $status = '<span class="badge badge-pill badge-warning">PPK</span>';
                } elseif ($row->status == 21) {
                    $status = '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                } elseif ($row->status == 22) {

                    $status = '<span class="badge badge-pill badge-primary">Kepala UKPBJ</span>';
                } elseif ($row->status == 31) {

                    $status = '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                } elseif ($row->status == 32) {

                    $status = '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                } elseif ($row->status == 33) {

                    $status = '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                } elseif ($row->status == 34) {

                    $status = '<span class="badge badge-pill badge-warning">PPK</span>';
                }elseif ($row->status == 88) {

                    $status = '<span class="badge badge-pill badge-danger">PPK</span><br><small><i>System stops auto-submission, no updates for 3 days</i></small>';
                } else {

                    $status = '<span class="badge badge-pill badge-primary">Error</span>';
                }
            }
            
            $result[] = [
                'no' => $no++,
                'kode_rup' => $row->kode_rup,
                'nama_paket' => $row->nama_paket,
                'perangkat_daerah' => $row->perangkat_daerah,
                'rekening_kegiatan' => $row->rekening_kegiatan,
                'sumber_dana' => $row->sumber_dana,
                'pagu_anggaran' => 'Rp ' . number_format($row->pagu_anggaran, 0, ',', '.'),
                'pagu_hps' => 'Rp ' . number_format($row->pagu_hps, 0, ',', '.'),
                'jenis_pengadaan' => $row->jenis_pengadaan,
                'status' => $status,
                'action' => $button
                // Tambahkan kolom action jika perlu
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result
        ]);
    }

    public function sanitizeRupiah($value)
    {
        return (int) str_replace(['Rp ', '.', ','], '', $value);
    }
    public function simpanStep1(Request $request)
    {
        $validated = $request->validate([
            'kode_rup' => 'required|string|max:255',
            'nama_paket' => 'required|string|max:255',
            'perangkat_daerah' => 'required|string|max:255',
            'rekening_kegiatan' => 'required|string|max:255',
            'sumber_dana' => 'required|string|max:255',
            'pagu_anggaran' => 'required',
            'pagu_hps' => 'required',
            'jenis_pengadaan' => 'required|string|max:255',
            'metode_pengadaan_id' => 'required',
        ]);
        $paguAnggaran = $this->sanitizeRupiah($validated['pagu_anggaran']);
        $paguHps = $this->sanitizeRupiah($validated['pagu_hps']);

        if (Pengajuan::where('user_id', Auth::user()->id)->where('status', 9)->exists() && $request->id) {
            // edit Data

            $data = Pengajuan::find($request->id);
            $data->kode_rup = $request->kode_rup;
            $data->nama_paket = $request->nama_paket;
            $data->perangkat_daerah = $request->perangkat_daerah;
            $data->rekening_kegiatan = $request->rekening_kegiatan;
            $data->sumber_dana = $request->sumber_dana;
            $data->pagu_anggaran = $paguAnggaran;
            $data->pagu_hps = $paguHps;
            $data->jenis_pengadaan = $request->jenis_pengadaan;
            if ($data->metode_pengadaan_id != $request->metode_pengadaan_id) {
                // jika metode pengadaan berubah, hapus semua berkas yang ada
                $files = PengajuanFile::where('pengajuan_id', $data->id)->get();
                foreach ($files as $file) {

                    $filePath = public_path($file->file_path);
                    // dd(file_exists($filePath));
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
                PengajuanFile::where('pengajuan_id', $data->id)->delete();
            }
            $data->metode_pengadaan_id = $request->metode_pengadaan_id;

            $data->user_id = Auth::user()->id;
            $data->status = 9;
            $data->save();
        } else {

            // tambah data
            $data = new Pengajuan();
            $data->kode_rup = $request->kode_rup;
            $data->nama_paket = $request->nama_paket;
            $data->perangkat_daerah = $request->perangkat_daerah;
            $data->rekening_kegiatan = $request->rekening_kegiatan;
            $data->sumber_dana = $request->sumber_dana;
            $data->pagu_anggaran = $paguAnggaran;
            $data->pagu_hps = $paguHps;
            $data->jenis_pengadaan = $request->jenis_pengadaan;
            $data->metode_pengadaan_id = $request->metode_pengadaan_id;
            $data->user_id = Auth::user()->id;
            $data->status = 9;
            $data->save();
        }



        return response()->json(['success' => true, 'message' => 'Step 1 berhasil.', 'metode' => $request->metode_pengadaan_id, 'id' => $data->id]);
    }

    public function simpanStep2(Request $request)
    {
        $data = Pengajuan::findOrFail($request->id);
        $data->status = 0; // Update status to indicate step 2 is complete
        $data->created_at = Carbon::now(); 
        $data->save();

        $verifikators = \App\Models\User::where('role', 'verifikator')->get();
        foreach ($verifikators as $verifikator) {
            $verifikator->notify(new \App\Notifications\PengajuanNotification(
                'Pengajuan Tender Baru',
                'Pengajuan Tender baru dari ' . (auth()->user()->name ?? '-') . ' telah dibuat. Silakan klik notification ini untuk detailnya.',
                route('verifikator_pengajuanopen', $data->id)
            ));
        }
        return redirect()->route('ppk_dashboard')->with('pesan', 'Pengajuan berhasil dibuat. Silakan cek secara berkala status pengajuan.');
    }

    public function cekUploadBerkas(Request $request)
    {
        $pengajuan_id = $request->pengajuan_id;
        $metode_pengadaan_id = $request->metode_pengadaan_id;

        // Ambil semua slug berkas wajib
        $berkasWajib = \App\Models\MetodePengadaanBerkas::where('metode_pengadaan_id', $metode_pengadaan_id)->pluck('slug')->toArray();
        // Ambil slug yang sudah diupload
        $berkasUploaded = \App\Models\PengajuanFile::where('pengajuan_id', $pengajuan_id)->pluck('slug')->toArray();

        $belum = array_diff($berkasWajib, $berkasUploaded);

        return response()->json([
            'complete' => count($belum) === 0,
            'belum' => $belum
        ]);
    }

    public function metodePengadaanBerkas($id, Request $request)
    {
        $metode = $request->query('metode');

        $data = DB::select("
            SELECT 
                mpb.id,
                mpb.slug,
                mpb.nama_berkas,
                mpb.multiple,
                COALESCE(pf.status, 0) as status,
                pf.id as pengajuan_files_id,
                pf.slug,
                pf.file_path
            FROM metode_pengadaan_berkass mpb
            LEFT JOIN (
                SELECT pf1.*
                FROM pengajuan_files pf1
                INNER JOIN (
                    SELECT slug, MAX(created_at) as max_created
                    FROM pengajuan_files
                    WHERE pengajuan_id = ?
                    GROUP BY slug
                ) pf2 ON pf1.slug = pf2.slug AND pf1.created_at = pf2.max_created
            ) pf ON pf.slug = mpb.slug
             where mpb.metode_pengadaan_id=?;
        ", [$id, $metode]);
        return response()->json(['data' => $data]);
    }

    public function destroy($id)
    {
        $data = Pengajuan::findOrFail($id);
        // dd($data->user->name);
        // Hapus file terkait di pengajuan_files dan storage
        $files = PengajuanFile::where('pengajuan_id', $data->id)->get();
        foreach ($files as $file) {

            $filePath = public_path('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $file->slug . '/' . $file->file_path);
            // dd(file_exists($filePath));
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        PengajuanFile::where('pengajuan_id', $data->id)->delete();

        // Hapus data utama
        $data->delete();

        return response()->json(['success' => true, 'pesan' => 'Data berhasil dihapus.']);
    }

    public function create()
    {
        $metodepengadaan = MetodePengadaan::selectRaw('id,nama_metode_pengadaan')->where('status', 1)->pluck('nama_metode_pengadaan', 'id');
        $pengajuan = Pengajuan::where('user_id', Auth::user()->id)->where('status', 9)->first();

        return view('dashboard.create', compact('pengajuan', 'metodepengadaan'));
    }

    public function uploadBerkasAjax(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'pengajuan_id' => 'required|exists:pengajuans,id',
            'berkas_id' => 'required|exists:metode_pengadaan_berkass,id',
        ]);

        $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
        $berkas = MetodePengadaanBerkas::findOrFail($request->berkas_id);
        $slug = $berkas->slug;

        $folderPath = 'pengajuan/' . now()->format('d-m-Y') . '/' . Auth::user()->username . '/' . $pengajuan->id . '/' . $slug;

        // Jika single file, hapus file lama
        if ($berkas->multiple != 1) {
            $existing = PengajuanFile::where('pengajuan_id', $pengajuan->id)
                ->where('slug', $slug)
                ->first();
            if ($existing) {
                $oldPath = public_path($existing->file_path);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                $existing->delete();
            }
        }

        // Proses upload
        $files = $request->file('file');
        if (is_array($files)) {
            // Multiple file
            foreach ($files as $file) {
                $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($folderPath), $filename);

                PengajuanFile::create([
                    'pengajuan_id' => $pengajuan->id,
                    'nama_file' => $berkas->nama_berkas,
                    'slug' => $slug,
                    'file_path' => $folderPath . '/' . $filename,
                ]);
            }
        } else {
            // Single file
            $file = $files;
            $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($folderPath), $filename);

            PengajuanFile::create([
                'pengajuan_id' => $pengajuan->id,
                'nama_file' => $berkas->nama_berkas,
                'slug' => $slug,
                'file_path' => $folderPath . '/' . $filename,
            ]);
        }

        return response()->json(['success' => true]);
    }

    
    

    






    // public function kirim_pengajuan(Request $request)
    // {
    //     // Validasi data utama
    //     $request->validate([
    //         'kode_rup' => 'required|string|max:255',
    //         'nama_paket' => 'required|string|max:255',
    //         'perangkat_daerah' => 'required|string|max:255',
    //         'rekening_kegiatan' => 'required|string|max:255',
    //         'sumber_dana' => 'required|string|max:255',
    //         'pagu_anggaran' => 'required|string|max:255',
    //         'pagu_hps' => 'required|string|max:255',
    //         'jenis_pengadaan' => 'required|string',
    //         'metode_pengadaan_id' => 'required',
    //     ]);

    //     // Simpan data utama pengajuan
    //     $data = new Pengajuan();
    //     $data->kode_rup             = $request->kode_rup;
    //     $data->nama_paket           = $request->nama_paket;
    //     $data->perangkat_daerah     = $request->perangkat_daerah;
    //     $data->rekening_kegiatan    = $request->rekening_kegiatan;
    //     $data->sumber_dana          = $request->sumber_dana;
    //     $data->pagu_anggaran        = $request->pagu_anggaran;
    //     $data->pagu_hps             = $request->pagu_hps;
    //     $data->jenis_pengadaan      = $request->jenis_pengadaan;
    //     $data->metode_pengadaan_id     = $request->metode_pengadaan_id;
    //     $data->user_id              = auth()->user()->id;
    //     $data->save();

    //     // Ambil semua berkas dari metode_pengadaan_berkas yang aktif
    //     $berkasList = MetodePengadaanBerkas::where('metode_pengadaan_id', $request->metode_pengadaan_id)
    //         ->where('status', 1)
    //         ->get();

    //     // Penampung jumlah nama_file
    //     $namaFileCount = [];

    //     // Simpan file ke tabel pengajuan_files
    //     foreach ($berkasList as $berkas) {
    //         $inputName = $berkas->slug . ($berkas->multiple == 1 ? '' : '');
    //         if ($request->hasFile($inputName)) {
    //             $files = $request->file($inputName);
    //             if ($berkas->multiple == 1 && is_array($files)) {
    //                 foreach ($files as $file) {
    //                     // Penomoran nama_file
    //                     $baseName = $berkas->nama_berkas;
    //                     if (!isset($namaFileCount[$baseName])) {
    //                         $namaFileCount[$baseName] = 1;
    //                     } else {
    //                         $namaFileCount[$baseName]++;
    //                     }
    //                     $namaFileFinal = $baseName;
    //                     if ($namaFileCount[$baseName] > 1) {
    //                         $namaFileFinal .= ' ' . $namaFileCount[$baseName];
    //                     }

    //                     $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
    //                     $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

    //                     // Simpan ke tabel pengajuan_files
    //                     \DB::table('pengajuan_files')->insert([
    //                         'pengajuan_id' => $data->id,
    //                         'nama_file' => $namaFileFinal,
    //                         'slug' => $berkas->slug,
    //                         'file_path' => $filename,
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                     ]);
    //                 }
    //             } else {
    //                 $file = is_array($files) ? $files[0] : $files;
    //                 // Penomoran nama_file
    //                 $baseName = $berkas->nama_berkas;
    //                 if (!isset($namaFileCount[$baseName])) {
    //                     $namaFileCount[$baseName] = 1;
    //                 } else {
    //                     $namaFileCount[$baseName]++;
    //                 }
    //                 $namaFileFinal = $baseName;
    //                 if ($namaFileCount[$baseName] > 1) {
    //                     $namaFileFinal .= ' ' . $namaFileCount[$baseName];
    //                 }

    //                 $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
    //                 $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

    //                 // Simpan ke tabel pengajuan_files
    //                 \DB::table('pengajuan_files')->insert([
    //                     'pengajuan_id' => $data->id,
    //                     'nama_file' => $namaFileFinal,
    //                     'slug' => $berkas->slug,
    //                     'file_path' => $filename,
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ]);
    //             }
    //         }
    //     }


    //     return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil dikirim.']);
    // }
}
