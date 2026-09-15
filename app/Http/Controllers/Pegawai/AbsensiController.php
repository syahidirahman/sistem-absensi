<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in_time', $today)
                                ->first();

        $isCheckedIn = $attendance && !$attendance->check_out_time;
        $isCheckedOut = $attendance && $attendance->check_out_time;

        return view('pegawai.absensi', compact('isCheckedIn', 'isCheckedOut'));
    }
}