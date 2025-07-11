<?php

namespace App\Http\Controllers;

use App\Models\MetodePengadaan;
use App\Models\MetodePengadaanBerkas;
use App\Models\Pengajuan;
use App\Models\PengajuanFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PengajuanController extends Controller
{
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

        if(auth()->user()->role=='ppk'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','status', 'verifikator_status', 'kepalaukpbj_status', 'pokjapemilihan_status')
                ->where('user_id', auth()->user()->id);
        } elseif (auth()->user()->role == 'verifikator'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','verifikator_status');
        } elseif (auth()->user()->role == 'kepalaukpbj') {
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status')
                ->where('verifikator_status', 1);
        } elseif(auth()->user()->role == 'pokjapemilihan'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status','pokjapemilihan_status')
                ->where('verifikator_status', 1)->where('kepalaukpbj_status', 1);
        } else{
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','status');
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
        $recordsTotal = \DB::table('pengajuans')->count();

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
            if (auth()->user()->role == 'ppk') {

                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if($row->verifikator_status==0 && $row->kepalaukpbj_status==0 && $row-> pokjapemilihan_status==0){
                    $button .= '<button class="btn btn-danger btn-sm delete-post" data-id="' . $row->id . '">Hapus</button>';
                }
                            
                if ($row->status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                } 
            } elseif(auth()->user()->role=="verifikator"){
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if ($row->verifikator_status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->verifikator_status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->verifikator_status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->verifikator_status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }
            } elseif(auth()->user()->role=='kepalaukpbj'){
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if ($row->kepalaukpbj_status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->kepalaukpbj_status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->kepalaukpbj_status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->kepalaukpbj_status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }
            } elseif (auth()->user()->role == 'pokjapemilihan') {
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if ($row->pokjapemilihan_status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->pokjapemilihan_status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->pokjapemilihan_status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->pokjapemilihan_status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }
            } else{
                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if ($row->status == 0) {
                    $status = '<span class="badge badge-pill badge-primary">Proses</span>';
                } elseif ($row->status == 1) {
                    $status = '<span class="badge badge-pill badge-success">Disetujui</span>';
                } elseif ($row->status == 2) {
                    $status = '<span class="badge badge-pill badge-danger">Tidak Disetujui</span>';
                } elseif ($row->status == 3) {
                    $status = '<span class="badge badge-pill badge-warning">Dikembalikan</span>';
                }
            }
            
            $result[] = [
                'no' => $no++,
                'kode_rup' => $row->kode_rup,
                'nama_paket' => $row->nama_paket,
                'perangkat_daerah' => $row->perangkat_daerah,
                'rekening_kegiatan' => $row->rekening_kegiatan,
                'sumber_dana' => $row->sumber_dana,
                'pagu_anggaran' => $row->pagu_anggaran,
                'pagu_hps' => $row->pagu_hps,
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
    public function open($id)
    {
        $data = Pengajuan::findOrFail($id);
        $history = Pengajuan::where('user_id', $data->user_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $files = PengajuanFile::where('pengajuan_id', $data->id)->get();
        return view('dashboard.open', compact('data', 'files', 'history'));
    }

    public function getFiles($id, Request $request)
    {
       
        if ($request->ajax()) {
            $files = PengajuanFile::where('pengajuan_id', $id)->get();
            return DataTables::of($files)
                ->addIndexColumn()
                ->addColumn('nama_file', function ($row) {
                    return $row->nama_file . ($row->revisi_ke > 0 ? ' (Revisi ' . $row->revisi_ke . ')' : '');
                })
                ->addColumn('action', function ($row) {
                    $data = Pengajuan::findOrFail($row->pengajuan_id);
                    if($row->status_verifikator==3 || $row->status_pokjapemilihan == 3 ){
                        if(auth()->user()->role=='ppk' && $row->status == 0 || $row->status==3){
                            $button= '<button class="btn btn-warning btn-sm edit-post" data-id="' . $row->id . '">Edit</button>';
                            $button .= '<a href="' . asset('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $row->slug . '/' . $row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                        }else{
                            $button = '<a href="' . asset('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $row->slug . '/' . $row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                        }
                    }else{
                        $button = '<a href="' . asset('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $row->slug . '/' . $row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                    }
                    return $button;
                })->addColumn('checkedd',function($f){
                    if(auth()->user()->role=='verifikator'){
                        if($f->status_verifikator==0){
                            return '<input type="checkbox" name="check_data" class="checkboks" value="'.$f->id.'" id="select'.$f->id.'">';
                        }else{
                            return '<input type="checkbox" hidden class="checkboks" >';
                        }
                    }elseif(auth()->user()->role=='pokjapemilihan'){
                        if($f->status_pokjapemilihan==0 && $f->status==0){
                            return '<input type="checkbox" name="check_data" class="checkboks" value="'.$f->id.'" id="select'.$f->id.'">';
                        }else{
                            return '<input type="checkbox" hidden class="checkboks" >';
                        }
                    }
                })->addColumn('statuss', function($f) {
                    if ($f->status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($f->status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($f->status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($f->status == 3 || $f->status==99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    
                    return $status;
                })->addColumn('status_verifikator', function($f) {
                    if ($f->status_verifikator == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($f->status_verifikator == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($f->status_verifikator == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($f->status_verifikator == 3 || $f->status_verifikator==99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($f->pesan_verifikator) {
                        $status .= '<br> ' . $f->pesan_verifikator;
                    } 
                    return $status;
                })->addColumn('status_pokjapemilihan', function($f) {
                    if ($f->status_pokjapemilihan == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($f->status_pokjapemilihan == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($f->status_pokjapemilihan == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($f->status_pokjapemilihan == 3 || $f->status_pokjapemilihan==99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($f->pesan_pokjapemilihan) {
                        $status .= '<br> ' . $f->pesan_pokjapemilihan;
                    } 
                    return $status;
                })
                ->rawColumns(['action','checkedd','statuss','nama_file','status_verifikator','status_pokjapemilihan'])
                ->make(true);
        }
    }

    public function kirim_pengajuan(Request $request)
    {
        // Validasi data utama
        $request->validate([
            'kode_rup' => 'required|string|max:255',
            'nama_paket' => 'required|string|max:255',
            'perangkat_daerah' => 'required|string|max:255',
            'rekening_kegiatan' => 'required|string|max:255',
            'sumber_dana' => 'required|string|max:255',
            'pagu_anggaran' => 'required|string|max:255',
            'pagu_hps' => 'required|string|max:255',
            'jenis_pengadaan' => 'required|string',
            'metode_pengadaan' => 'required',
        ]);

        // Simpan data utama pengajuan
        $data = new Pengajuan();
        $data->kode_rup             = $request->kode_rup;
        $data->nama_paket           = $request->nama_paket;
        $data->perangkat_daerah     = $request->perangkat_daerah;
        $data->rekening_kegiatan    = $request->rekening_kegiatan;
        $data->sumber_dana          = $request->sumber_dana;
        $data->pagu_anggaran        = $request->pagu_anggaran;
        $data->pagu_hps             = $request->pagu_hps;
        $data->jenis_pengadaan      = $request->jenis_pengadaan;
        $data->metode_pengadaan     = $request->metode_pengadaan;
        $data->user_id              = auth()->user()->id;
        $data->save();

        // Ambil semua berkas dari metode_pengadaan_berkas yang aktif
        $berkasList = MetodePengadaanBerkas::where('metode_pengadaan_id', $request->metode_pengadaan)
            ->where('status', 1)
            ->get();

        // Penampung jumlah nama_file
        $namaFileCount = [];

        // Simpan file ke tabel pengajuan_files
        foreach ($berkasList as $berkas) {
            $inputName = $berkas->slug . ($berkas->multiple == 1 ? '' : '');
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if ($berkas->multiple == 1 && is_array($files)) {
                    foreach ($files as $file) {
                        // Penomoran nama_file
                        $baseName = $berkas->nama_berkas;
                        if (!isset($namaFileCount[$baseName])) {
                            $namaFileCount[$baseName] = 1;
                        } else {
                            $namaFileCount[$baseName]++;
                        }
                        $namaFileFinal = $baseName;
                        if ($namaFileCount[$baseName] > 1) {
                            $namaFileFinal .= ' ' . $namaFileCount[$baseName];
                        }

                        $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

                        // Simpan ke tabel pengajuan_files
                        \DB::table('pengajuan_files')->insert([
                            'pengajuan_id' => $data->id,
                            'nama_file' => $namaFileFinal,
                            'slug' => $berkas->slug,
                            'file_path' => $filename,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    $file = is_array($files) ? $files[0] : $files;
                    // Penomoran nama_file
                    $baseName = $berkas->nama_berkas;
                    if (!isset($namaFileCount[$baseName])) {
                        $namaFileCount[$baseName] = 1;
                    } else {
                        $namaFileCount[$baseName]++;
                    }
                    $namaFileFinal = $baseName;
                    if ($namaFileCount[$baseName] > 1) {
                        $namaFileFinal .= ' ' . $namaFileCount[$baseName];
                    }

                    $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

                    // Simpan ke tabel pengajuan_files
                    \DB::table('pengajuan_files')->insert([
                        'pengajuan_id' => $data->id,
                        'nama_file' => $namaFileFinal,
                        'slug' => $berkas->slug,
                        'file_path' => $filename,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil dikirim.']);
    }

    public function metodePengadaanBerkas($id)
    {
        // Ambil data berkas berdasarkan metode_pengadaan_id
        $data = MetodePengadaanBerkas::where('metode_pengadaan_id', $id)->where('status',1)->get();
        return response()->json(['data' => $data]);
    }

    public function destroy($id)
    {
        $data = Pengajuan::findOrFail($id);
        // dd($data->user->name);
        // Hapus file terkait di pengajuan_files dan storage
        $files = PengajuanFile::where('pengajuan_id', $data->id)->get();
        foreach ($files as $file) {
        
            $filePath = public_path('pengajuan/'.$data->created_at->format('d-m-Y').'/' .$data->user->name.'/'. $data->id . '/' . $file->slug. '/' . $file->file_path);
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
    public function editFile($id)
    {
        $file = PengajuanFile::findOrFail($id);
        return response()->json($file);
    }

    public function updateFile(Request $request, $id)
    {
        $file = PengajuanFile::findOrFail($id);
        $data = Pengajuan::findOrFail($file->pengajuan_id);

        

        $request->validate([
            'pesan' => 'nullable|string',
            'file_upload' => 'required|file|max:2048', // max 2MB, sesuaikan jika perlu
        ]);

        // Update status dan pesan
        $file->status = 99;

        // Jika ada file baru diupload
        if ($request->hasFile('file_upload')) {
            $uploaded = $request->file('file_upload');
            $filename = time() . '-' . uniqid() . '-revisi.' . $uploaded->getClientOriginalExtension();
            $uploadPath = public_path('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $file->slug );
            $uploaded->move($uploadPath, $filename);

            // Penomoran nama_file revisi
            

            

            if($file->status_pokjapemilihan == 3 && $file->status == 99){
                // Insert file revisi sebagai record baru
                \DB::table('pengajuan_files')->insert([
                    'pengajuan_id' => $data->id,
                    'nama_file' => $file->nama_file,
                    'revisi_ke' => $file->revisi_ke + 1, // Increment revisi ke
                    'status_verifikator' => 9,
                    'slug' => $file->slug,
                    'file_path' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $file->save();

                $allFiles1 = PengajuanFile::where('pengajuan_id', $file->pengajuan_id)->get();

                // Cek status file
                $allApproveds = $allFiles1->filter(function ($file) {
                    return $file->status != 99;
                })->every(function ($file) {
                    return $file->status_pokjapemilhan == 0 || $file->status_pokjapemilhan == 1;
                });
                if ($allApproveds) {
                    $data->status = 0;
                    $data->pokjapemilihan_status = 0; // Proses/Belum lengkap
                    $data->pokjapemilihan_updated = Carbon::now();
                    $data->save();
                }
            }else{
                // Insert file revisi sebagai record baru
                \DB::table('pengajuan_files')->insert([
                    'pengajuan_id' => $data->id,
                    'nama_file' => $file->nama_file,
                    'revisi_ke' => $file->revisi_ke + 1, // Increment revisi ke
                    'slug' => $file->slug,
                    'file_path' => $filename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $file->save();
                $allFiles = PengajuanFile::where('pengajuan_id', $file->pengajuan_id)->get();

                // Cek status file
                $allApproved = $allFiles->filter(function ($file) {
                    return $file->status != 99;
                })->every(function ($file) {
                    return $file->status_verifikator == 0 || $file->status_verifikator == 1;
                });
                if ($allApproved) {
                    $data->status = 0;
                    $data->verifikator_status = 0; // Proses/Belum lengkap
                    $data->verifikator_updated = Carbon::now();
                    $data->save();
                }
            }
            
            return response()->json(['success' => true, 'pesan' => 'File pengajuan berhasil diupdate.']);
        }else{
            return response()->json(['success' => false, 'pesan' => 'Silahkan isi file pengajuan']);
        }
    }

    public function filesApproval(Request $request, $id)
    {
        $pengajuan = Pengajuan::find($id);

        if(auth()->user()->role=='verifikator'){
            // Update status file sesuai request
            foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pFile) {
                    $pFile->status_verifikator = $request->status;
                    $pFile->pesan_verifikator = $request->keterangan;
                    if($request->status == 3){
                        $pFile->status_pokjapemilihan = 9;
                        $pFile->status=0;
                    }
                    $pFile->save();
                }
            }

            // every() cek semua file status 1.
            // contains() cek ada file status 3.
            // Jika semua 1 → status pengajuan 1, jika ada 3 → status pengajuan 2, selain itu status 0.
            // Ambil ulang semua file pengajuan
            $allFiles = PengajuanFile::where('pengajuan_id', $pengajuan->id)->get();

            // Cek status file
            $allApproved = $allFiles->filter(function ($file) {
                return $file->status != 99;
            })->every(function ($file) {
                return $file->status_verifikator == 1;
            });

            $anyRejected = $allFiles->contains(function ($file) {
                return $file->status_verifikator == 2;
            });
            $anyDikembalikan = $allFiles->contains(function ($file) {
                
                return $file->status_verifikator == 3;
            });

            if ($allApproved) {
                $pengajuan->verifikator_id = auth()->user()->id;
                $pengajuan->verifikator_status = 1; // Semua file disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=0;
            } elseif ($anyRejected) {
                $pengajuan->verifikator_id = auth()->user()->id;
                $pengajuan->verifikator_status = 2; // Ada file tidak disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=2; // Status pengajuan 2 (tidak disetujui)
            }elseif ($anyDikembalikan) {
                $pengajuan->verifikator_id = auth()->user()->id;
                $pengajuan->verifikator_status = 3; // Ada file tidak disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=3;
            } else {
                $pengajuan->verifikator_status = 0; // Proses/Belum lengkap
            }
            $pengajuan->save();
        }else if(auth()->user()->role=='pokjapemilihan'){
             // Update status file sesuai request
             foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pFile) {
                    $pFile->status_pokjapemilihan = $request->status;
                    $pFile->pesan_pokjapemilihan = $request->keterangan;
                    if ($request->status == 3) {
                        $pFile->status = 3;
                    }elseif ($request->status == 2) {
                        $pFile->status = 2;
                    } else {
                        $pFile->status = 1;
                    }
                    $pFile->save();
                }
            }

            // every() cek semua file status 1.
            // contains() cek ada file status 3.
            // Jika semua 1 → status pengajuan 1, jika ada 3 → status pengajuan 2, selain itu status 0.
            // Ambil ulang semua file pengajuan
            $allFiles = PengajuanFile::where('pengajuan_id', $pengajuan->id)->get();

            // Cek status file
            $allApproved = $allFiles->filter(function ($file) {
                return $file->status != 99;
            })->every(function ($file) {
                return $file->status_pokjapemilihan == 1;
            });

            $anyRejected = $allFiles->contains(function ($file) {
                return $file->status_pokjapemilihan == 2;
            });

            $anyDikembalikan = $allFiles->contains(function ($file) {
                return $file->status_pokjapemilihan == 3;
            });
            if ($allApproved) {
                $pengajuan->pokjapemilihan_id = auth()->user()->id;
                $pengajuan->pokjapemilihan_status = 1; // Semua file disetujui
                $pengajuan->pokjapemilihan_updated = Carbon::now();
                $pengajuan->status = 1;
            } elseif ($anyRejected) {
                $pengajuan->pokjapemilihan_id = auth()->user()->id;
                $pengajuan->pokjapemilihan_status = 2; // Ada file tidak disetujui
                $pengajuan->pokjapemilihan_updated = Carbon::now();
            } elseif ($anyDikembalikan) {
                $pengajuan->pokjapemilihan_id = auth()->user()->id;
                $pengajuan->pokjapemilihan_status = 3; // Ada file tidak disetujui
                $pengajuan->pokjapemilihan_updated = Carbon::now();
                $pengajuan->status = 3;
            } else {
                $pengajuan->pokjapemilihan_status = 0; // Proses/Belum lengkap
            }
            $pengajuan->save();
        }
        

        return response()->json(['success' => true]);
    }
    public function disposisiPokja(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->kepalaukpbj_id=auth()->user()->id;
        $pengajuan->kepalaukpbj_updated = Carbon::now();
        $pengajuan->kepalaukpbj_status = 1; // Disetujui Kepala UKPBJ
        $pengajuan->status = 0;
        $pengajuan->save();

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil didisposisi ke Pokja Pemilihan.']);
    }

    public function tolakPengajuan(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->kepalaukpbj_id = auth()->user()->id;
        $pengajuan->kepalaukpbj_updated = Carbon::now();
        $pengajuan->kepalaukpbj_status = 2; // Ditolak Kepala UKPBJ
        $pengajuan->status = 2; // 2 = Tidak Disetujui
        $pengajuan->save();

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil ditolak.']);
    }
}
