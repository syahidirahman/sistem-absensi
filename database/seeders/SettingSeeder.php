<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
            'toleransi_keterlambatan' => 15,
            'radius_default' => 100,
        ]);
    }
}