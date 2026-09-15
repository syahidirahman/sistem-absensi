<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendanceToday = Attendance::where('user_id', $user->id)
                                     ->whereDate('check_in_time', $today)
                                     ->first();

        $status = 'Belum absen';
        if ($attendanceToday) {
            if ($attendanceToday->check_out_time) {
                $status = 'Sudah Check-In & Check-Out';
            } else {
                $status = 'Sudah Check-In, belum Check-Out';
            }
        }

        $histories = Attendance::where('user_id', $user->id)
                               ->orderBy('check_in_time', 'desc')
                               ->limit(7)
                               ->get();

        return view('pegawai.dashboard', compact('status', 'histories'));
    }
}