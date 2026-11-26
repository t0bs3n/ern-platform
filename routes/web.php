<?php

use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsImageController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

Route::get('/', HomeController::class)->name('home');

// News images live in private storage (docs/DECISIONS.md); the controller checks who may see them.
Route::get('news/{news:slug}/image', NewsImageController::class)->name('news.image');

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

// The admin area: signed in and a role beyond member (the `admin` group); each tab checks its own gates.
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', [AdminAreaController::class, 'overview'])->name('admin');

    // Content tab (R1-01): news and pages, for editors.
    Route::middleware('can:edit-content')->prefix('content')->name('admin.content.')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->name('index');
        Route::get('news/new', [NewsController::class, 'create'])->name('news.create');
        Route::post('news', [NewsController::class, 'store'])->name('news.store');
        Route::get('news/{news}', [NewsController::class, 'edit'])->name('news.edit');
        Route::put('news/{news}', [NewsController::class, 'update'])->name('news.update');
        Route::get('pages/new', [PageController::class, 'create'])->name('pages.create');
        Route::post('pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('pages/{page}', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
    });

    Route::get('{tab}', [AdminAreaController::class, 'show'])->whereAlpha('tab')->name('admin.tab');
});
