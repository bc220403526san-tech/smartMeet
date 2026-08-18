<?php

use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\RoleRequestController;
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

        /*
        | Dashboard
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::delete('/activities/{key}', [DashboardController::class, 'removeActivity'])
            ->name('activities.remove');

        Route::get('/activities/fetch', [DashboardController::class, 'fetchActivities'])
            ->name('activities.fetch');

        /*
        | Reports
        */
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });

        /*
        | Settings — profile, avatar, password, notifications, deactivation
        */
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::patch('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
            Route::post('/avatar', [SettingsController::class, 'updateAvatar'])->name('avatar.update');
            Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
            Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
            Route::delete('/deactivate', [SettingsController::class, 'deactivate'])->name('deactivate');
            Route::post('/flash', [SettingsController::class, 'storeFlash'])->name('flash');
        });

        /*
        | Role Requests — approve/reject organizer role change requests
        */
        Route::prefix('role-requests')->name('role-requests.')->group(function () {
            Route::get('/', [RoleRequestController::class, 'index'])->name('index');
            Route::patch('/{roleRequest}/approve', [RoleRequestController::class, 'approve'])->name('approve');
            Route::patch('/{roleRequest}/reject', [RoleRequestController::class, 'reject'])->name('reject');
            Route::delete('/{roleRequest}', [RoleRequestController::class, 'destroy'])->name('destroy');
        });

        /*
        | Meetings — read + moderate only (no create/edit by admin)
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {
            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
            Route::get('/{meeting}/edit', [MeetingController::class, 'edit'])->name('edit');
            Route::delete('/{meeting}', [MeetingController::class, 'destroy'])->name('destroy');
            Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('cancel');
            Route::patch('/{meeting}/flag', [MeetingController::class, 'flag'])->name('flag');
        });

        /*
        | Users — full CRUD + role change + status toggle
        */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/change-role', [UserController::class, 'changeRole'])->name('change-role');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });
    });
