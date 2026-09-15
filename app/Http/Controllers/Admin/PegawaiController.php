<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = User::where('role', '!=', 'admin') // atau tampilkan semua, sesuaikan
                        ->orderBy('created_at', 'desc')
                        ->get();
        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:users,nip',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,pegawai',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'nip', 'email', 'role']);
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = true;

        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profile_photos', 'public');
            $data['foto_profil'] = $path;
        }

        User::create($data);

        return redirect()->route('admin.pegawai.index')->with('success', 'Akun berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pegawai = User::findOrFail($id); // atau User::where('role', 'pegawai')->findOrFail($id)
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:users,nip,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,pegawai',
            'password' => 'nullable|string|min:8',
            'foto_profil' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'nip', 'email', 'role', 'is_active']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profile_photos', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return redirect()->route('admin.pegawai.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Cegah menghapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.pegawai.index')->with('success', 'Akun berhasil dihapus.');
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }
        $user->is_active = !$user->is_active;
        $user->save();
        return redirect()->route('admin.pegawai.index')->with('success', 'Status akun berhasil diubah.');
    }
}