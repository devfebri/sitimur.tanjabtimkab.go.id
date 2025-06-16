<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function proses_login(Request $request)
    {
        if (!$request->has('_token') || $request->session()->token() !== $request->_token) {
            return redirect('/')->with('gagal', 'Session expired, silakan refresh halaman.');
        }
        // dd($request->all());
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            if(auth()->user()->akses!=1){
                Auth::logout();
                return redirect('/')->with('berhasil', 'Akun anda sedang dalam proses verifikasi oleh admin');
            }else{
                if(auth()->user()->role == 'admin') {
                    return redirect(route('admin_user'))->with('pesan', 'Selamat datang kembali "' .auth()->user()->name . '"');
                } else{

                    return redirect(route(auth()->user()->role . '_dashboard'))->with('pesan', 'Selamat datang kembali "' .auth()->user()->name . '"');
                }
            }
        } else {
            return redirect('/')->with('gagal', 'Periksa Username dan Password anda');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function proses_register(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'nip' => 'required|string|max:50',
            'nik' => 'required|string|max:50',
            'jabatan' => 'required|string|max:100',
            'pangkat' => 'required|string|max:100',
            'nohp' => 'required|string|max:20',
            'jk' => 'required|in:Laki-Laki,Perempuan',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'nik' => $request->nik,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'nohp' => $request->nohp,
            'jk' => $request->jk,
            'role' => 'ppk', // default role
        ]);

        // Auth::login($user);

        return redirect('/')->with('berhasil', 'Register berhasil dan menunggu verifikasi oleh admin');
    }
}
