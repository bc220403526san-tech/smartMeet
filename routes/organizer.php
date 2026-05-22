<?php

use App\Http\Controllers\Organizer\MeetingAttendController;
use App\Http\Controllers\Organizer\MeetingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organizer Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:organizer'])
    ->prefix('organizer')
    ->name('organizer.')
    ->group(function () {

        Route::view('/dashboard', 'organizer.dashboard')->name('dashboard');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::view('/', 'organizer.settings.index')->name('index');
        });

        Route::prefix('participants')->name('participants.')->group(function () {
            Route::view('/', 'organizer.participants.index')->name('index');
            Route::view('/create', 'organizer.participants.create')->name('create');
            Route::view('/{id}', 'organizer.participants.show')->name('show');
            Route::view('/{id}/edit', 'organizer.participants.edit')->name('edit');
        });

        /*
        | Meetings — CRUD + attend/signal/transcript
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {

            // CRUD
            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/create', [MeetingController::class, 'create'])->name('create');
            Route::post('/', [MeetingController::class, 'store'])->name('store');
            Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
            Route::get('/{meeting}/edit', [MeetingController::class, 'edit'])->name('edit');
            Route::put('/{meeting}', [MeetingController::class, 'update'])->name('update');
            Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('cancel');

            // Live session
            Route::get('/{meeting}/attend', [MeetingAttendController::class, 'attend'])->name('attend');
            Route::post('/{meeting}/signal', [MeetingAttendController::class, 'signal'])->name('signal');
            Route::post('/{meeting}/transcript', [MeetingAttendController::class, 'saveTranscript'])->name('transcript');

        });

    });
