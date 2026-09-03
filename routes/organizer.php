<?php

use App\Http\Controllers\Organizer\DashboardController;
use App\Http\Controllers\Organizer\MeetingAttendController;
use App\Http\Controllers\Organizer\MeetingController;
use App\Http\Controllers\Organizer\MeetingModerationController;
use App\Http\Controllers\Organizer\ParticipantController;
use App\Http\Controllers\Organizer\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Organizer Panel Routes
|--------------------------------------------------------------------------
|
| URL prefix:
|   /organizer/...
|
| Route names:
|   organizers.*
|
*/

Route::middleware(['auth', 'role:organizers'])
    ->prefix('organizer')
    ->name('organizers.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */
        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {

                Route::get('/', [SettingsController::class, 'index'])
                    ->name('index');

                Route::patch('/profile', [SettingsController::class, 'updateProfile'])
                    ->name('profile.update');

                Route::post('/avatar', [SettingsController::class, 'updateAvatar'])
                    ->name('avatar.update');

                Route::put('/password', [SettingsController::class, 'updatePassword'])
                    ->name('password.update');

                Route::patch('/notifications', [SettingsController::class, 'updateNotifications'])
                    ->name('notifications.update');

                Route::delete('/deactivate', [SettingsController::class, 'deactivate'])
                    ->name('deactivate');

                Route::post('/flash', [SettingsController::class, 'storeFlash'])
                    ->name('flash');

                Route::post('/role-request', [SettingsController::class, 'roleRequest'])
                    ->name('role-request');
            });

        /*
        |--------------------------------------------------------------------------
        | Participants
        |--------------------------------------------------------------------------
        */
        Route::prefix('participants')
            ->name('participants.')
            ->group(function () {

                Route::get('/', [ParticipantController::class, 'index'])
                    ->name('index');

                Route::get('/{participant}', [ParticipantController::class, 'show'])
                    ->name('show');

                Route::delete('/{participant}', [ParticipantController::class, 'destroy'])
                    ->name('destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Meetings
        |--------------------------------------------------------------------------
        */
        Route::prefix('meetings')
            ->name('meetings.')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Meeting CRUD
                |--------------------------------------------------------------------------
                */

                Route::get('/', [MeetingController::class, 'index'])
                    ->name('index');

                Route::get('/create', [MeetingController::class, 'create'])
                    ->name('create');

                Route::post('/', [MeetingController::class, 'store'])
                    ->name('store');

                /*
                 * IMPORTANT:
                 * Keep static routes before /{meeting}
                 * so "status-check" is never treated as a meeting ID.
                 */
                Route::get('/status-check', [MeetingController::class, 'statusCheck'])
                    ->name('status-check');

                Route::get('/{meeting}', [MeetingController::class, 'show'])
                    ->name('show');

                Route::get('/{meeting}/edit', [MeetingController::class, 'edit'])
                    ->name('edit');

                Route::put('/{meeting}', [MeetingController::class, 'update'])
                    ->name('update');

                Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])
                    ->name('cancel');

                Route::post('/{meeting}/send-invite', [MeetingController::class, 'sendInvite'])
                    ->name('sendInvite');

                Route::post('/{meeting}/end', [MeetingController::class, 'end'])
                    ->name('end');

                /*
                |--------------------------------------------------------------------------
                | Live Meeting Session
                |--------------------------------------------------------------------------
                */

                Route::get('/{meeting}/attend', [MeetingAttendController::class, 'attend'])
                    ->name('attend');

                Route::post('/{meeting}/signal', [MeetingAttendController::class, 'signal'])
                    ->name('signal');

                Route::post('/{meeting}/transcript', [MeetingAttendController::class, 'saveTranscript'])
                    ->name('transcript');

                Route::post('/{meeting}/mark-left', [MeetingAttendController::class, 'markLeft'])
                    ->name('markLeft');

                Route::post('/{meeting}/moderate', [MeetingModerationController::class, 'moderate'])
                    ->name('moderate');
            });
    });
