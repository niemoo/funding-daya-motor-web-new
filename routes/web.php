<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Attendance
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');
    Route::get('/attendances/items/template', [AttendanceController::class, 'downloadItemTemplate'])->name('attendances.items.template');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::put('/attendances/{attendance}/items', [AttendanceController::class, 'updateItems'])->name('attendances.items.update');
    Route::post('/attendances/{attendance}/items/import/preview', [AttendanceController::class, 'importItemsPreview'])->name('attendances.items.import.preview');
    Route::post('/attendances/{attendance}/items/import/confirm', [AttendanceController::class, 'importItemsConfirm'])->name('attendances.items.import.confirm');

    // Users
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export')->middleware('role:Admin');
    Route::resource('users', UserController::class)->middleware('role:Admin');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__.'/auth.php';
