<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        Office::create([
            'name' => 'Bawaslu Provinsi Kalimantan Selatan',
            'address' => 'Jl. RE Martadinata No.3, Kertak Baru Ilir, Kec. Banjarmasin Tengah, Kota Banjarmasin, Kalimantan Selatan 70231',
            'latitude' => -3.328316582913301,
            'longitude' => 114.58814047809338,
            'radius' => 100,
        ]);
    }
}