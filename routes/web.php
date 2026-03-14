<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    return Inertia::render('Clients/Auth', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'status' => session('status'),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
