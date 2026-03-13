<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('talkheals-secure-login')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/', [LoginController::class, 'store']);
});

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Availability management
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('admin.availability');
    Route::post('/availability/schedule', [AvailabilityController::class, 'saveSchedule'])->name('admin.availability.schedule');
    Route::post('/availability/settings', [AvailabilityController::class, 'saveSettings'])->name('admin.availability.settings');
    Route::post('/availability/exceptions', [AvailabilityController::class, 'addException'])->name('admin.availability.exceptions.store');
    Route::delete('/availability/exceptions/{exception}', [AvailabilityController::class, 'removeException'])->name('admin.availability.exceptions.destroy');
});
