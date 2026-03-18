<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    // Dashboard — semua role bisa akses
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users/export', [UserController::class, 'export'])
        ->name('users.export')
        ->middleware('role:Admin');

    Route::resource('users', UserController::class)
        ->middleware('role:Admin');

    // Users — Admin only
    Route::resource('users', UserController::class)
        ->middleware('role:Admin');

    // Attendance — semua role, tapi data difilter di controller
    Route::get('/attendances/export', [AttendanceController::class, 'export'])
        ->name('attendances.export');
    Route::get('/attendances', [AttendanceController::class, 'index'])
        ->name('attendances.index');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__.'/auth.php';
