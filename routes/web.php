<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\ConferenceAdminController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\EventAdminController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewsController as PublicNewsController;
use App\Http\Controllers\NewsImageController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;

Route::get('/', HomeController::class)->name('home');

// About Us and Get Involved (R1-04).
Route::get('about', [AboutController::class, 'about'])->name('about');
Route::get('get-involved', [AboutController::class, 'involved'])->name('involved');

// Conferences (R1-06).
Route::get('conferences', [ConferenceController::class, 'index'])->name('conferences.index');
Route::get('conferences/{conference}/submit', [ConferenceController::class, 'submit'])->name('conferences.submit');
Route::get('conferences/{conference}/{tab?}', [ConferenceController::class, 'show'])->where('tab', '[a-z]+')->name('conferences.show');

// Events and deadlines (R1-08).
Route::get('events', [EventController::class, 'index'])->name('events.index');
Route::get('events.ics', [EventController::class, 'feed'])->name('events.feed');

// Podcast and ERN Live (R1-07).
Route::get('podcast', [MediaController::class, 'podcast'])->name('podcast');
Route::get('live', [MediaController::class, 'live'])->name('live');

// Code of Conduct, Imprint and Privacy Policy (R1-05).
Route::get('code-of-conduct', [LegalPageController::class, 'conduct'])->name('conduct');
Route::get('imprint', [LegalPageController::class, 'imprint'])->name('imprint');
Route::get('privacy', [LegalPageController::class, 'privacy'])->name('privacy');

// News (R1-03).
Route::get('news', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('news/{slug}', [PublicNewsController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('news.show');
Route::get('feed/news.xml', [PublicNewsController::class, 'feed'])->name('feed.news');

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

    // Conference tab (R1-06): the list for the tab's roles, editing for the conference team.
    Route::get('conference', [ConferenceAdminController::class, 'index'])->name('admin.conference.index');
    Route::middleware('can:manage-conference')->group(function () {
        Route::get('conference/{conference}', [ConferenceAdminController::class, 'edit'])->name('admin.conference.edit');
        Route::put('conference/{conference}', [ConferenceAdminController::class, 'update'])->name('admin.conference.update');
    });

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
        Route::get('people/new', [TeamMemberController::class, 'create'])->name('people.create');
        Route::post('people', [TeamMemberController::class, 'store'])->name('people.store');
        Route::get('people/{person}', [TeamMemberController::class, 'edit'])->name('people.edit');
        Route::put('people/{person}', [TeamMemberController::class, 'update'])->name('people.update');
        Route::delete('people/{person}', [TeamMemberController::class, 'destroy'])->name('people.destroy');
        Route::get('events/new', [EventAdminController::class, 'create'])->name('events.create');
        Route::post('events', [EventAdminController::class, 'store'])->name('events.store');
        Route::get('events/{event}', [EventAdminController::class, 'edit'])->name('events.edit');
        Route::put('events/{event}', [EventAdminController::class, 'update'])->name('events.update');
        Route::delete('events/{event}', [EventAdminController::class, 'destroy'])->name('events.destroy');
    });

    Route::get('{tab}', [AdminAreaController::class, 'show'])->whereAlpha('tab')->name('admin.tab');
});
