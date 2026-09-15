<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FaceEmbedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceController extends Controller
{
    // Tampilkan halaman registrasi wajah
    public function register($id)
    {
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);
        $hasFace = $pegawai->faceEmbedding()->exists();
        return view('admin.pegawai.register-face', compact('pegawai', 'hasFace'));
    }

    // Simpan deskriptor wajah
    public function store(Request $request, $id)
{
    try {
        // Validasi (photo tidak wajib)
        $request->validate([
            'descriptor' => 'required|array',
        ]);

        // Cari pegawai
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);

        // Hapus data lama jika ada
        if ($pegawai->faceEmbedding) {
            if ($pegawai->faceEmbedding->photo_path) {
                // Hapus file foto jika ada (tapi kita skip)
            }
            $pegawai->faceEmbedding->delete();
        }

        // Simpan ke database (tanpa foto)
        FaceEmbedding::create([
            'user_id' => $pegawai->id,
            'descriptor' => $request->descriptor,
            'photo_path' => null, // TIDAK simpan foto
        ]);

        return response()->json([
            'message' => 'Wajah berhasil didaftarkan!'
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Hapus data wajah
    public function destroy($id)
    {
        try {
            $pegawai = User::where('role', 'pegawai')->findOrFail($id);
            if ($pegawai->faceEmbedding) {
                if ($pegawai->faceEmbedding->photo_path) {
                    Storage::disk('public')->delete($pegawai->faceEmbedding->photo_path);
                }
                $pegawai->faceEmbedding->delete();
            }
            return redirect()->back()->with('success', 'Data wajah berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal hapus data: ' . $e->getMessage());
        }
    }
}