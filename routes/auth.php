<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ============================================
// REGISTER DINONAKTIFKAN (Redirect ke login)
// ============================================
Route::get('/register', function () {
    return redirect('/login');
})->name('register');

// ============================================
// LOGIN (TANPA MIDDLEWARE GUEST)
// ============================================
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login'); // middleware('guest') dihapus

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

// ============================================
// LOGOUT
// ============================================
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// ============================================
// LOGOUT DARURAT (GET) - untuk testing
// ============================================
Route::get('/logout-get', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout-get');

// ============================================
// PASSWORD RESET (jika diperlukan)
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
// EMAIL VERIFICATION (jika diperlukan)
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