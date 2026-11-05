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
// LOGIN PEGAWAI (tanpa middleware auth)
// ============================================
Route::get('/pegawai/login', [AuthController::class, 'showLoginForm'])->name('pegawai.login');

// Hanya gunakan SATU route POST login, pasang throttle untuk keamanan
Route::post('/pegawai/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('pegawai.login.post');

Route::post('/pegawai/logout', [AuthController::class, 'logout'])->name('pegawai.logout');

// ============================================
// ROUTE ADMIN (Middleware: auth + role:admin)
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Pegawai
    Route::resource('pegawai', PegawaiController::class);
    Route::get('/pegawai/{id}/toggle-active', [PegawaiController::class, 'toggleActive'])->name('pegawai.toggle-active');
    
    // Registrasi Wajah
    Route::get('/pegawai/{id}/face', [FaceController::class, 'register'])->name('pegawai.face.register');
    Route::post('/pegawai/{id}/face', [FaceController::class, 'store'])->name('pegawai.face.store');
    Route::delete('/pegawai/{id}/face', [FaceController::class, 'destroy'])->name('pegawai.face.destroy');
    
    // Absensi Admin
    Route::get('/absensi', [AdminAttendanceController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{id}', [AdminAttendanceController::class, 'show'])->name('absensi.show');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/pdf', [LaporanController::class, 'exportPDF'])->name('laporan.pdf');
    Route::post('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    
    // Pengaturan
    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
    
    // Profil Admin
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// ============================================
// ROUTE PEGAWAI (Middleware: auth + role:pegawai)
// ============================================
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('dashboard');
    
    // Absensi
    Route::get('/absensi', [PegawaiAbsensiController::class, 'index'])->name('absensi.index');
    
    // Profil
    Route::get('/profile', [PegawaiProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [PegawaiProfileController::class, 'update'])->name('profile.update');
});