<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartGroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    // Roles
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    });
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    // Users
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export')->middleware('permission:users.export');
    // Route::middleware('permission:users.view')->group(function () {
    //     Route::resource('users', UserController::class)->except(['show']);
    // });
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('permission:users.view');

    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('permission:users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:users.create');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('permission:users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:users.edit');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('permission:users.delete');

        
    // Attendance
    Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export')->middleware('permission:attendances.export');
    Route::get('/attendances/items/template', [AttendanceController::class, 'downloadItemTemplate'])->name('attendances.items.template');
    Route::get('/attendances/{attendance}/invoice', [AttendanceController::class, 'invoice'])->name('attendances.invoice')->middleware('permission:attendances.invoice');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show')->middleware('permission:attendances.view');
    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit')->middleware('permission:attendances.edit');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update')->middleware('permission:attendances.edit');
    Route::put('/attendances/{attendance}/items', [AttendanceController::class, 'updateItems'])->name('attendances.items.update')->middleware('permission:attendances.edit');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy')->middleware('permission:attendances.delete');
    Route::post('/attendances/{attendance}/items/import/preview', [AttendanceController::class, 'importItemsPreview'])->name('attendances.items.import.preview');
    Route::post('/attendances/{attendance}/items/import/confirm', [AttendanceController::class, 'importItemsConfirm'])->name('attendances.items.import.confirm');
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index')->middleware('permission:attendances.view');

    // Parts
    Route::get('/parts/import/progress', [PartController::class, 'importProgress'])
        ->name('parts.import.progress');

    Route::post('/parts/import', [PartController::class, 'importExcel'])
        ->name('parts.import')
        ->middleware('permission:parts.import');

    Route::get('/parts/export', [PartController::class, 'export'])
        ->name('parts.export')
        ->middleware('permission:parts.export');

    Route::get('/parts', [PartController::class, 'index'])
        ->name('parts.index')
        ->middleware('permission:parts.view');

    Route::get('/parts/create', [PartController::class, 'create'])
        ->name('parts.create')
        ->middleware('permission:parts.create');

    Route::post('/parts', [PartController::class, 'store'])
        ->name('parts.store')
        ->middleware('permission:parts.create');

    Route::get('/parts/{part}/edit', [PartController::class, 'edit'])
        ->name('parts.edit')
        ->middleware('permission:parts.edit');

    Route::put('/parts/{part}', [PartController::class, 'update'])
        ->name('parts.update')
        ->middleware('permission:parts.edit');

    Route::delete('/parts/{part}', [PartController::class, 'destroy'])
        ->name('parts.destroy')
        ->middleware('permission:parts.delete');



    // Part Groups
    Route::get('/part-groups', [PartGroupController::class, 'index'])
        ->name('part-groups.index')
        ->middleware('permission:part-groups.view');

    Route::get('/part-groups/create', [PartGroupController::class, 'create'])
        ->name('part-groups.create')
        ->middleware('permission:part-groups.create');

    Route::post('/part-groups', [PartGroupController::class, 'store'])
        ->name('part-groups.store')
        ->middleware('permission:part-groups.create');

    Route::get('/part-groups/{part_group}/edit', [PartGroupController::class, 'edit'])
        ->name('part-groups.edit')
        ->middleware('permission:part-groups.edit');

    Route::put('/part-groups/{part_group}', [PartGroupController::class, 'update'])
        ->name('part-groups.update')
        ->middleware('permission:part-groups.edit');

    Route::delete('/part-groups/{part_group}', [PartGroupController::class, 'destroy'])
        ->name('part-groups.destroy')
        ->middleware('permission:part-groups.delete');

 
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware('permission:profile.view');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

require __DIR__.'/auth.php';
