<?php

use App\Http\Controllers\Participant\MeetingAttendController;
use App\Http\Controllers\Participant\MeetingController;
use App\Http\Controllers\Participant\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Participant Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:participant'])
    ->prefix('participant')
    ->name('participant.')
    ->group(function () {

        Route::view('/dashboard', 'participant.dashboard')->name('dashboard');

        /*
        | Settings
        */
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::patch('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
            Route::post('/avatar', [SettingsController::class, 'updateAvatar'])->name('avatar.update');
            Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
            Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');
            Route::delete('/deactivate', [SettingsController::class, 'deactivate'])->name('deactivate');
            Route::post('/flash', [SettingsController::class, 'storeFlash'])->name('flash');
            Route::post('/role-request', [SettingsController::class, 'roleRequest'])->name('role-request');
        });

        /*
        | Meetings — browse + live session
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {

            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/today', [MeetingController::class, 'today'])->name('today');
            Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
            Route::get('/status-check', [\App\Http\Controllers\Participant\MeetingController::class, 'statusCheck'])
                ->name('status-check');

            // Live session
            Route::get('/{meeting}/attend', [MeetingAttendController::class, 'attend'])->name('attend');
            Route::post('/{meeting}/signal', [MeetingAttendController::class, 'signal'])->name('signal');
            Route::post('/{meeting}/transcript', [MeetingAttendController::class, 'saveTranscript'])->name('transcript');
            Route::post('meetings/{meeting}/mark-left', [\App\Http\Controllers\Participant\MeetingAttendController::class, 'markLeft'])
                ->name('markLeft');
        });

    });
