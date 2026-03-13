<?php

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
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
