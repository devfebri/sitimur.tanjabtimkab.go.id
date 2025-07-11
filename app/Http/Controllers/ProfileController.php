<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Return the profile view with user data
        return view('profile', compact('user'));
    }

    public function update(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
            'nip' => 'required|string|max:50',
            'nik' => 'required|string|max:50',
            'jabatan' => 'required|string|max:100',
            'pangkat' => 'required|string|max:100',
            'nohp' => 'required|string|max:20',
            'jk' => 'required|in:Laki-Laki,Perempuan',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Update user attributes
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->name = $request->name;
        $user->nip = $request->input('nip');
        $user->nik = $request->input('nik');
        $user->jabatan = $request->input('jabatan');
        $user->pangkat = $request->input('pangkat');
        $user->nohp = $request->input('nohp');
        $user->jk = $request->input('jk');

        // Ubah password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Save the user
        $user->save();

        // Redirect back with success message
        return redirect()->route($user->role.'_profile')->with('pesan', 'Profile updated successfully.');
    }
}
