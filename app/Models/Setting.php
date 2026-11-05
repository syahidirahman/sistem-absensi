<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_masuk',
        'jam_pulang',
        'toleransi_keterlambatan',
        'radius_default',
        'office_latitude',
        'office_longitude',
    ];

    // HAPUS BAGIAN $casts AGAR TIDAK ERROR
}