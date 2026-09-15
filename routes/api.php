<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'API is working!';
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
});

Route::middleware('auth:sanctum')->post('/attendance/verify', [AttendanceController::class, 'verify']);