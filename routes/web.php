<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\FaceController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Pegawai\AuthController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\AbsensiController as PegawaiAbsensiController;
use App\Http\Controllers\Pegawai\ProfileController as PegawaiProfileController;
use Illuminate\Support\Facades\Route;

// ============================================
// HALAMAN UTAMA
// ============================================
Route::get('/', function () {
    return view('welcome');
});

// ============================================
// AUTH (Breeze) - Semua route auth ada di routes/auth.php
// ============================================
require __DIR__.'/auth.php';

// ============================================
// ROUTE TESTING (LOGIN CEPAT)
// ============================================
Route::get('/login-as-admin', function () {
    $admin = \App\Models\User::where('role', 'admin')->first();
    if (!$admin) {
        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'admin@absensi.com',
            'nip' => 'ADMIN001',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
    auth()->login($admin);
    return redirect('/admin/dashboard');
})->name('login-as-admin');

Route::get('/login-as-user', function () {
    $user = \App\Models\User::where('role', 'pegawai')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'name' => 'Test Pegawai',
            'email' => 'pegawai@test.com',
            'nip' => 'PEG001',
            'password' => bcrypt('password123'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);
    }
    auth()->login($user);
    return redirect('/pegawai/dashboard');
})->name('login-as-user');

// ============================================
// LOGIN PEGAWAI (tanpa middleware auth)
// ============================================
Route::get('/pegawai/login', [AuthController::class, 'showLoginForm'])->name('pegawai.login');
Route::post('/pegawai/login', [AuthController::class, 'login'])->name('pegawai.login.post');
Route::post('/pegawai/logout', [AuthController::class, 'logout'])->name('pegawai.logout');

// ============================================
// ROUTE ADMIN (Middleware: auth + role:admin)
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai/{id}/toggle-active', [PegawaiController::class, 'toggleActive'])->name('pegawai.toggle-active');
    Route::get('/pegawai/{id}/face', [FaceController::class, 'register'])->name('pegawai.face.register');
    Route::post('/pegawai/{id}/face', [FaceController::class, 'store'])->name('pegawai.face.store');
    Route::delete('/pegawai/{id}/face', [FaceController::class, 'destroy'])->name('pegawai.face.destroy');
    Route::get('/absensi', [AdminAttendanceController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{id}', [AdminAttendanceController::class, 'show'])->name('absensi.show');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/pdf', [LaporanController::class, 'exportPDF'])->name('laporan.pdf');
    Route::post('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// ============================================
// ROUTE PEGAWAI (Middleware: auth + role:pegawai)
// ============================================
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', [PegawaiAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/profile', [PegawaiProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [PegawaiProfileController::class, 'update'])->name('profile.update');
});