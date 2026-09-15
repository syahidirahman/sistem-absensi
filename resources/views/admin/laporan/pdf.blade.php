<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Absensi</h2>
        <p>Periode: {{ $request->tanggal_mulai }} s/d {{ $request->tanggal_selesai }}</p>
        <p>Dicetak: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $a->user->name ?? '-' }}</td>
                <td>{{ $a->check_in_time ? $a->check_in_time->format('d-m-Y') : '-' }}</td>
                <td>{{ $a->check_in_time ? $a->check_in_time->format('H:i:s') : '-' }}</td>
                <td>{{ $a->check_out_time ? $a->check_out_time->format('H:i:s') : '-' }}</td>
                <td>
                    @if($a->check_in_time && $a->check_in_time->format('H:i:s') > '08:00:00')
                        <span style="color: red;">Terlambat</span>
                    @else
                        <span style="color: green;">Hadir</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>