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
    public function index(Request $request)
    {
        $data = Pengajuan::all();
        $metodepengadaan = MetodePengadaan::where('status', 1)->get();
        
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-primary btn-sm open-post" data-id="' . $row->id . '">Open</button>
                        <button class="btn btn-danger btn-sm delete-post" data-id="' . $row->id . '">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('dashboard.index',compact('data', 'metodepengadaan'));
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
                ->addColumn('action', function ($row) {
                $data = Pengajuan::findOrFail($row->pengajuan_id);
                

                    return '
                    <button class="btn btn-warning btn-sm edit-post" data-id="' . $row->id . '">Edit</button>
                    <a href="' . asset('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $row->slug . '/' . $row->file_path) . '" target="_blank" class="btn btn-sm btn-info"><span class="ti-import"></span></a>
                    ';
                })
                ->rawColumns(['action'])
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

        // Simpan file ke tabel pengajuan_files
        foreach ($berkasList as $berkas) {
            $inputName = $berkas->slug . ($berkas->multiple == 1 ? '' : '');
            if ($request->hasFile($inputName)) {
                $files = $request->file($inputName);
                if ($berkas->multiple == 1 && is_array($files)) {
                    foreach ($files as $file) {
                        $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

                        // Simpan ke tabel pengajuan_files
                        \DB::table('pengajuan_files')->insert([
                            'pengajuan_id' => $data->id,
                            'nama_file' => $berkas->nama_berkas,
                            'mutliple' => $berkas->multiple,
                            'slug' => $berkas->slug,
                            'file_path' => $filename,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    $file = is_array($files) ? $files[0] : $files;
                    $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('pengajuan/' . Carbon::now()->format('d-m-Y') . '/' . auth()->user()->username . '/' . $data->id . '/' . $berkas->slug), $filename);

                    // Simpan ke tabel pengajuan_files
                    \DB::table('pengajuan_files')->insert([
                        'pengajuan_id' => $data->id,
                        'nama_file' => $berkas->nama_berkas,
                        'mutliple' => $berkas->multiple,
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
        // Validasi
        // dd($request->all());
        $request->validate([
            'statuss' => 'required|in:0,1,2',
            'pesan' => 'nullable|string',
            'file_upload' => 'nullable|file|max:2048', // max 2MB, sesuaikan jika perlu
        ]);

        // Update status dan pesan
        $file->status = $request->statuss;
        $file->pesan = $request->pesan;

        // Jika ada file baru diupload
        if ($request->hasFile('file_upload')) {
            // Hapus file lama jika ada
            $oldPath = public_path('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $file->slug . '/' . $file->file_path);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }

            // Simpan file baru
            $uploaded = $request->file('file_upload');
            $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $uploaded->move(public_path('pengajuan/' . $data->created_at->format('d-m-Y') . '/' . $data->user->name . '/' . $data->id . '/' . $file->slug ), $filename);

            // Update nama file di database
            $file->file_path = $filename;
        }

        $file->save();

        return response()->json(['success' => true, 'pesan' => 'File pengajuan berhasil diupdate.']);
    }
}
