<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartGroupController;
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
    Route::get('/attendances/{attendance}/invoice', [AttendanceController::class, 'invoice'])->name('attendances.invoice');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])
    ->name('attendances.destroy')
    ->middleware('role:Admin');

    // Users
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export')->middleware('role:Admin');
    Route::resource('users', UserController::class)->middleware('role:Admin');

    // Part Groups
    Route::resource('part-groups', PartGroupController::class)->except(['show']);

    // Parts
    Route::get('/parts/export', [PartController::class, 'export'])->name('parts.export');
    Route::get('/parts/import/progress', [PartController::class, 'importProgress'])->name('parts.import.progress');
    Route::post('/parts/import', [PartController::class, 'importExcel'])->name('parts.import');
    Route::resource('parts', PartController::class)->except(['show']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__.'/auth.php';
