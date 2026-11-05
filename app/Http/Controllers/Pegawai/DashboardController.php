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

        // Ambil data 7 hari terakhir
        $startDate = now()->subDays(6)->startOfDay();
        $attendances = Attendance::where('user_id', $user->id)
                                 ->where('check_in_time', '>=', $startDate)
                                 ->get()
                                 ->keyBy(function($item) {
                                     return $item->check_in_time->toDateString();
                                 });

        $histories = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i);
            $dateString = $date->toDateString();
            
            if (isset($attendances[$dateString])) {
                $histories[] = [
                    'is_alpha' => false,
                    'date' => $dateString,
                    'attendance' => $attendances[$dateString]
                ];
            } else {
                // Jika bukan hari Sabtu/Minggu dan tidak ada absen, maka Alpa
                if ($date->isWeekday()) {
                    $histories[] = [
                        'is_alpha' => true,
                        'date' => $dateString,
                    ];
                }
            }
        }

        $setting = \App\Models\Setting::first();

        return view('pegawai.dashboard', compact('status', 'histories', 'setting'));
    }
}