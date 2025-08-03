<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;
use App\Models\PengajuanFile;
use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class PengajuanOpenController extends Controller
{
    // INI DATA OPEN
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
                $statuspokja->$kolomStatus = 1; // Sedang direviu
                $statuspokja->save();
            }
        }
        // dd($pokjaKe);
        // $idUser = Auth::user()->id;

        // if(Auth::user()->role=='pokjapemilihan' && $statuspokja->count()>0){
        //     // dd('ok');
        //     $statuspokja->update(['status'=>1]);
        // }


        $data = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3', 'verifikator', 'kepalaUkpbj', 'metodePengadaan'])->findOrFail($id);
        

        $history = Pengajuan::where('user_id', $data->user_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $files = PengajuanFile::where('pengajuan_id', $data->id)->get();

        $tglpengembalian = PengajuanFile::where('pengajuan_id', $data->id)
            ->where('verifikator_status', 3)
            ->orderBy('created_at', 'desc')
            ->first();

        $revisi = PengajuanFile::where('pengajuan_id', $data->id)
            ->where('revisi_ke', '!=', 0)
            ->orderBy('created_at', 'desc')
            ->first();
        $createdAtRevisiTerakhir = $revisi ? $revisi->created_at : null;
        return view('dashboard.open', compact('data',  'files', 'history', 'tglpengembalian', 'revisi', 'createdAtRevisiTerakhir'));
    }
    public function getFiles($id, Request $request)
    {

        if ($request->ajax()) {
            $files =  DB::table('pengajuan_files')
                ->select('pengajuan_files.*', 'pengajuans.pokja1_id', 'pengajuans.pokja2_id', 'pengajuans.pokja3_id', 'pengajuans.verifikator_updated_akhir','pengajuans.verifikator_status_akhir',  'pengajuans.updated_at')
                ->join('pengajuans', 'pengajuan_files.pengajuan_id', '=', 'pengajuans.id')
                ->where('pengajuan_files.pengajuan_id', $id)
                ->where('pengajuan_files.status', '!=', 99)
                ->orderBy('pengajuan_files.nama_file')
                ->orderBy('pengajuan_files.revisi_ke')
                ->get();

            return DataTables::of($files)
                ->addIndexColumn()
                ->addColumn('nama_file', function ($row) {
                    $nama = $row->nama_file;
                    if ($row->revisi_ke > 0) {
                        $nama .= ' <span class="badge badge-warning-soft">Revisi ' . $row->revisi_ke . '</span>';
                    }
                    // Tambahkan icon jika multiple
                    $isMultiple = PengajuanFile::where('pengajuan_id', $row->pengajuan_id)
                        ->where('nama_file', $row->nama_file)
                        ->count() > 1;
                    if ($isMultiple) {
                        $nama .= ' <i class="mdi mdi-layers text-info" title="Ada lebih dari satu file untuk dokumen ini"></i>';
                    }
                    return $nama;
                })
                ->addColumn('action', function ($row) {
                    
                    if (Auth::user()->role == 'ppk' && $row->verifikator_status == 3 && ($row->status == 0 || $row->status == 3)) {
                        $button = '<button class="btn btn-warning btn-sm edit-post" data-id="' . $row->id . '">Edit</button> ' ;
                        $button .= '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span> Download</a>';
                    }elseif((
                        (Auth::user()->role=='ppk' && $row->pokja1_status == 3) ||
                        (Auth::user()->role=='ppk' && $row->pokja2_status == 3) ||
                        (Auth::user()->role=='ppk' && $row->pokja3_status == 3)
                    ) && ($row->status == 0 || $row->status == 3)){
                        $button = '<button class="btn btn-warning btn-sm edit-post" data-id="' . $row->id . '">Edit</button> ';
                        $button .= '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span> Download</a>';
                    }else{
                        $button = '<a href="' . asset($row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span> Download</a>';
                    }
                    
                        
                    return $button;
                })
                ->addColumn('checkedd', function ($row) {
                    if (Auth::user()->role == 'verifikator') {
                        if ($row->verifikator_status == 0 && $row->verifikator_status_akhir !=2) {
                            return '<input type="checkbox" name="check_data" class="checkboks" value="' . $row->id . '" id="select' . $row->id . '">';
                        } else {
                            return '<input type="checkbox" hidden class="checkboks" >';
                        }
                    } elseif (Auth::user()->role == 'pokjapemilihan') {
                        // Cek pokja mana yang sedang login
                        $pokjaKe = null;
                        $userId = Auth::user()->id;
                        if ($row->pokja1_id == $userId) $pokjaKe = 1;
                        if ($row->pokja2_id == $userId) $pokjaKe = 2;
                        if ($row->pokja3_id == $userId) $pokjaKe = 3;
                        
                        if ($pokjaKe) {
                            $statusField = 'pokja' . $pokjaKe . '_status';
                            $pokjaStatus = $row->$statusField ?? 0;
                            
                            // Tampilkan checkbox hanya jika belum disetujui atau dikembalikan
                            if ($pokjaStatus == 0) {
                                return '<input type="checkbox" name="check_data" class="checkboks" value="' . $row->id . '" id="select' . $row->id . '">';
                            } else {
                                return '<input type="checkbox" hidden class="checkboks" >';
                            }
                        }
                        return '<input type="checkbox" name="check_data" class="checkboks" value="' . $row->id . '" id="select' . $row->id . '">';
                    }
                    return '';
                })
                ->addColumn('statuss', function ($row) {
                    // Status untuk verifikator/pokja
                    $status = '';
                    if (Auth::user()->role == 'pokjapemilihan') {
                        $pokjaKe = null;
                        $userId = Auth::user()->id;
                        if ($row->pokja1_id == $userId) $pokjaKe = 1;
                        if ($row->pokja2_id == $userId) $pokjaKe = 2;
                        if ($row->pokja3_id == $userId) $pokjaKe = 3;
                        if ($pokjaKe) {
                            $statusField = 'pokja' . $pokjaKe . '_status';
                            $updatedField = 'pokja' . $pokjaKe . '_updated';
                            $pesanField = 'pokja' . $pokjaKe . '_pesan';
                            $statusValue = $row->$statusField ?? 0;
                            $updatedValue = $row->$updatedField ?? null;
                            $pesanValue = $row->$pesanField ?? null;

                            if ($statusValue == 0) {
                                $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                            } elseif ($statusValue == 1) {
                                $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                            } elseif ($statusValue == 2) {
                                $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                            } elseif ($statusValue == 3 || $statusValue == 99) {
                                $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                            } else {
                                $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                            }
                            if ($updatedValue) {
                                $status .= '<br> ' . \Carbon\Carbon::parse($updatedValue)->format('d/m/Y H:i:s');
                            }
                            if ($pesanValue) {
                                $status .= '<br>' . $pesanValue;
                            }
                            return $status;
                        }
                    }
                    // Default: status umum
                    if($row->pokja1_status == null || $row->pokja1_status == 0){
                        $pokja1_status='Belum Reviu';
                    }elseif($row->pokja1_status == 1){
                        $pokja1_status = 'Selesai';
                    } elseif ($row->pokja1_status == 3) {
                        $pokja1_status = 'Dikembalikan';
                    } else {
                        $pokja1_status = 'Status: ' . $row->pokja1_status;
                    }
                    
                    if($row->pokja2_status == null || $row->pokja2_status == 0){
                        $pokja2_status='Belum Reviu';
                    }elseif($row->pokja2_status == 1){
                        $pokja2_status = 'Selesai';
                    } elseif ($row->pokja2_status == 3) {
                        $pokja2_status = 'Dikembalikan';
                    } else {
                        $pokja2_status = 'Status: ' . $row->pokja2_status;
                    }
                    
                    if($row->pokja3_status == null || $row->pokja3_status == 0){
                        $pokja3_status='Belum Reviu';
                    }elseif($row->pokja3_status == 1){
                        $pokja3_status = 'Selesai';
                    } elseif ($row->pokja3_status == 3) {
                        $pokja3_status = 'Dikembalikan';
                    } else {
                        $pokja3_status = 'Status: ' . $row->pokja3_status;
                    }
                    $status = '<span ><b><i>Pokja 1 :'. $pokja1_status. '<br> Pokja 2 :' . $pokja2_status . '<br> Pokja 3 :' . $pokja3_status . '<br></i></b></span>';
                    // if ($row->status == 0) {
                    // } elseif ($row->status == 1) {
                    //     $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    // } elseif ($row->status == 2) {
                    //     $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    // } elseif ($row->status == 3 || $row->status == 99) {
                    //     $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    // } else {
                    //     $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    // }
                    // if ($row->updated_at) {
                    //     $status .= '<br> ' . \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y H:i:s');
                    // }
                    return $status;
                })
                ->addColumn('verifikator_status', function ($row) {
                    if ($row->verifikator_status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($row->verifikator_status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($row->verifikator_status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($row->verifikator_status == 3 || $row->verifikator_status == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($row->verifikator_updated) {
                        $status .= '<br> ' . \Carbon\Carbon::parse($row->verifikator_updated)->format('d/m/Y H:i:s');
                    }
                    if ($row->verifikator_pesan) {
                        $status .= '<br> ' . $row->verifikator_pesan;
                    }
                    return $status;
                })
                ->addColumn('pokja1_status', function ($row) {
                    if ($row->pokja1_status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($row->pokja1_status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($row->pokja1_status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($row->pokja1_status == 3 || $row->pokja1_status == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($row->pokja1_updated) {
                        $status .= '<br> ' . \Carbon\Carbon::parse($row->pokja1_updated)->format('d/m/Y H:i:s');
                    }
                    if ($row->pokja1_pesan) {
                        $status .= '<br> ' . $row->pokja1_pesan;
                    }
                    return $status;
                })
                ->addColumn('pokja2_status', function ($row) {
                    if ($row->pokja2_status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($row->pokja2_status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($row->pokja2_status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($row->pokja2_status == 3 || $row->pokja2_status == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($row->pokja2_updated) {
                        $status .= '<br> ' . \Carbon\Carbon::parse($row->pokja2_updated)->format('d/m/Y H:i:s');
                    }
                    if ($row->pokja2_pesan) {
                        $status .= '<br> ' . $row->pokja2_pesan;
                    }
                    return $status;
                })
                ->addColumn('pokja3_status', function ($row) {
                    if ($row->pokja3_status == 0) {
                        $status = '<span class="badge badge-pill badge-primary"><b><i>Belum diperiksa</i></b></span>';
                    } elseif ($row->pokja3_status == 1) {
                        $status = '<span class="badge badge-pill badge-success"><b><i>Disetujui</i></b></span>';
                    } elseif ($row->pokja3_status == 2) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>Tidak Disetujui</i></b></span>';
                    } elseif ($row->pokja3_status == 3 || $row->pokja3_status == 99) {
                        $status = '<span class="badge badge-pill badge-warning"><b><i>Dikembalikan</i></b></span>';
                    } else {
                        $status = '<span class="badge badge-pill badge-secondary"><b><i>Null</i></b></span>';
                    }
                    if ($row->pokja3_updated) {
                        $status .= '<br> ' . \Carbon\Carbon::parse($row->pokja3_updated)->format('d/m/Y H:i:s');
                    }
                    if ($row->pokja3_pesan) {
                        $status .= '<br> ' . $row->pokja3_pesan;
                    }
                    return $status;
                })
                ->rawColumns(['action', 'checkedd', 'statuss', 'nama_file', 'verifikator_status','pokja1_status','pokja2_status','pokja3_status'])
                ->make(true);
        }
    }

    // KETIKA BUTTON EDIT DI KLIK
    public function pengajuan_open_edit($id)
    {
        $file = PengajuanFile::findOrFail($id);
        return response()->json($file);
    }

    // KETIKA DI SUBMIT
    public function pengajuan_open_update(Request $request, $id)
    {
        $pengajuanFile = PengajuanFile::findOrFail($id);
        $pengajuan = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3'])->findOrFail($pengajuanFile->pengajuan_id);
        $request->validate([
            'pesan' => 'nullable|string',
            'file_upload' => 'required|file|max:5120', // max 2MB, sesuaikan jika perlu
        ]);
        // MERUBAH STATUS FILE LAMA JADI 99
        $pengajuanFile->status = 99;

        if ($request->hasFile('file_upload')) {
            // UPLOAD FILE BARU
            $uploaded = $request->file('file_upload');
            $filename = time() . '-' . uniqid() . '-revisi.' . $uploaded->getClientOriginalExtension();
            $uploadPath = 'pengajuan/' . $pengajuan->created_at->format('d-m-Y') . '/' . Auth::user()->username . '/' . $pengajuan->id . '/' . $pengajuanFile->slug;
            $uploaded->move(public_path($uploadPath), $filename);

            // Reset status pokja untuk file yang direvisi jika statusnya 34
            $pokjaResetData = [];
            if ($pengajuan->status == 34) {
                // Reset status pokja yang mengembalikan file ini
                if ($pengajuanFile->pokja1_status == 3) {
                    $pokjaResetData['pokja1_status'] = 0;
                    $pokjaResetData['pokja1_pesan'] = null;
                    $pokjaResetData['pokja1_updated'] = null;
                }
                if ($pengajuanFile->pokja2_status == 3) {
                    $pokjaResetData['pokja2_status'] = 0;
                    $pokjaResetData['pokja2_pesan'] = null;
                    $pokjaResetData['pokja2_updated'] = null;
                }
                if ($pengajuanFile->pokja3_status == 3) {
                    $pokjaResetData['pokja3_status'] = 0;
                    $pokjaResetData['pokja3_pesan'] = null;
                    $pokjaResetData['pokja3_updated'] = null;
                }
            }
            
            \DB::table('pengajuan_files')->insert([
                'pengajuan_id' => $pengajuan->id,
                'nama_file' => $pengajuanFile->nama_file,
                'revisi_ke' => $pengajuanFile->revisi_ke + 1, // Increment revisi ke
                'verifikator_status' => 0,
                'slug' => $pengajuanFile->slug,
                'file_path' => $uploadPath . '/' . $filename,
                'created_at' => now(),
                'updated_at' => now(),
            ] + $pokjaResetData);
        
            $pengajuanFile->save();
                    
            // Update status pengajuan berdasarkan kondisi
            if ($pengajuan->status == 14) {
                // Jika status 14 (dikembalikan verifikator) -> reset ke verifikator
                $pengajuan->verifikator_status_akhir = 0;
                $pengajuan->verifikator_updated_akhir = Carbon::now();
                $pengajuan->status = 0;
                
                // Kirim notifikasi ke verifikator
                $verifikators = \App\Models\User::where('role', 'verifikator')->get();
                foreach ($verifikators as $verifikator) {
                    $verifikator->notify(new \App\Notifications\PengajuanUntukVerifikatorNotification(
                        'Perbaruan File Pengajuan',
                        $pengajuan->user->name . ' telah memperbarui file pengajuannya, silakan cek kembali untuk verifikasi.',
                        route('verifikator_pengajuanopen', $pengajuan->id)
                    ));
                }
            } elseif ($pengajuan->status == 34) {
                // Jika status 34 (dikembalikan pokja) -> reset ke pokja
                $pengajuan->status = 21; // Kembali ke status reviu pokja
                
                // Kirim notifikasi ke pokja yang terkait
                $pokjaIds = [];
                if ($pengajuan->pokja1_id) $pokjaIds[] = $pengajuan->pokja1_id;
                if ($pengajuan->pokja2_id) $pokjaIds[] = $pengajuan->pokja2_id;
                if ($pengajuan->pokja3_id) $pokjaIds[] = $pengajuan->pokja3_id;
                
                foreach ($pokjaIds as $pokjaId) {
                    $pokjaUser = \App\Models\User::find($pokjaId);
                    if ($pokjaUser) {
                        $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                            'File Pengajuan Telah Diperbarui',
                            $pengajuan->user->name . ' telah memperbarui file pengajuan untuk paket "' . $pengajuan->nama_paket . '". Silakan lakukan reviu ulang.',
                            route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                        ));
                    }
                }
            }
            
            $pengajuan->save();

            $allFiles = PengajuanFile::where('pengajuan_id', $pengajuanFile->pengajuan_id)->get();
            // Cek status file
            $allApproved = $allFiles->filter(function ($file) {
                return $file->status != 99;
            })->every(function ($file) {
                return $file->status == 0 || $file->status == 1;
            });
            if ($allApproved) {
                $pengajuan->status = ($pengajuan->status == 21) ? 21 : 0;
                $pengajuan->save();
            }

            return response()->json(['success' => true, 'pesan' => 'File pengajuan berhasil diupdate.']);
        } else {
            return response()->json(['success' => false, 'pesan' => 'Silahkan isi file pengajuan']);
        }
    }

    public function filesApproval(Request $request, $id)
    {
        $pengajuan = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3'])->find($id);

        if (!$pengajuan) {
            return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan.']);
        }

        if (Auth::user()->role == 'verifikator') {
            // Update status file sesuai request
            foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pFile) {
                    $pFile->verifikator_status = $request->status;
                    $pFile->verifikator_pesan = $request->keterangan;
                    $pFile->verifikator_updated = Carbon::now();
                    
                    // Update status utama file berdasarkan aksi verifikator
                    if ($request->status == 1) {
                        $pFile->status = 1; // Disetujui
                        
                        // Notifikasi ke user bahwa file disetujui oleh verifikator
                        try {
                            if ($pengajuan->user) {
                                $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                                    'File Disetujui Verifikator',
                                    'File "' . $pFile->nama_file . '" telah disetujui oleh Verifikator.',
                                    route('ppk_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error sending notification: " . $e->getMessage());
                        }
                        
                    } elseif ($request->status == 3) {
                        $pFile->status = 3; // Perlu perbaikan
                        
                        // Notifikasi ke user bahwa file perlu diperbaiki
                        try {
                            if ($pengajuan->user) {
                                $pesan = 'File "' . $pFile->nama_file . '" perlu diperbaiki oleh Verifikator.';
                                if ($request->keterangan) {
                                    $pesan .= ' Pesan: ' . $request->keterangan;
                                }
                                
                                $pengajuan->user->notify(new \App\Notifications\PengajuanPerbaikanFileNotification(
                                    'File Perlu Diperbaiki - Verifikator',
                                    $pesan,
                                    route('ppk_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error sending notification: " . $e->getMessage());
                        }
                    }
                    
                    $pFile->save();
                }
            }

            $allFiles = PengajuanFile::where('pengajuan_id', $pengajuan->id)
                ->where('status', '!=', 99)->get();

            // Cek status file
            $allApproved = $allFiles->every(function ($file) {
                return $file->verifikator_status == 1;
            });

            $anyDikembalikan = $allFiles->contains(function ($file) {
                return $file->verifikator_status == 3;
            });

            if ($allApproved) {
                $pengajuan->verifikator_id = Auth::user()->id;
                $pengajuan->verifikator_status_akhir = 1; // Semua file disetujui
                $pengajuan->verifikator_updated_akhir = Carbon::now();
                $pengajuan->status = 11;
                
                // Notifikasi ke user
                try {
                    if ($pengajuan->user) {
                        $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                            'Pengajuan Anda Telah Disetujui Verifikator',
                            'Pengajuan tender Anda telah lolos verifikasi. Silakan menunggu proses penunjukan Pokja oleh Kepala UKPBJ.',
                            route('ppk_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to user: " . $e->getMessage());
                }
                
                // Notifikasi ke Kepala UKPBJ
                try {
                    $kepalaUKPBJs = \App\Models\User::where('role', 'kepalaukpbj')->get();
                    foreach ($kepalaUKPBJs as $kepala) {
                        $kepala->notify(new \App\Notifications\PengajuanNotification(
                            'Pengajuan Tender Baru Masuk',
                            'Terdapat pengajuan tender yang telah disetujui verifikator dan menunggu persetujuan Anda untuk penunjukan Pokja.',
                            route('kepalaukpbj_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to kepala UKPBJ: " . $e->getMessage());
                }
                
            } elseif ($anyDikembalikan) {
                $pengajuan->verifikator_id = Auth::user()->id;
                $pengajuan->verifikator_status_akhir = 3; // Ada file tidak disetujui
                $pengajuan->verifikator_updated_akhir = Carbon::now();
                $pengajuan->status = 14;
                
                // Kirim notifikasi ke user pengajuan
                try {
                    if ($pengajuan->user) {
                        $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                            'Perbaikan File Pengajuan',
                            'Ada file yang perlu diperbaiki oleh verifikator. Silakan perbarui file pengajuan Anda paling lambat tanggal '.Carbon::now()->addDays(3)->format('d/m/Y H:i:s'),
                            route('ppk_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to user: " . $e->getMessage());
                }
            } else {
                $pengajuan->verifikator_status_akhir = 0; // Proses/Belum lengkap
            }
            $pengajuan->save();
            
        } elseif (Auth::user()->role == 'pokjapemilihan') {
            // Tentukan pokja ke berapa user ini
            $pokjaKe = null;
            if ($pengajuan->pokja1_id == Auth::user()->id) {
                $pokjaKe = 1;
            } elseif ($pengajuan->pokja2_id == Auth::user()->id) {
                $pokjaKe = 2;
            } elseif ($pengajuan->pokja3_id == Auth::user()->id) {
                $pokjaKe = 3;
            }

            if (!$pokjaKe) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk pokja ini.']);
            }

            // Update status file sesuai request
            foreach ($request->id as $row) {
                $pFile = PengajuanFile::find($row);
                if ($pFile) {
                    $statusField = 'pokja' . $pokjaKe . '_status';
                    $updatedField = 'pokja' . $pokjaKe . '_updated';
                    $pesanField = 'pokja' . $pokjaKe . '_pesan';

                    // Update status pokja spesifik dengan direct assignment
                    $pFile->{$statusField} = (int)$request->status;
                    $pFile->{$updatedField} = Carbon::now();
                    if ($request->keterangan) {
                        $pFile->{$pesanField} = $request->keterangan;
                    }

                    // Update status utama file berdasarkan aksi pokja
                    if ($request->status == 1) {
                        // Jika pokja menyetujui, set status file menjadi 1
                        $pFile->status = 1;
                        
                        // Notifikasi ke user bahwa file disetujui oleh pokja
                        try {
                            if ($pengajuan->user) {
                                $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                                    'File Disetujui Pokja ' . $pokjaKe,
                                    'File "' . $pFile->nama_file . '" telah disetujui oleh Pokja Pemilihan ' . $pokjaKe . '.',
                                    route('ppk_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error sending notification: " . $e->getMessage());
                        }
                        
                    } elseif ($request->status == 3) {
                        // Jika pokja meminta perbaikan, set status file menjadi 3
                        $pFile->status = 3;
                        
                        // Notifikasi ke user bahwa file perlu diperbaiki
                        try {
                            if ($pengajuan->user) {
                                $pesan = 'File "' . $pFile->nama_file . '" perlu diperbaiki oleh Pokja Pemilihan ' . $pokjaKe . '.';
                                if ($request->keterangan) {
                                    $pesan .= ' Pesan: ' . $request->keterangan;
                                }
                                
                                $pengajuan->user->notify(new \App\Notifications\PengajuanPerbaikanFileNotification(
                                    'File Perlu Diperbaiki - Pokja ' . $pokjaKe,
                                    $pesan,
                                    route('ppk_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error sending notification: " . $e->getMessage());
                        }
                    }
                    
                    // Pastikan save berhasil dengan error handling
                    try {
                        $saved = $pFile->save();
                        if (!$saved) {
                            \Log::error("Failed to save file ID: {$pFile->id}");
                        }
                        
                        // Refresh data dari database untuk memastikan update berhasil
                        $pFile->refresh();
                        
                        // Log untuk debugging (opsional - bisa dihapus nanti)
                        \Log::info("File ID: {$pFile->id}, Pokja: {$pokjaKe}, Status Field: {$statusField}, New Status: {$pFile->{$statusField}}, File Status: {$pFile->status}");
                        
                    } catch (\Exception $e) {
                        \Log::error("Error saving file ID {$pFile->id}: " . $e->getMessage());
                        return response()->json(['success' => false, 'message' => 'Gagal menyimpan perubahan']);
                    }
                }
            }

            // Ambil ulang semua file pengajuan yang aktif
            $allFiles = PengajuanFile::where('pengajuan_id', $pengajuan->id)
                ->where('status', '!=', 99)->get();

            // Cek apakah ada pokja yang meminta perbaikan pada file yang sedang di-reviu
            $anyPokjaNeedsFix = $allFiles->contains(function ($file) {
                return $file->pokja1_status == 3 || $file->pokja2_status == 3 || $file->pokja3_status == 3;
            });

            // Cek apakah semua pokja sudah menyetujui semua file
            $allPokjaApproved = $allFiles->every(function ($file) use ($pengajuan) {
                // Cek untuk setiap pokja yang assigned
                $pokja1_ok = true;
                $pokja2_ok = true;
                $pokja3_ok = true;
                
                if ($pengajuan->pokja1_id) {
                    $pokja1_ok = ($file->pokja1_status == 1);
                }
                if ($pengajuan->pokja2_id) {
                    $pokja2_ok = ($file->pokja2_status == 1);
                }
                if ($pengajuan->pokja3_id) {
                    $pokja3_ok = ($file->pokja3_status == 1);
                }
                
                return $pokja1_ok && $pokja2_ok && $pokja3_ok;
            });

            // Update status pengajuan berdasarkan kondisi
            if ($anyPokjaNeedsFix) {
                // Jika ada pokja yang meminta perbaikan
                $pengajuan->status = 34; // Ada file dikembalikan pokja
                
                // Notifikasi ke user pengajuan
                try {
                    if ($pengajuan->user) {
                        $pengajuan->user->notify(new \App\Notifications\PengajuanPerbaikanFileNotification(
                            'File Pengajuan Perlu Diperbaiki',
                            'Ada file yang perlu diperbaiki berdasarkan reviu Pokja. Silakan perbarui file pengajuan Anda.',
                            route('ppk_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to user: " . $e->getMessage());
                }

                // Notifikasi ke pokja lain tentang adanya perubahan status
                try {
                    for ($i = 1; $i <= 3; $i++) {
                        if ($i != $pokjaKe && $pengajuan->{'pokja' . $i . '_id'}) {
                            $pokjaUser = \App\Models\User::find($pengajuan->{'pokja' . $i . '_id'});
                            if ($pokjaUser) {
                                $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                                    'Perubahan Status Pengajuan',
                                    'Terdapat perubahan status pada pengajuan tender yang sedang Anda reviu. Pokja ' . $pokjaKe . ' telah meminta perbaikan pada beberapa file.',
                                    route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to pokja: " . $e->getMessage());
                }

                // Untuk file yang sudah disetujui pokja lain, status tetap 1 (disetujui)
                foreach ($allFiles as $file) {
                    $shouldKeepApproved = false;
                    
                    // Cek apakah ada pokja lain yang sudah menyetujui file ini
                    if ($file->pokja1_status == 1 && $pokjaKe != 1) $shouldKeepApproved = true;
                    if ($file->pokja2_status == 1 && $pokjaKe != 2) $shouldKeepApproved = true;
                    if ($file->pokja3_status == 1 && $pokjaKe != 3) $shouldKeepApproved = true;
                    
                    // Jika ada pokja lain yang sudah menyetujui dan file ini tidak diminta perbaikan oleh pokja yang sedang login
                    if ($shouldKeepApproved && $request->status != 3) {
                        $file->status = 1; // Tetap disetujui
                        $file->save();
                    }
                }

            } elseif ($allPokjaApproved && $allFiles->count() > 0) {
                // Jika semua pokja menyetujui
                $pengajuan->status = 31; // Semua file disetujui pokja
                
                // Notifikasi ke user pengajuan
                try {
                    if ($pengajuan->user) {
                        $pengajuan->user->notify(new \App\Notifications\PengajuanDisetujui(
                            'Pengajuan Anda Telah Disetujui Pokja',
                            'Semua file pengajuan Anda telah disetujui oleh Pokja Pemilihan.',
                            route('ppk_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to user: " . $e->getMessage());
                }

                // Notifikasi ke semua pokja tentang penyelesaian reviu
                try {
                    for ($i = 1; $i <= 3; $i++) {
                        if ($pengajuan->{'pokja' . $i . '_id'}) {
                            $pokjaUser = \App\Models\User::find($pengajuan->{'pokja' . $i . '_id'});
                            if ($pokjaUser) {
                                $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                                    'Pengajuan Tender Selesai Direviu',
                                    'Pengajuan tender telah selesai direviu oleh semua Pokja Pemilihan. Semua file telah disetujui.',
                                    route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to pokja: " . $e->getMessage());
                }

                // Notifikasi ke Kepala UKPBJ
                try {
                    $kepalaUKPBJs = \App\Models\User::where('role', 'kepalaukpbj')->get();
                    foreach ($kepalaUKPBJs as $kepala) {
                        $kepala->notify(new \App\Notifications\PengajuanUntukPpkNotification(
                            'Pengajuan Tender Siap untuk Persetujuan Akhir',
                            'Pengajuan tender telah disetujui oleh semua Pokja dan menunggu persetujuan akhir Anda.',
                            route('kepalaukpbj_pengajuanopen', $pengajuan->id)
                        ));
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending notification to kepala UKPBJ: " . $e->getMessage());
                }
            } else {
                // Jika masih dalam proses (tidak semua pokja selesai)
                // Notifikasi ke pokja lain tentang progress
                $approvedCount = 0;
                $totalPokja = 0;
                
                for ($i = 1; $i <= 3; $i++) {
                    if ($pengajuan->{'pokja' . $i . '_id'}) {
                        $totalPokja++;
                        $pokjaApprovedAll = $allFiles->every(function ($file) use ($i) {
                            return $file->{'pokja' . $i . '_status'} == 1;
                        });
                        if ($pokjaApprovedAll) $approvedCount++;
                    }
                }
                
                // Notifikasi progress ke pokja lain
                try {
                    for ($i = 1; $i <= 3; $i++) {
                        if ($i != $pokjaKe && $pengajuan->{'pokja' . $i . '_id'}) {
                            $pokjaUser = \App\Models\User::find($pengajuan->{'pokja' . $i . '_id'});
                            if ($pokjaUser) {
                                $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                                    'Update Progress Reviu',
                                    'Pokja ' . $pokjaKe . ' telah menyelesaikan reviu file. Progress: ' . $approvedCount . '/' . $totalPokja . ' pokja telah menyelesaikan reviu.',
                                    route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                                ));
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Error sending progress notification to pokja: " . $e->getMessage());
                }
            }
            
            $pengajuan->save();
        }

        return response()->json([
            'success' => true, 
            'message' => 'Status berhasil diupdate',
            'debug_info' => [
                'pokja' => $pokjaKe ?? 'unknown',
                'status_request' => $request->status ?? 'none',
                'files_updated' => count($request->id ?? [])
            ]
        ]);
    }

    public function disposisiPokja(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->kepalaukpbj_id = Auth::user()->id;
        $pengajuan->kepalaukpbj_updated = Carbon::now();
        $pengajuan->kepalaukpbj_status = 1; // Disetujui Kepala UKPBJ
        $pengajuan->status = 0;
        $pengajuan->save();

        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil didisposisi ke Pokja Pemilihan.']);
    }

    public function tolakPengajuan(Request $request, $id)
    {
        $pengajuan = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3'])->findOrFail($id);
        if(Auth::user()->role == 'kepalaukpbj' && $pengajuan->kepalaukpbj_status == 0) {
            $pengajuan->kepalaukpbj_id = Auth::user()->id;
            $pengajuan->kepalaukpbj_updated = Carbon::now();
            $pengajuan->kepalaukpbj_status = 2; // Ditolak Kepala UKPBJ
            $pengajuan->status = 22; // 2 = Tidak Disetujui
            $pengajuan->save();

            // Notifikasi ke user pengajuan
            try {
                if ($pengajuan->user) {
                    $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                        'Pengajuan Tender Tidak Diterima Kepala UKPBJ',
                        'Mohon maaf, pengajuan tender Anda tidak diterima oleh Kepala UKPBJ. Silakan cek kembali dokumen dan persyaratan, lalu ajukan ulang jika diperlukan. Terima kasih atas perhatian dan kerja samanya.',
                        route('ppk_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to user: " . $e->getMessage());
            }

            // Notifikasi ke pokja jika ada yang ter-assign
            try {
                for ($i = 1; $i <= 3; $i++) {
                    if ($pengajuan->{'pokja' . $i . '_id'}) {
                        $pokjaUser = \App\Models\User::find($pengajuan->{'pokja' . $i . '_id'});
                        if ($pokjaUser) {
                            $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                                'Pengajuan Tender Ditolak',
                                'Pengajuan tender yang sedang Anda reviu telah ditolak oleh Kepala UKPBJ.',
                                route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                            ));
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to pokja: " . $e->getMessage());
            }
        }elseif(Auth::user()->role == 'verifikator' && $pengajuan->verifikator_status_akhir == 0) {
            $pengajuan->status = 12; // 2 = Tidak Disetujui
            $pengajuan->verifikator_id = Auth::user()->id;
            $pengajuan->verifikator_updated_akhir = Carbon::now();
            $pengajuan->verifikator_status_akhir = 2; // Ditolak Verifikator
            $pengajuan->pesan_akhir = $request->pesan_akhir;
            $pengajuan->save();

            // Notifikasi ke user pengajuan
            try {
                if ($pengajuan->user) {
                    $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                        'Pengajuan Tender Tidak Diterima Kepala Verifikator',
                        'Mohon maaf, pengajuan tender Anda tidak diterima oleh Kepala Verifikator. Silakan cek kembali dokumen dan persyaratan, lalu ajukan ulang jika diperlukan. Terima kasih atas perhatian dan kerja samanya.',
                        route('ppk_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to user: " . $e->getMessage());
            }

            // Notifikasi ke Kepala UKPBJ jika ada
            try {
                $kepalaUKPBJs = \App\Models\User::where('role', 'kepalaukpbj')->get();
                foreach ($kepalaUKPBJs as $kepala) {
                    $kepala->notify(new \App\Notifications\PengajuanNotification(
                        'Pengajuan Tender Ditolak Verifikator',
                        'Pengajuan tender telah ditolak oleh Verifikator dan tidak akan diteruskan ke tahap selanjutnya.',
                        route('kepalaukpbj_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to kepala UKPBJ: " . $e->getMessage());
            }
        }
        return response()->json(['success' => true, 'pesan' => 'Pengajuan berhasil ditolak.']);
    }

    public function getPokja()
    {
        $pokja = User::select('id', 'name', 'nip', 'nik', 'jabatan')->where('role', 'pokjapemilihan')->get();

        return response()->json($pokja);
    }
    
    public function kirimPokja(Request $request)
    {
        $request->validate([
            'pokja_ids' => 'required|array|min:3|max:3',
            'pokja_ids.*' => 'exists:users,id', // pastikan id pokja valid
            'pengajuan_id' => 'required|exists:pengajuans,id'
        ]);

        $pengajuan = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3'])->find($request->pengajuan_id);
        $pengajuan->status = 21; // Status 21 = Sudah dikirim ke Pokja Pemilihan
        $pengajuan->kepalaukpbj_id = Auth::user()->id;
        $pengajuan->kepalaukpbj_status = 1;
        $pengajuan->kepalaukpbj_updated = Carbon::now();

        // Simpan data baru & notifikasi ke pokja terpilih
        $key = 1;
        foreach ($request->pokja_ids as $pokjaId) {
            $pokja_id = 'pokja' . $key . '_id';
            $pokja_status = 'pokja' . $key . '_status_akhir';
            $pengajuan->{$pokja_id} = $pokjaId;
            $pengajuan->{$pokja_status} = 0;

            // Notifikasi ke Pokja terpilih
            try {
                $pokjaUser = \App\Models\User::find($pokjaId);
                if ($pokjaUser) {
                    $pesan = 'Selamat, Anda telah dipilih oleh Kepala UKPBJ sebagai anggota Pokja Pemilihan untuk paket "' . $pengajuan->nama_paket . '". Silakan cek detail tugas Anda di sistem.';
                    $pokjaUser->notify(new \App\Notifications\PengajuanNotification(
                        'Penunjukan Pokja Pemilihan',
                        $pesan,
                        route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to pokja {$pokjaId}: " . $e->getMessage());
            }

            ++$key;
        }
        $pengajuan->save();

        return response()->json(['message' => 'Pokja berhasil dipilih.']);
    }

    public function selesaiReviu(Request $request, $id)
    {
        $pengajuan = Pengajuan::with(['user', 'pokja1', 'pokja2', 'pokja3'])->findOrFail($id);
        
        // Pastikan user adalah pokja yang ter-assign
        $pokjaKe = null;
        if ($pengajuan->pokja1_id == Auth::user()->id) {
            $pokjaKe = 1;
        } elseif ($pengajuan->pokja2_id == Auth::user()->id) {
            $pokjaKe = 2;
        } elseif ($pengajuan->pokja3_id == Auth::user()->id) {
            $pokjaKe = 3;
        }

        if (!$pokjaKe) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk pokja ini.']);
        }

        // Cek apakah semua file sudah disetujui oleh pokja ini
        $allFiles = PengajuanFile::where('pengajuan_id', $pengajuan->id)
            ->where('status', '!=', 99)->get();

        $statusField = 'pokja' . $pokjaKe . '_status';
        $allApprovedByThisPokja = $allFiles->every(function ($file) use ($statusField) {
            return $file->$statusField == 1;
        });

        if (!$allApprovedByThisPokja) {
            return response()->json([
                'success' => false, 
                'message' => 'Masih ada file yang belum Anda setujui. Silakan setujui semua file terlebih dahulu.'
            ]);
        }

        // Update status pokja_status_akhir menjadi 2 (selesai reviu)
        $statusAkhirField = 'pokja' . $pokjaKe . '_status_akhir';
        $pengajuan->$statusAkhirField = 2;
        $pengajuan->save();

        // Notifikasi ke user bahwa pokja telah selesai reviu
        try {
            $pokjaUser = Auth::user();
            if ($pengajuan->user) {
                $pengajuan->user->notify(new \App\Notifications\PengajuanNotification(
                    'Pokja ' . $pokjaKe . ' Selesai Reviu',
                    'Pokja Pemilihan ' . $pokjaKe . ' (' . $pokjaUser->name . ') telah menyelesaikan reviu pengajuan tender Anda.',
                    route('ppk_pengajuanopen', $pengajuan->id)
                ));
            }
        } catch (\Exception $e) {
            \Log::error("Error sending notification to user: " . $e->getMessage());
        }

        // Notifikasi ke pokja lain tentang penyelesaian reviu
        try {
            for ($i = 1; $i <= 3; $i++) {
                if ($i != $pokjaKe && $pengajuan->{'pokja' . $i . '_id'}) {
                    $pokjaLain = \App\Models\User::find($pengajuan->{'pokja' . $i . '_id'});
                    if ($pokjaLain) {
                        $pokjaLain->notify(new \App\Notifications\PengajuanNotification(
                            'Update Progress Reviu',
                            'Pokja ' . $pokjaKe . ' (' . $pokjaUser->name . ') telah menyelesaikan reviu. Silakan segera selesaikan reviu Anda jika belum.',
                            route('pokjapemilihan_pengajuanopen', $pengajuan->id)
                        ));
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error sending notification to pokja: " . $e->getMessage());
        }

        // Cek apakah semua pokja sudah selesai reviu
        $allPokjaSelesai = true;
        for ($i = 1; $i <= 3; $i++) {
            $pokjaIdField = 'pokja' . $i . '_id';
            $pokjaStatusAkhirField = 'pokja' . $i . '_status_akhir';
            
            if ($pengajuan->$pokjaIdField && $pengajuan->$pokjaStatusAkhirField != 2) {
                $allPokjaSelesai = false;
                break;
            }
        }

        if ($allPokjaSelesai) {
            // Jika semua pokja sudah selesai reviu, update status pengajuan
            $pengajuan->status = 31; // Status selesai reviu semua pokja
            $pengajuan->save();

            // Notifikasi ke user pengajuan
            try {
                if ($pengajuan->user) {
                    $pengajuan->user->notify(new \App\Notifications\PengajuanDisetujui(
                        'Pengajuan Tender Selesai Direviu',
                        'Selamat! Semua Pokja Pemilihan telah menyelesaikan reviu pengajuan tender Anda.',
                        route('ppk_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to user: " . $e->getMessage());
            }

            // Notifikasi ke Kepala UKPBJ
            try {
                $kepalaUKPBJs = \App\Models\User::where('role', 'kepalaukpbj')->get();
                foreach ($kepalaUKPBJs as $kepala) {
                    $kepala->notify(new \App\Notifications\PengajuanUntukPpkNotification(
                        'Pengajuan Tender Selesai Direviu Pokja',
                        'Pengajuan tender telah selesai direviu oleh semua Pokja Pemilihan dan siap untuk tahap selanjutnya.',
                        route('kepalaukpbj_pengajuanopen', $pengajuan->id)
                    ));
                }
            } catch (\Exception $e) {
                \Log::error("Error sending notification to kepala UKPBJ: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Reviu Anda telah selesai dan berhasil disimpan.',
            'all_pokja_selesai' => $allPokjaSelesai
        ]);
    }
}
