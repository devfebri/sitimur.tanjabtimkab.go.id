<?php

namespace App\Http\Controllers;

use App\Models\MetodePengadaan;
use App\Models\MetodePengadaanBerkas;
use App\Models\Pengajuan;
use App\Models\PengajuanFile;
use App\Models\PengajuanPokja;
use App\Models\User;
use App\Notifications\PengajuanDisetujui;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        if(Auth::user()->role=='ppk'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','status', 'verifikator_status', 'kepalaukpbj_status','verifikator_updated','kepalaukpbj_updated','created_at')
                ->where('user_id', Auth::user()->id)
                ->where('status', '!=', 9)
                ->orderBy('created_at','desc'); // Exclude status 9 (draft)
        } elseif (Auth::user()->role == 'verifikator'){
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan','verifikator_status','verifikator_updated','kepalaukpbj_updated','created_at')
                 ->where('status', '!=', 9)
                ->orderBy('created_at', 'desc'); 
        } elseif (Auth::user()->role == 'kepalaukpbj') {
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status','verifikator_updated','kepalaukpbj_updated','created_at')
                ->where('verifikator_status', 1)
                ->where('status', '!=', 9)
                ->orderBy('created_at', 'desc');
        } elseif(Auth::user()->role == 'pokjapemilihan'){
            $userId = Auth::user()->id;
            
            $query = \DB::table('pengajuans')
                ->select('id', 'kode_rup', 'nama_paket', 'perangkat_daerah', 'rekening_kegiatan', 'sumber_dana', 'pagu_anggaran', 'pagu_hps', 'jenis_pengadaan', 'kepalaukpbj_status', 'verifikator_updated', 'kepalaukpbj_updated', 'created_at','status','pokja1_status_akhir', 'pokja2_status_akhir', 'pokja3_status_akhir', 'pokja1_id', 'pokja2_id', 'pokja3_id')
                ->where('verifikator_status', 1)
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
                    'verifikator_updated',
                    'kepalaukpbj_updated',
                    'pokja1_id',
                    'pokja2_id',
                    'pokja3_id',
                    'created_at'
                )
                ->where(function ($q) {
                    $q->where('status', 21)
                        ->orWhere('status', 31);
                })
                ->where(function ($q) {
                    $userId = auth()->user()->id;
                    $q->where('pokja1_id', $userId)
                        ->orWhere('pokja2_id', $userId)
                        ->orWhere('pokja3_id', $userId);
                })
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
            
            // Sekarang $pokjaKe berisi angka 1/2/3 atau null jika tidak ditemukan
            if (Auth::user()->role == 'ppk') {

                $button = '<button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>';
                if($row->verifikator_status==0 && $row->kepalaukpbj_status==0 ){
                    $button .= '<button class="btn btn-danger btn-sm delete-post" data-id="' . $row->id . '">Hapus</button>';
                }
                if($row->status==0){
                    $status= '<span class="badge badge-pill badge-primary">Verifikator</span>';
                }elseif($row->status==11){
                    $status= '<span class="badge badge-pill badge-primary">Kepala UKPBJ</span>';
                }elseif($row->status==12){
                    $status= '<span class="badge badge-pill badge-danger">Verifikator</span> <br>';
                    $status.= '<small><i>Pengajuan tidak disetujui</i></small>';
                }elseif($row->status==13){
                    $status= '<span class="badge badge-pill badge-primary">Verifikator</span>';
                }elseif($row->status==14){
                        $status= '<span class="badge badge-pill badge-warning">PPK</span>';
                }elseif($row->status==21){
                    $status= '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                }
                elseif($row->status==22){

                    $status= '<span class="badge badge-pill badge-primary">Kepala UKPBJ</span>';
                }
                elseif($row->status==31){

                    $status= '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                }
                elseif($row->status==32){

                    $status= '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                }
                elseif($row->status==33){

                    $status= '<span class="badge badge-pill badge-primary">Pokja Pemilihan</span>';
                }
                elseif($row->status==34){

                    $status= '<span class="badge badge-pill badge-warning">PPK</span>';
                }else{

                    $status= '<span class="badge badge-pill badge-primary">Error</span>';
                }
            } elseif(Auth::user()->role=="verifikator"){
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
    public function open($id)
    {
        // dd('ok');
        // fungsi untuk merubah status pokja menjadi sedang direviu
        $statuspokja = Pengajuan::where('id', $id)
            ->where(function ($query) {
                $userId = Auth::user()->id;
                $query->where('pokja1_id', $userId)
                    ->orWhere('pokja2_id', $userId)
                    ->orWhere('pokja3_id', $userId);
            })
            ->first();

        $pokjaKe = null;
        if ($statuspokja) {
            if ($statuspokja->pokja1_id == Auth::user()->id) {
                $pokjaKe = 1;
            } elseif ($statuspokja->pokja2_id == Auth::user()->id) {
                $pokjaKe = 2;
            } elseif ($statuspokja->pokja3_id == Auth::user()->id) {
                $pokjaKe = 3;
            }
        }
        if ($pokjaKe) {
            $statuspokja = Pengajuan::where('id', $id)
                ->where('pokja' . $pokjaKe . '_id', Auth::user()->id)
                ->where('pokja' . $pokjaKe . '_status_akhir', 0)
                ->first();

            if ($statuspokja) {
                $kolomStatus = 'pokja' . $pokjaKe . '_status_akhir';
                $statuspokja->$kolomStatus = 1;
                $statuspokja->save();
            }
        }
        // dd($pokjaKe);
        // $idUser = Auth::user()->id;

        // if(Auth::user()->role=='pokjapemilihan' && $statuspokja->count()>0){
        //     // dd('ok');
        //     $statuspokja->update(['status'=>1]);
        // }


        $data = Pengajuan::findOrFail($id);
        $pokja = PengajuanPokja::where('pengajuan_id', $data->id)->get();
        
        $history = Pengajuan::where('user_id', $data->user_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $files = PengajuanFile::where('pengajuan_id', $data->id)->get();
        
        $tglpengembalian = PengajuanFile::where('pengajuan_id', $data->id)
            ->where('status_verifikator', 3)
            ->orderBy('created_at', 'desc')
            ->first();

        $revisi = PengajuanFile::where('pengajuan_id', $data->id)
            ->where('revisi_ke', '!=', 0)
            ->orderBy('created_at', 'desc')
            ->first();
        $createdAtRevisiTerakhir = $revisi ? $revisi->created_at : null;
        return view('dashboard.open', compact('data', 'pokja','files', 'history', 'tglpengembalian','revisi', 'createdAtRevisiTerakhir'));
    }
    public function getFiles($id, Request $request)
    {

        if ($request->ajax()) {
            $files = PengajuanFile::where('pengajuan_id', $id)->get();
            $files =  DB::table('pengajuan_files')
                ->select('pengajuan_files.*', 'pengajuans.pokja1_id', 'pengajuans.pokja2_id', 'pengajuans.pokja3_id','pengajuans.verifikator_updated')
                ->join('pengajuans', 'pengajuan_files.pengajuan_id', '=', 'pengajuans.id')
                ->where('pengajuan_files.pengajuan_id', $id)
                ->get();
            return DataTables::of($files)
                ->addIndexColumn()
                ->addColumn('nama_file', function ($row) {
                    return $row->nama_file . ($row->revisi_ke > 0 ? ' (Revisi ' . $row->revisi_ke . ')' : '');
                })  
                ->addColumn('action', function ($row) {
                    $data = Pengajuan::findOrFail($row->pengajuan_id);
                    if ($row->status_verifikator == 3) {
                        if (Auth::user()->role == 'ppk' && $row->status == 0 || $row->status == 3) {
                            $button = '<button class="btn btn-warning btn-sm edit-post" data-id="' . $row->id . '">Edit</button>';
                            $button .= '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                        } else {
                            $button = '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                        }
                    } else {
                        $button = '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>';
                    }
                    return $button;
                })->addColumn('checkedd', function ($f) {
                    if (Auth::user()->role == 'verifikator') {
                        if ($f->status_verifikator == 0) {
                            return '<input type="checkbox" name="check_data" class="checkboks" value="' . $f->id . '" id="select' . $f->id . '">';
                        } else {
                            return '<input type="checkbox" hidden class="checkboks" >';
                        }
                    } elseif (Auth::user()->role == 'pokjapemilihan') {
                        return '<input type="checkbox" name="check_data" class="checkboks" value="' . $f->id . '" id="select' . $f->id . '">';
                        // if (0 && $f->status == 0) {
                        // } else {
                        //     return '<input type="checkbox" hidden class="checkboks" >';
                        // }
                    }
                })->addColumn('statuss', function ($f) {
                    // Cek jika user pokja pemilihan
                    if (Auth::user()->role == 'pokjapemilihan') {
                        $pokjaKe = null;
                        $userId = Auth::user()->id;
                        if ( $f->pokja1_id == $userId) {
                            $pokjaKe = 1;
                        } elseif ( $f->pokja2_id == $userId) {
                            $pokjaKe = 2;
                        } elseif ( $f->pokja3_id == $userId) {
                            $pokjaKe = 3;
                        }
                        // dd($pokjaKe);
                        if ($pokjaKe) {
                            $statusField = 'pokja' . $pokjaKe . '_status';
                            $updatedField = 'pokja' . $pokjaKe . '_updated';
                            $pesanField = 'pokja' . $pokjaKe . '_pesan';
                            $statusValue = $f->$statusField ?? 0;
                            $updatedValue = $f->$updatedField ?? null;
                            $pesanValue = $f->$pesanField ?? null;

                            if ($statusValue == 0) {
                                $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                            } elseif ($statusValue == 1) {
                                $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                                if ($updatedValue) {
                                    $status .= '<br> ' . \Carbon\Carbon::parse($updatedValue)->format('d/m/Y H:i:s');
                                }
                            } elseif ($statusValue == 2) {
                                $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                                if ($updatedValue) {
                                    $status .= '<br> ' . \Carbon\Carbon::parse($updatedValue)->format('d/m/Y H:i:s');
                                }
                            } elseif ($statusValue == 3 || $statusValue == 99) {
                                $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                                if ($updatedValue) {
                                    $status .= '<br> ' . \Carbon\Carbon::parse($updatedValue)->format('d/m/Y H:i:s');
                                }
                            } else {
                                $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                            }
                            if ($pesanValue) {
                                $status .= '<br>' . $pesanValue;
                            }
                            return $status;
                        }
                    }
                    // Default: status umum
                    if ($f->status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($f->status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                        if ($f->status_updated) {
                            $status .= '<br> ' . $f->status_updated->format('d/m/Y H:i:s');
                        }
                    } elseif ($f->status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                        if ($f->status_updated) {
                            $status .= '<br> ' . $f->status_updated->format('d/m/Y H:i:s');
                        }
                    } elseif ($f->status == 3 || $f->status == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                        if ($f->status_updated) {
                            $status .= '<br> ' . $f->status_updated->format('d/m/Y H:i:s');
                        }
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    return $status;
                })->addColumn('status_verifikator', function ($f) {
                    if ($f->status_verifikator == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($f->status_verifikator == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                        $status .= '<br> ' . $f->verifikator_updated;
                        if ($f->verifikator_updated) {
                        }
                    } elseif ($f->status_verifikator == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                        if ($f->verifikator_updated) {
                            $status .= '<br> ' . $f->verifikator_updated->format('d/m/Y H:i:s');
                        }
                    } elseif ($f->status_verifikator == 3 || $f->status_verifikator == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                        if ($f->verifikator_updated) {
                            $status .= '<br> ' . $f->verifikator_updated->format('d/m/Y H:i:s');
                        }
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($f->pesan_verifikator) {
                        $status .= '<br> ' . $f->pesan_verifikator;
                    }
                    return $status;
                })
                ->rawColumns(['action', 'checkedd', 'statuss', 'nama_file', 'status_verifikator'])
                ->make(true);
        }
    }

    public function create()
    {
        $metodepengadaan = MetodePengadaan::selectRaw('id,nama_metode_pengadaan')->where('status', 1)->pluck('nama_metode_pengadaan', 'id');
        $pengajuan=Pengajuan::where('user_id', Auth::user()->id)->where('status',9)->first();

        return view('dashboard.create', compact('pengajuan', 'metodepengadaan'));
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
            if($data->metode_pengadaan_id != $request->metode_pengadaan_id){
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
        }else{
         
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
        
        

        return response()->json(['success' => true, 'message' => 'Step 1 berhasil.','metode' => $request->metode_pengadaan_id,'id'=>$data->id]);
    }

    public function simpanStep2(Request $request)
    {
  

        $data = Pengajuan::findOrFail($request->id);
        $data->status = 0; // Update status to indicate step 2 is complete
        $data->created_at = Carbon::now(); // Set created_at to now
        $data->save();

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
        ", [$id,$metode]);


        // dd($data);
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
            // $uploadPath = public_path('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $file->slug );
            $uploadPath = 'pengajuan/' . $data->created_at->format('d-m-Y') . '/' . Auth::user()->username . '/' . $data->id . '/' . $file->slug;
            $uploaded->move(public_path($uploadPath), $filename);

            // Penomoran nama_file revisi

            

            if($file->status == 99){
                // Insert file revisi sebagai record baru
                \DB::table('pengajuan_files')->insert([
                    'pengajuan_id' => $data->id,
                    'nama_file' => $file->nama_file,
                    'revisi_ke' => $file->revisi_ke + 1, // Increment revisi ke
                    'status_verifikator' => 9,
                    'slug' => $file->slug,
                    'file_path' => $uploadPath . '/' . $filename,
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
                    // $data->pokjapemilihan_status = 0; // Proses/Belum lengkap
                    // $data->pokjapemilihan_updated = Carbon::now();
                    $data->save();
                }
            }else{
                // Insert file revisi sebagai record baru
                \DB::table('pengajuan_files')->insert([
                    'pengajuan_id' => $data->id,
                    'nama_file' => $file->nama_file,
                    'revisi_ke' => $file->revisi_ke + 1, // Increment revisi ke
                    'slug' => $file->slug,
                    'file_path' => $uploadPath . '/' . $filename,
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

        if(Auth::user()->role=='verifikator'){
            // Update status file sesuai request
            foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pFile) {
                    $pFile->status_verifikator = $request->status;
                    $pFile->pesan_verifikator = $request->keterangan;
                    if($request->status == 3){
                        // $pFile->status_pokjapemilihan = 9;
                        $pFile->status=0;
                    }
                    $pFile->save();
                }
            }


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
                $pengajuan->verifikator_id = Auth::user()->id;
                $pengajuan->verifikator_status = 1; // Semua file disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=11;
            } elseif ($anyRejected) {
                $pengajuan->verifikator_id = Auth::user()->id;
                $pengajuan->verifikator_status = 2; // Ada file tidak disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=12; // Status pengajuan 2 (tidak disetujui)
            }elseif ($anyDikembalikan) {
                $pengajuan->verifikator_id = auth()->user()->id;
                $pengajuan->verifikator_status = 3; // Ada file tidak disetujui
                $pengajuan->verifikator_updated = Carbon::now();
                $pengajuan->status=14;
            } else {
                $pengajuan->verifikator_status = 0; // Proses/Belum lengkap
            }
            $pengajuan->save();
        }else if(Auth::user()->role=='pokjapemilihan'){
            // Cek user pokja ke berapa
            $pokjaKe = null;
            if ($pengajuan->pokja1_id == Auth::user()->id) {
                $pokjaKe = 1;
            } elseif ($pengajuan->pokja2_id == Auth::user()->id) {
                $pokjaKe = 2;
            } elseif ($pengajuan->pokja3_id == Auth::user()->id) {
                $pokjaKe = 3;
            }
            // Sekarang $pokjaKe berisi 1/2/3 atau null jika tidak ditemukan

            // Update status file sesuai request
           
            foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pokjaKe == 1) {
                    $pFile->pokja1_status = $request->status;
                    $pFile->pokja1_updated = Carbon::now();
                    if ($request->keterangan) {
                        $pFile->pokja1_pesan = $request->keterangan;
                    }
                }elseif ($pokjaKe == 2) {
                    $pFile->pokja2_status = $request->status;
                    $pFile->pokja2_updated = Carbon::now();
                    if ($request->keterangan) {
                        $pFile->pokja2_pesan = $request->keterangan;
                    }
                }elseif ($pokjaKe == 3) {
                    $pFile->pokja3_status = $request->status;
                    $pFile->pokja3_updated = Carbon::now();
                    if ($request->keterangan) {
                        $pFile->pokja3_pesan = $request->keterangan;
                    }
                }
                $pFile->save();
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
                // return $file->status_pokjapemilihan == 1;
            });

            $anyRejected = $allFiles->contains(function ($file) {
                // return $file->status_pokjapemilihan == 2;
            });

            $anyDikembalikan = $allFiles->contains(function ($file) {
                // return $file->status_pokjapemilihan == 3;
            });
            if ($allApproved) {
                // $pengajuan->pokjapemilihan_id = auth()->user()->id;
                // $pengajuan->pokjapemilihan_status = 1; // Semua file disetujui
                // $pengajuan->pokjapemilihan_updated = Carbon::now();
                $pengajuan->status = 31;
            } elseif ($anyRejected) {
                // $pengajuan->pokjapemilihan_id = auth()->user()->id;
                // $pengajuan->pokjapemilihan_status = 2; // Ada file tidak disetujui
                // $pengajuan->pokjapemilihan_updated = Carbon::now();
                $pengajuan->status = 32;
            } elseif ($anyDikembalikan) {
                // $pengajuan->pokjapemilihan_id = auth()->user()->id;
                // $pengajuan->pokjapemilihan_status = 3; // Ada file tidak disetujui
                // $pengajuan->pokjapemilihan_updated = Carbon::now();
                $pengajuan->status = 34;
            } else {
                // $pengajuan->pokjapemilihan_status = 0; // Proses/Belum lengkap
            }
            $pengajuan->save();
        }
        $pengajuan->user->notify(new PengajuanDisetujui($pengajuan));
        

        return response()->json(['success' => true]);
    }
    public function disposisiPokja(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->kepalaukpbj_id=Auth::user()->id;
        $pengajuan->kepalaukpbj_updated = Carbon::now();
        $pengajuan->kepalaukpbj_status = 1; // Disetujui Kepala UKPBJ
        $pengajuan->status = 0;
        $pengajuan->save();

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil didisposisi ke Pokja Pemilihan.']);
    }

    public function tolakPengajuan(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->kepalaukpbj_id = Auth::user()->id;
        $pengajuan->kepalaukpbj_updated = Carbon::now();
        $pengajuan->kepalaukpbj_status = 2; // Ditolak Kepala UKPBJ
        $pengajuan->status = 2; // 2 = Tidak Disetujui
        $pengajuan->save();

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil ditolak.']);
    }

    public function uploadBerkasAjax(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'pengajuan_id' => 'required|exists:pengajuans,id',
        ]);

        $file = $request->file('file');
        $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);
        $berkas=MetodePengadaanBerkas::find($request->berkas_id);
        // dd($berkas);
        // Cek & hapus file lama
        $existing = PengajuanFile::where('pengajuan_id', $pengajuan->id)
            ->where('slug', $request->slug)
            ->first();

        if ($existing) {
            $oldPath = public_path($existing->file_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $existing->delete();
        }

        // Simpan file baru
        $folderPath = 'pengajuan/' . now()->format('d-m-Y') . '/' . Auth::user()->username . '/' . $pengajuan->id . '/' . $request->slug;
        $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($folderPath), $filename);

        PengajuanFile::create([
            'pengajuan_id' => $pengajuan->id,
            'nama_file' => $berkas->nama_berkas,
            'slug' => $berkas->slug,
            'file_path' => $folderPath . '/' . $filename,
        ]);

        return response()->json(['success' => true]);
    }

    public function getPokja()
    {
        $pokja = User::select('id','name','nip','nik','jabatan')->where('role', 'pokjapemilihan')->get();
   
        return response()->json($pokja);
    }
    public function kirimPokja(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'pokja_ids' => 'required|array|min:3|max:3',
            'pokja_ids.*' => 'exists:users,id', // pastikan id pokja valid
            'pengajuan_id' => 'required|exists:pengajuans,id'
        ]);

        $pengajuan= Pengajuan::find($request->pengajuan_id);
        $pengajuan->status = 21; // Status 21 = Sudah dikirim ke Pokja Pemilihan
        $pengajuan->kepalaukpbj_id=Auth::user()->id;
        $pengajuan->kepalaukpbj_status=1;
        $pengajuan->kepalaukpbj_updated=Carbon::now();

    

        // Simpan data baru
        
        $key=1;
        foreach ($request->pokja_ids as $pokjaId) {
            $pokja_id = 'pokja' . $key . '_id';
            $pokja_status = 'pokja' . $key . '_status_akhir';
            $pengajuan->{$pokja_id}=$pokjaId;
            $pengajuan->{$pokja_status}=0;
            ++$key;
        }
        $pengajuan->save();

        return response()->json(['message' => 'Pokja berhasil dipilih.']);
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
