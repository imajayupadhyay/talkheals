<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Public booking browsing (calendar & slots visible to everyone)
Route::get('/booking/calendar', [BookingController::class, 'getCalendar'])->name('booking.calendar');
Route::get('/booking/slots', [BookingController::class, 'getSlots'])->name('booking.slots');

Route::middleware(['auth', 'verified'])->group(function () {
    // Clinical Profile Routes
    Route::get('/profile', [ClientProfileController::class, 'edit'])->name('client.profile.edit');
    Route::patch('/profile', [ClientProfileController::class, 'update'])->name('client.profile.update');

    // Booking submission (auth required)
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    // My Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('client.appointments');
    Route::patch('/appointments/{booking}/cancel', [AppointmentController::class, 'cancel'])->name('client.appointments.cancel');
});

// Newsletter subscription (accessible to authenticated clients)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware(['auth', 'verified'])
    ->name('newsletter.subscribe');

// Google OAuth callback (must be outside admin auth middleware)
Route::get('/google/callback', [\App\Http\Controllers\Admin\GoogleController::class, 'callback'])
    ->middleware('auth:admin')
    ->name('google.callback');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
