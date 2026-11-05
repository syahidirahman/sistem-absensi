<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\FaceEmbedding;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // ============================================
    // HAVERSINE DISTANCE (Menghitung Jarak)
    // ============================================
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // ============================================
    // EUCLIDEAN DISTANCE (Face Recognition)
    // ============================================
    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    // ============================================
    // VERIFIKASI WAJAH (Hanya untuk user login)
    // ============================================
    private function verifyFaceForLoggedInUser($descriptor)
    {
        $user = auth()->user();
        if (!$user) {
            Log::warning('Tidak ada user yang login');
            return null;
        }

        $faceData = FaceEmbedding::where('user_id', $user->id)->first();
        if (!$faceData || !$faceData->descriptor) {
            Log::warning('User tidak memiliki data wajah', ['user' => $user->name]);
            return null;
        }

        $distance = $this->euclideanDistance($descriptor, $faceData->descriptor);
        $threshold = 0.45; // Batas toleransi kecocokan wajah

        Log::info('Face verification', [
            'user' => $user->name,
            'distance' => $distance,
            'accepted' => $distance < $threshold ? 'YES' : 'NO'
        ]);

        return ($distance < $threshold) ? $user : null;
    }

    // ============================================
    // CHECK-IN
    // ============================================
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'descriptor' => 'required|array',
            'photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. Verifikasi wajah
        $user = $this->verifyFaceForLoggedInUser($request->descriptor);
        if (!$user) {
            return response()->json(['message' => 'Wajah tidak cocok atau belum terdaftar'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Akun tidak aktif'], 403);
        }

        // 2. Ambil Pengaturan & Data Kantor
        $setting = Setting::first();
        $office = Office::first();

        // 3. Validasi Jam Masuk & Jam Pulang
        if ($setting && $setting->jam_masuk) {
            $jamMasuk = \Carbon\Carbon::parse($setting->jam_masuk)->format('H:i:s');
            $jamPulang = \Carbon\Carbon::parse($setting->jam_pulang)->format('H:i:s');
            $sekarang = now()->format('H:i:s');
            
            if ($sekarang < $jamMasuk) {
                return response()->json([
                    'message' => "Belum masuk jam kerja. Presensi baru bisa dilakukan mulai pukul " . \Carbon\Carbon::parse($setting->jam_masuk)->format('H:i') . "."
                ], 403);
            }

            if ($sekarang > $jamPulang) {
                return response()->json([
                    'message' => "Jam kerja sudah berakhir pukul " . \Carbon\Carbon::parse($setting->jam_pulang)->format('H:i') . ". Anda tidak bisa presensi masuk lagi."
                ], 403);
            }
        }

        // 4. Hitung Jarak & Validasi Radius
        $office_lat = $setting->office_latitude ?? ($office->latitude ?? 0);
        $office_lng = $setting->office_longitude ?? ($office->longitude ?? 0);
        $radius = $setting->radius_default ?? ($office->radius ?? 100);

        $distance = $this->haversineDistance(
            $request->latitude,
            $request->longitude,
            $office_lat,
            $office_lng
        );

        if ($distance > $radius) {
            return response()->json([
                'message' => 'Di luar radius kantor',
                'distance' => round($distance, 2),
                'radius' => $radius
            ], 403);
        }

        // 5. Cek apakah sudah check-in hari ini
        $today = now()->toDateString();
        $existing = Attendance::where('user_id', $user->id)
                              ->whereDate('check_in_time', $today)
                              ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah check-in hari ini'], 409);
        }

        // 6. Simpan ke Database
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'check_in_time' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'face_verified' => true,
        ]);

        // 7. Simpan Foto Check-In (Aman dari Error)
        if ($request->photo) {
            try {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo));
                $filename = 'checkin_' . $user->id . '_' . time() . '.jpg';
                Storage::disk('public')->put('attendance_photos/' . $filename, $imageData);
                $attendance->photo_in = 'attendance_photos/' . $filename;
                $attendance->save();
            } catch (\Exception $e) {
                \Log::error('Gagal simpan foto in: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Check-in berhasil!',
            'attendance' => $attendance,
            'distance' => round($distance, 2)
        ], 201);
    }

    // ============================================
    // CHECK-OUT
    // ============================================
    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'descriptor' => 'required|array',
            'photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 1. Verifikasi wajah
        $user = $this->verifyFaceForLoggedInUser($request->descriptor);
        if (!$user) {
            return response()->json(['message' => 'Wajah tidak cocok atau belum terdaftar'], 401);
        }

        // 2. Cari data check-in hari ini
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in_time', $today)
                                ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum check-in hari ini'], 404);
        }

        if ($attendance->check_out_time) {
            return response()->json(['message' => 'Anda sudah check-out'], 409);
        }

        // 3. Validasi Radius Check-Out
        $setting = Setting::first();
        $office = Office::first();
        $office_lat = $setting->office_latitude ?? ($office->latitude ?? 0);
        $office_lng = $setting->office_longitude ?? ($office->longitude ?? 0);
        $radius = $setting->radius_default ?? ($office->radius ?? 100);

        $distance = $this->haversineDistance(
            $request->latitude,
            $request->longitude,
            $office_lat,
            $office_lng
        );

        if ($distance > $radius) {
            return response()->json([
                'message' => 'Di luar radius untuk check-out',
                'distance' => round($distance, 2),
                'radius' => $radius
            ], 403);
        }

        // 4. Simpan Waktu Check-Out
        $attendance->check_out_time = now();
        $attendance->save();

        // 5. Simpan Foto Check-Out (Aman dari Error)
        if ($request->photo) {
            try {
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo));
                $filename = 'checkout_' . $user->id . '_' . time() . '.jpg';
                Storage::disk('public')->put('attendance_photos/' . $filename, $imageData);
                $attendance->photo_out = 'attendance_photos/' . $filename;
                $attendance->save();
            } catch (\Exception $e) {
                \Log::error('Gagal simpan foto out: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance
        ]);
    }

    // ============================================
    // VERIFY WAJAH (Auto-Verify)
    // ============================================
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descriptor' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['valid' => false, 'message' => 'Data tidak lengkap'], 422);
        }

        $user = $this->verifyFaceForLoggedInUser($request->descriptor);
        if ($user) {
            return response()->json(['valid' => true, 'user' => $user->name]);
        }

        return response()->json(['valid' => false, 'message' => 'Wajah tidak cocok'], 401);
    }
}