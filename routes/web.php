<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Clients/Auth', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'status' => session('status'),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/Index');
    })->name('dashboard');

    // Clinical Profile Routes
    Route::get('/profile', [ClientProfileController::class, 'edit'])->name('client.profile.edit');
    Route::patch('/profile', [ClientProfileController::class, 'update'])->name('client.profile.update');

    // Booking (free discovery call)
    Route::get('/booking/calendar', [BookingController::class, 'getCalendar'])->name('booking.calendar');
    Route::get('/booking/slots', [BookingController::class, 'getSlots'])->name('booking.slots');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // My Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('client.appointments');
    Route::patch('/appointments/{booking}/cancel', [AppointmentController::class, 'cancel'])->name('client.appointments.cancel');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
