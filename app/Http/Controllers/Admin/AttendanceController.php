<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua pegawai untuk dropdown
        $pegawai = User::where('role', 'pegawai')->get();
        
        // Ambil setting untuk jam masuk default
        $setting = Setting::first();

        // Query absensi
        $query = Attendance::with('user');

        // Filter tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('check_in_time', '>=', $request->tanggal_mulai);
        }

        // Filter tanggal selesai
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('check_in_time', '<=', $request->tanggal_selesai);
        }

        // Filter pegawai
        if ($request->filled('pegawai_id')) {
            $query->where('user_id', $request->pegawai_id);
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status == 'hadir') {
                $query->whereTime('check_in_time', '<=', '08:00:00');
            } elseif ($request->status == 'terlambat') {
                $query->whereTime('check_in_time', '>', '08:00:00');
            } elseif ($request->status == 'selesai') {
                $query->whereNotNull('check_out_time');
            } elseif ($request->status == 'belum_checkout') {
                $query->whereNull('check_out_time');
            }
        }

        // Urutkan dari yang terbaru
        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(10);

        return view('admin.absensi.index', compact('attendances', 'pegawai', 'request', 'setting'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.absensi.show', compact('attendance'));
    }
}