<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaceEmbedding extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'descriptor', 'photo_path'];

    protected $casts = [
        'descriptor' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}