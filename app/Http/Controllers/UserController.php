<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function getUserData(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'username',
            2 => 'name',
            3 => 'role',
            4 => 'nohp',
            5 => 'jabatan',
            6 => 'pangkat',
            7 => 'jk',
            8 => 'nip',
            9 => 'nik',
            10 => 'akses'
        ];

        $query = DB::table('users')
            ->select('id', 'username', 'name', 'role', 'nohp', 'jabatan', 'pangkat', 'jk', 'nip', 'nik', 'akses');

        // Search
        $search = $request->input('search.value');
        if ($search && strlen($search) >= 3) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Total records sebelum filter
        $recordsTotal = DB::table('users')->count();

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
            $actionButtons = '';
            if (Auth::user()->role == 'admin') {
                $actionButtons = '
                    <button class="btn btn-warning btn-sm edit-post" data-id="'.$row->id.'">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    
                ';
            }

            $result[] = [
                'no' => $no++,
                'username' => $row->username,
                'name' => $row->name,
                'role' => $row->role,
                'nohp' => $row->nohp ?? '-',
                'jabatan' => $row->jabatan ?? '-',
                'pangkat' => $row->pangkat ?? '-',
                'jk' => $row->jk ?? '-',
                'nip' => $row->nip ?? '-',
                'nik' => $row->nik ?? '-',
                'status' => $row->akses == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>',
                'action' => $actionButtons
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result
        ]);
    }

    public function create(Request $request)
    {
        $id = $request->id;
        if ($id) {
            // Update existing user
            $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required|string',
            ]);

            $user = User::find($id);
            
            $updateData = [
                'name' => $request->name,
                'role' => $request->role,
                'nohp' => $request->nohp,
                'jabatan' => $request->jabatan,
                'pangkat' => $request->pangkat,
                'jk' => $request->jk,
                'nip' => $request->nip,
                'nik' => $request->nik,
                'akses' => $request->statuss ?? 1,
            ];

            // Only update password if provided
            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);
        } else {
            // Create new user
            $request->validate([
                'username' => 'required|unique:users,username',
                'name' => 'required|string|max:255',
                'role' => 'required|string',
                'password' => 'required|min:6|confirmed',
            ]);

            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'role' => $request->role,
                'password' => bcrypt($request->password),
                'nohp' => $request->nohp,
                'jabatan' => $request->jabatan,
                'pangkat' => $request->pangkat,
                'jk' => $request->jk,
                'nip' => $request->nip,
                'nik' => $request->nik,
                'akses' => $request->statuss ?? 1,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => $id ? 'User berhasil diupdate' : 'User berhasil ditambahkan',
            'data' => $user
        ]);
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Soft delete - set akses to 0 instead of actual deletion
            $user->update(['akses' => 0]);
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus user'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user'
            ], 500);
        }
    }
    public function checkUsername(Request $request)
    {
        $exists = User::where('username', $request->username)
            ->where('id', '!=', $request->id)
            ->exists();
        return response()->json(!$exists); // true jika unik, false jika sudah ada
    }
}
