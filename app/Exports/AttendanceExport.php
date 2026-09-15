<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query(): Builder
    {
        $query = Attendance::with('user')
                           ->whereDate('check_in_time', '>=', $this->request->tanggal_mulai)
                           ->whereDate('check_in_time', '<=', $this->request->tanggal_selesai);

        if ($this->request->pegawai_id) {
            $query->where('user_id', $this->request->pegawai_id);
        }

        return $query->orderBy('check_in_time', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Tanggal',
            'Check-In',
            'Check-Out',
            'Lokasi Check-In',
            'Lokasi Check-Out',
            'Status',
            'Keterlambatan',
        ];
    }

    public function map($attendance): array
    {
        static $counter = 0;
        $counter++;

        $status = 'Hadir';
        $telat = '-';
        if ($attendance->check_in_time && $attendance->check_in_time->format('H:i:s') > '08:00:00') {
            $status = 'Terlambat';
            $telat = $attendance->check_in_time->diffInMinutes(\Carbon\Carbon::today()->setTime(8, 0, 0)) . ' menit';
        }

        return [
            $counter,
            $attendance->user->name ?? '-',
            $attendance->check_in_time ? $attendance->check_in_time->format('d-m-Y') : '-',
            $attendance->check_in_time ? $attendance->check_in_time->format('H:i:s') : '-',
            $attendance->check_out_time ? $attendance->check_out_time->format('H:i:s') : '-',
            $attendance->latitude && $attendance->longitude ? $attendance->latitude . ', ' . $attendance->longitude : '-',
            $attendance->check_out_time ? $attendance->latitude . ', ' . $attendance->longitude : '-',
            $status,
            $telat,
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}