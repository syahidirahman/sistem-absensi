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
        $pegawai = User::where('role', 'pegawai')->get();
        $setting = \App\Models\Setting::first(); // Ambil pengaturan
        $query = Attendance::with('user');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('check_in_time', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('check_in_time', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('pegawai_id')) {
            $query->where('user_id', $request->pegawai_id);
        }
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

        $attendances = $query->orderBy('check_in_time', 'desc')->paginate(10);

        return view('admin.absensi.index', compact('attendances', 'pegawai', 'request', 'setting'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('admin.absensi.show', compact('attendance'));
    }
}