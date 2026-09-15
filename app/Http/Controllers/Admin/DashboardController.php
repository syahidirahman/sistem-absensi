<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPegawai = User::where('role', 'pegawai')->count();
        $totalHadir = Attendance::whereDate('check_in_time', today())->count();
        $totalTerlambat = Attendance::whereDate('check_in_time', today())
                                    ->whereTime('check_in_time', '>', '08:00:00')
                                    ->count();
        $totalAlpha = $totalPegawai - Attendance::whereDate('check_in_time', today())->distinct('user_id')->count();

        return view('admin.dashboard', compact('totalPegawai', 'totalHadir', 'totalTerlambat', 'totalAlpha'));
    }
}