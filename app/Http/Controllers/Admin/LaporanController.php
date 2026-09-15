<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class LaporanController extends Controller
{
    public function index()
    {
        $pegawai = User::where('role', 'pegawai')->get();
        return view('admin.laporan.index', compact('pegawai'));
    }

    public function exportPDF(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'pegawai_id' => 'nullable|exists:users,id',
        ]);

        $query = Attendance::with('user')
                           ->whereDate('check_in_time', '>=', $request->tanggal_mulai)
                           ->whereDate('check_in_time', '<=', $request->tanggal_selesai);

        if ($request->pegawai_id) {
            $query->where('user_id', $request->pegawai_id);
        }

        $attendances = $query->orderBy('check_in_time', 'desc')->get();

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('attendances', 'request'));
        return $pdf->download('laporan_absensi_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'pegawai_id' => 'nullable|exists:users,id',
        ]);

        return Excel::download(new AttendanceExport($request), 'laporan_absensi_' . now()->format('Ymd_His') . '.xlsx');
    }
}