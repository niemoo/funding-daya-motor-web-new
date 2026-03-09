<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;

// ── Public routes ──
Route::post('/login', [AuthController::class, 'login']);

// ── Protected routes (Sanctum) ──
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::get('/attendance-status',   [AuthController::class, 'attendanceStatus']);

    // Attendance
    Route::post('/checkin',           [AttendanceController::class, 'checkin']);
    Route::post('/checkout',          [AttendanceController::class, 'checkout']);
    Route::get('/history/daily',      [AttendanceController::class, 'dailyHistory']);
    Route::get('/statistics',         [AttendanceController::class, 'statistics']);
});