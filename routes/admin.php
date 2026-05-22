<?php

use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::view('/reports', 'admin.reports.index')->name('reports');
        Route::view('/settings', 'admin.settings.index')->name('settings');
        Route::view('/profile', 'admin.profile.index')->name('profile');

        /*
        | Meetings — read + moderate only (no create/edit)
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {
            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
            Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('cancel');
            Route::patch('/{meeting}/flag', [MeetingController::class, 'flag'])->name('flag');
        });

        /*
        | Users — full CRUD + status toggle
        */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });

    });
