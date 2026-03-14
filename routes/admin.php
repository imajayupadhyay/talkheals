<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoogleController;
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

    // Bookings management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.status');

    // Workshops management
    Route::get('/workshops', [WorkshopController::class, 'index'])->name('admin.workshops');
    Route::post('/workshops', [WorkshopController::class, 'store'])->name('admin.workshops.store');
    Route::patch('/workshops/{workshop}', [WorkshopController::class, 'update'])->name('admin.workshops.update');
    Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy'])->name('admin.workshops.destroy');

    // Admin users management
    Route::get('/admins', [AdminUserController::class, 'index'])->name('admin.admins');
    Route::post('/admins', [AdminUserController::class, 'store'])->name('admin.admins.store');
    Route::patch('/admins/{admin}', [AdminUserController::class, 'update'])->name('admin.admins.update');
    Route::delete('/admins/{admin}', [AdminUserController::class, 'destroy'])->name('admin.admins.destroy');

    // Google Calendar integration
    Route::get('/google/connect', [GoogleController::class, 'redirect'])->name('admin.google.connect');
    Route::delete('/google/disconnect', [GoogleController::class, 'disconnect'])->name('admin.google.disconnect');

    // Clients CRUD
    Route::get('/clients', [ClientController::class, 'index'])->name('admin.clients');
    Route::post('/clients', [ClientController::class, 'store'])->name('admin.clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
    Route::patch('/clients/{client}', [ClientController::class, 'update'])->name('admin.clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
});
