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
            Route::put('/', [SettingsController::class, 'update'])->name('update');
        });

        /*
        | Meetings — browse + live session
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {

            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/today', [MeetingController::class, 'today'])->name('today');

            // Live session
            Route::get('/{meeting}/attend', [MeetingAttendController::class, 'attend'])->name('attend');
            Route::post('/{meeting}/signal', [MeetingAttendController::class, 'signal'])->name('signal');
            Route::post('/{meeting}/transcript', [MeetingAttendController::class, 'saveTranscript'])->name('transcript');

        });

    });
