<?php

use App\Http\Controllers\Organizer\MeetingAttendController;
use App\Http\Controllers\Organizer\MeetingController;
use App\Http\Controllers\Organizer\ParticipantController;
use App\Http\Controllers\Organizer\MeetingModerationController;
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

        Route::get('/dashboard', [App\Http\Controllers\Organizer\DashboardController::class, 'index'])
                ->name('dashboard');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Organizer\SettingsController::class, 'index'])
                ->name('index');

            Route::patch('/profile', [\App\Http\Controllers\Organizer\SettingsController::class, 'updateProfile'])
                ->name('profile.update');

            Route::post('/avatar', [\App\Http\Controllers\Organizer\SettingsController::class, 'updateAvatar'])
                ->name('avatar.update');

            Route::put('/password', [\App\Http\Controllers\Organizer\SettingsController::class, 'updatePassword'])
                ->name('password.update');

            Route::patch('/notifications', [\App\Http\Controllers\Organizer\SettingsController::class, 'updateNotifications'])
                ->name('notifications.update');

            Route::delete('/deactivate', [\App\Http\Controllers\Organizer\SettingsController::class, 'deactivate'])
                ->name('deactivate');

            Route::post('/flash', [\App\Http\Controllers\Organizer\SettingsController::class, 'storeFlash'])
                ->name('flash');
            Route::post('/role-request', [App\Http\Controllers\Organizer\SettingsController::class, 'roleRequest'])
                ->name('role-request');
        });

        Route::prefix('participants')->name('participants.')->group(function () {

            Route::get('/', [ParticipantController::class, 'index'])->name('index');
            Route::get('/{participant}', [ParticipantController::class, 'show'])
                ->name('show');
            Route::delete('/{participant}', [ParticipantController::class, 'destroy'])
                ->name('destroy');

        });

        /*
        | Meetings — CRUD + attend/signal/transcript
        */
        Route::prefix('meetings')->name('meetings.')->group(function () {

            // CRUD
            Route::get('/', [MeetingController::class, 'index'])->name('index');
            Route::get('/create', [MeetingController::class, 'create'])->name('create');
            Route::post('/', [MeetingController::class, 'store'])->name('store');
            Route::get('/status-check', [\App\Http\Controllers\Organizer\MeetingController::class, 'statusCheck'])
                ->name('status-check');
            Route::get('/{meeting}', [MeetingController::class, 'show'])->name('show');
            Route::get('/{meeting}/edit', [MeetingController::class, 'edit'])->name('edit');
            Route::put('/{meeting}', [MeetingController::class, 'update'])->name('update');
            Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('cancel');
            Route::post('/{meeting}/send-invite', [MeetingController::class, 'sendInvite'])->name('sendInvite');
            Route::post('/{meeting}/end', [MeetingController::class, 'end'])
                ->name('end');

            // Live session
            Route::get('/{meeting}/attend', [MeetingAttendController::class, 'attend'])->name('attend');
            Route::post('/{meeting}/signal', [MeetingAttendController::class, 'signal'])->name('signal');
            Route::post('/{meeting}/transcript', [MeetingAttendController::class, 'saveTranscript'])->name('transcript');
            Route::post('/{meeting}/mark-left', [\App\Http\Controllers\Organizer\MeetingAttendController::class, 'markLeft'])
                ->name('markLeft');
            Route::post('/{meeting}/moderate', [MeetingModerationController::class, 'moderate'])
                ->name('moderate');

        });

    });
