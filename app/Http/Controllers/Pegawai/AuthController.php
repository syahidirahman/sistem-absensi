<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pegawai.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            return back()->withErrors(['nip' => 'NIP tidak ditemukan'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah'])->withInput();
        }

        if ($user->role !== 'pegawai') {
            return back()->withErrors(['nip' => 'Akses ditolak. Akun ini bukan Pegawai. Silakan login melalui halaman Login Admin.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['nip' => 'Akun Anda dinonaktifkan. Hubungi admin.'])->withInput();
        }

        // === LOGIKA REMEMBER ME ===
        $remember = $request->has('remember'); // Cek apakah checkbox dicentang
        
        Auth::login($user, $remember); // Masukkan parameter $remember

        $request->session()->regenerate(); // Amankan session

        return redirect()->route('pegawai.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/pegawai/login');
    }
}