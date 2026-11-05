<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_out_time',
        'latitude',
        'longitude',
        'face_verified',
        'photo_in',
        'photo_out',
        // 'photo_path', <-- SUDAH DIHAPUS KARENA TIDAK DIGUNAKAN
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'face_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}