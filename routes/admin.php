<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('talkheals-secure-login')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('admin.login');
    Route::post('/', [LoginController::class, 'store']);
});

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('admin.logout');
});
