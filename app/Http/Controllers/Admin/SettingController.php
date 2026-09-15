<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_keterlambatan' => 15,
                'radius_default' => 100,
            ]);
        }
        return view('admin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i|after:jam_masuk',
            'toleransi_keterlambatan' => 'required|integer|min:0|max:120',
            'radius_default' => 'required|integer|min:10|max:10000',
        ]);

        $setting = Setting::first();
        if (!$setting) {
            $setting = new Setting();
        }

        $setting->jam_masuk = $request->jam_masuk . ':00';
        $setting->jam_pulang = $request->jam_pulang . ':00';
        $setting->toleransi_keterlambatan = $request->toleransi_keterlambatan;
        $setting->radius_default = $request->radius_default;
        $setting->save();

        return redirect()->route('admin.setting.index')->with('success', 'Pengaturan berhasil disimpan!');
    }
}