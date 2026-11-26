<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

Route::view('/', 'home')->name('home');

// Sign-in, sign-out and password reset (Fortify's controllers, our addresses).
// Reserved under /members: sign-in, sign-out, forgot-password, reset-password.
Route::prefix('members')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('sign-in', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('sign-in', [AuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    });

    Route::post('sign-out', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

    Route::view('/', 'members.index')->middleware('auth')->name('members');
});
