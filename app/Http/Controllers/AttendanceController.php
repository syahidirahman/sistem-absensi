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
    // HAVERSINE DISTANCE
    // ============================================
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    // ============================================
    // EUCLIDEAN DISTANCE
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
    // VERIFY FACE FOR LOGGED IN USER
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
        $threshold = 0.35;

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

        // 2. Cek radius (dari setting atau office)
        $office = Office::first();
        $setting = Setting::first();

        $radius = $setting ? $setting->radius_default : ($office->radius ?? 100);

        $distance = $this->haversineDistance(
            $request->latitude,
            $request->longitude,
            $office->latitude ?? 0,
            $office->longitude ?? 0
        );

        if ($distance > $radius) {
            return response()->json([
                'message' => 'Di luar radius kantor',
                'distance' => $distance,
                'radius' => $radius
            ], 403);
        }

        // 3. Cek apakah sudah check-in hari ini
        $today = now()->toDateString();
        $existing = Attendance::where('user_id', $user->id)
                              ->whereDate('check_in_time', $today)
                              ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah check-in hari ini'], 409);
        }

        // 4. Cek keterlambatan
        $status = 'Hadir';
        if ($setting) {
            $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $setting->jam_masuk);
            $toleransi = $setting->toleransi_keterlambatan;
            $batasTelat = $jamMasuk->copy()->addMinutes($toleransi);
            if (now() > $batasTelat) {
                $status = 'Terlambat';
            }
        }

        // 5. Simpan absensi
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'check_in_time' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'face_verified' => true,
        ]);

        // 6. Simpan foto check-in
        if ($request->photo) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo));
            $filename = 'checkin_' . $user->id . '_' . time() . '.png';
            Storage::disk('public')->put('attendance_photos/' . $filename, $imageData);
            $attendance->photo_in = 'attendance_photos/' . $filename;
            $attendance->save();
        }

        return response()->json([
            'message' => 'Check-in berhasil!',
            'attendance' => $attendance,
            'distance' => $distance,
            'status' => $status
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

        // 2. Cari absensi hari ini
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

        // 3. Validasi radius (opsional)
        $office = Office::first();
        $setting = Setting::first();
        $radius = $setting ? $setting->radius_default : ($office->radius ?? 100);

        $distance = $this->haversineDistance(
            $request->latitude,
            $request->longitude,
            $office->latitude ?? 0,
            $office->longitude ?? 0
        );

        if ($distance > $radius) {
            return response()->json([
                'message' => 'Di luar radius untuk check-out',
                'distance' => $distance,
                'radius' => $radius
            ], 403);
        }

        // 4. Simpan check-out
        $attendance->check_out_time = now();
        $attendance->save();

        // 5. Simpan foto check-out
        if ($request->photo) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->photo));
            $filename = 'checkout_' . $user->id . '_' . time() . '.png';
            Storage::disk('public')->put('attendance_photos/' . $filename, $imageData);
            $attendance->photo_out = 'attendance_photos/' . $filename;
            $attendance->save();
        }

        return response()->json([
            'message' => 'Check-out berhasil!',
            'attendance' => $attendance
        ]);
    }

    // ============================================
    // VERIFY (AUTO-VERIFY)
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