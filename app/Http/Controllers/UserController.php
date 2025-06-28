<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use datatables;
use Illuminate\Support\Facades\DB;

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
            $result[] = [
                'no' => $no++,
                'username' => $row->username,
                'name' => $row->name,
                'role' => $row->role,
                'nohp' => $row->nohp,
                'jabatan' => $row->jabatan,
                'pangkat' => $row->pangkat,
                'jk' => $row->jk,
                'nip' => $row->nip,
                'nik' => $row->nik,
                'status' => $row->akses == 1 ? 'Active' : 'Inactive',
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
            // dd($request->all());
          
            $user = User::find($id);
            
            $user->update([
                'name' => $request->name,
                'role' => $request->role,
                'akses' => $request->statuss,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);
        } else {
            // dd($request->all());
            $validated = $request->validate([
                'username' => 'required|unique:users,username',
            ]);
            

            $user = new User;
            $user->username = $request->username;
            $user->name = $request->name;
            $user->role = $request->role;
            $user->password = bcrypt($request->password);
            $user->akses    = $request->statuss;
            $user->save();

        }
        return response()->json($user);
    }

    public function delete($id)
    {
        $user = User::find($id);

        $user->delete();
        return response()->json($user);
    }

    public function edit($id)
    {
        $data =  User::find($id);
        return response()->json($data);
    }
    public function checkUsername(Request $request)
    {
        $exists = User::where('username', $request->username)
            ->where('id', '!=', $request->id)
            ->exists();
        return response()->json(!$exists); // true jika unik, false jika sudah ada
    }
}
