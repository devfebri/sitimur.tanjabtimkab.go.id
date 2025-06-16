<?php

namespace App\Http\Controllers;

use App\Models\MetodePengadaan;
use App\Models\MetodePengadaanBerkas;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class PersyaratanController extends Controller
{
    public function index(Request $request)
    {
        $data = MetodePengadaan::all();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '
                    <button class="btn btn-primary btn-sm open-post" data-id="'.$row->id.'">Open</button>
                    <button class="btn btn-warning btn-sm edit-post" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm delete-post" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('persyaratan.index', compact('data'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
       
       
$slug = Str::slug($request->nama_metode_pengadaan, '_');
$count = MetodePengadaan::where('slug', 'LIKE', "{$slug}%")->count();
$newCount = $count > 0 ? ++$count : '';
$dataslug = $newCount > 0 ? "{$slug}_{$newCount}" : $slug;
        if ($id) {
            // dd('ok');
            $data = MetodePengadaan::find($id);
            $data->update([
                'nama_metode_pengadaan'     => $request->nama_metode_pengadaan,
                'status'                    => $request->statuss,
                'slug'                      => $dataslug
            ]);
        } else {
            $data                           = new MetodePengadaan;
            $data->nama_metode_pengadaan    = $request->nama_metode_pengadaan;
            $data->status                   = $request->statuss;
            $data->slug                     = $dataslug;
            $data->save();
        }
        return response()->json($data);
    }

    public function edit($id)
    {
        $data = MetodePengadaan::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_metode_pengadaan' => 'required',
            'status' => 'required'
        ]);
        $data = MetodePengadaan::findOrFail($id);
        $data->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        MetodePengadaan::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function open($id, Request $request)
    {
        $data = MetodePengadaan::findOrFail($id);

        if ($request->ajax()) {
            $berkas = MetodePengadaanBerkas::where('metode_pengadaan_id', $id)->get();
            return DataTables::of($berkas)
                
                ->addColumn('status', function($row){
                    return $row->status == 1 ? 'Active' : 'Inactive';
                })
                ->addColumn('multiple', function ($row) {
                    return $row->multiple == 1 ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function($row){
                    return '
                    <button class="btn btn-warning btn-sm edit-post" data-id="'.$row->id.'">Edit</button>
                    <button class="btn btn-danger btn-sm delete-post" data-id="'.$row->id.'">Hapus</button>
                    ';
                })
                ->rawColumns(['action', 'status', 'multiple'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('persyaratan.open', compact('data'));
    }

    public function berkasStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'metode_pengadaan_id' => 'required|exists:metode_pengadaans,id',
            'nama_berkas' => 'required',
            'statuss' => 'required'
        ]);

        $slug = Str::slug($request->nama_berkas, '_');
        $count = MetodePengadaanBerkas::where('slug', 'LIKE', "{$slug}%")->count();
        $newCount = $count > 0 ? ++$count : '';
        $dataslug = $newCount > 0 ? "{$slug}_{$newCount}" : $slug;

        if ($request->id) {
            $berkas = MetodePengadaanBerkas::findOrFail($request->id);
            $berkas->update([
                'nama_berkas' => $request->nama_berkas,
                'multiple' => $request->multiple ,
                'status' => $request->statuss,
                'slug' => $dataslug,
            ]);
        } else {
            $berkas = MetodePengadaanBerkas::create([
                'metode_pengadaan_id' => $request->metode_pengadaan_id,
                'nama_berkas' => $request->nama_berkas,
                'multiple' => $request->multiple,
                'status' => $request->statuss,
                'slug' => $dataslug,
            ]);
        }
        return response()->json(['success' => true, 'data' => $berkas]);
    }

    public function berkasEdit($id)
    {
        $berkas = MetodePengadaanBerkas::findOrFail($id);
        return response()->json($berkas);
    }

    public function berkasDestroy($id)
    {
        MetodePengadaanBerkas::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
