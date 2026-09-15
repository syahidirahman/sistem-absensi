<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('pegawai.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'foto_profil_crop' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update data
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload foto dari hasil crop (base64)
        if ($request->filled('foto_profil_crop')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // Decode base64
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_profil_crop));
            $filename = 'profile_' . $user->id . '_' . time() . '.png';
            Storage::disk('public')->put('profile_photos/' . $filename, $imageData);
            $user->foto_profil = 'profile_photos/' . $filename;
        }

        $user->save();

        return redirect()->route('pegawai.profile.index')
                         ->with('success', 'Profil berhasil diperbarui!');
    }
}