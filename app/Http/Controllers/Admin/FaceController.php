<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FaceEmbedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaceController extends Controller
{
    public function register($id)
    {
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);
        $hasFace = $pegawai->faceEmbedding()->exists();
        return view('admin.pegawai.register-face', compact('pegawai', 'hasFace'));
    }

    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'descriptor' => 'required|array',
                'photo' => 'nullable|string', // Menerima base64 image
            ]);

            $pegawai = User::where('role', 'pegawai')->findOrFail($id);

            // Hapus data lama jika ada
            if ($pegawai->faceEmbedding) {
                if ($pegawai->faceEmbedding->photo_path) {
                    Storage::disk('public')->delete($pegawai->faceEmbedding->photo_path);
                }
                $pegawai->faceEmbedding->delete();
            }

            // Simpan Foto Wajah
            $photoPath = null;
            if ($request->photo) {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo));
                $filename = 'face_' . $pegawai->id . '_' . time() . '.png';
                
                // Simpan ke storage/app/public/face_photos
                Storage::disk('public')->put('face_photos/' . $filename, $imageData);
                $photoPath = 'face_photos/' . $filename;
            }

            // Simpan ke database
            FaceEmbedding::create([
                'user_id' => $pegawai->id,
                'descriptor' => $request->descriptor,
                'photo_path' => $photoPath,
            ]);

            return response()->json([
                'message' => 'Wajah dan foto berhasil didaftarkan!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pegawai = User::where('role', 'pegawai')->findOrFail($id);
        if ($pegawai->faceEmbedding) {
            if ($pegawai->faceEmbedding->photo_path) {
                Storage::disk('public')->delete($pegawai->faceEmbedding->photo_path);
            }
            $pegawai->faceEmbedding->delete();
        }
        return redirect()->back()->with('success', 'Data wajah berhasil dihapus');
    }
}