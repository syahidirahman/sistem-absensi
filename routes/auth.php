<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ============================================
// REGISTER DINONAKTIFKAN (Redirect ke login)
// ============================================
Route::get('/register', function () {
    return redirect('/login');
})->name('register');

// ============================================
// LOGIN
// ============================================
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

// HAPUS 'guest' DARI SINI AGAR CONTROLLER BISA BEKERJA
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['throttle:5,1']); 

// ============================================
// LOGOUT (Hanya bisa diakses via POST demi keamanan)
// ============================================
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// ============================================
// PASSWORD RESET 
// ============================================
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// ============================================
// EMAIL VERIFICATION
// ============================================
Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ============================================
// CONFIRM PASSWORD
// ============================================
Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware('auth')
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware('auth');

// ============================================
// UPDATE PASSWORD
// ============================================
Route::put('/password', [PasswordController::class, 'update'])
    ->middleware('auth')
    ->name('password.update');