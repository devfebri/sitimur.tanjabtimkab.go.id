<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use datatables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->get();
       
        if ($request->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($f) {
                    $button = '<div class="tabledit-toolbar btn-toolbar" style="text-align: center;">';
                    $button .= '<div class="btn-group btn-group-sm" style="float: none;">';
                    $button .= '<button class="tabledit-edit-button btn btn-sm btn-warning edit-post" data-id=' . $f->id . ' id="alertify-success" style="float: none; margin: 5px;"><span class="ti-pencil"></span></button>';
                    // $button .= '<a href="#" target="_blank" style="margin: 5px;" class="tabledit-edit-button btn btn-sm btn-primary"><span class="ti-shift-right"></span></a>';
                    $button.='<button class="tabledit-delete-button btn btn-sm btn-danger delete" data-id='.$f->id.' style="float: none; margin: 5px;"><span class="ti-trash"></span></button>';
                    $button .= '</div>';
                    $button .= '</div>';

                    return $button;
                })->addColumn('status', function ($f) {

                    if ($f->akses == 1) {

                        $status = '<span class="badge badge-pill badge-primary"><b><i>active</i></b></span>';
                    } elseif ($f->akses == 0) {
                        $status = '<span class="badge badge-pill badge-danger"><b><i>not active</i></b></span>';
                    }
                    return $status;
                })
                ->rawColumns(['action',  'status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('user.index', compact('data'));
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
