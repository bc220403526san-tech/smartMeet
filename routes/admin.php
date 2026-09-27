<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MeetingAttendController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleRequestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->name('dashboard');

        Route::delete('/activities/{key}', [
            DashboardController::class,
            'removeActivity'
        ])->name('activities.remove');

        Route::get('/activities/fetch', [
            DashboardController::class,
            'fetchActivities'
        ])->name('activities.fetch');

        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        Route::get('/audit', [
            AuditLogController::class,
            'index'
        ])->name('audit');

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {

                Route::get('/', [
                    ReportController::class,
                    'index'
                ])->name('index');

                Route::get('/export', [
                    ReportController::class,
                    'export'
                ])->name('export');
            });

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::prefix('settings')
            ->name('settings.')
            ->group(function () {

                Route::get('/', [
                    SettingsController::class,
                    'index'
                ])->name('index');

                Route::patch('/profile', [
                    SettingsController::class,
                    'updateProfile'
                ])->name('profile.update');

                Route::post('/avatar', [
                    SettingsController::class,
                    'updateAvatar'
                ])->name('avatar.update');

                Route::put('/password', [
                    SettingsController::class,
                    'updatePassword'
                ])->name('password.update');

                Route::patch('/notifications', [
                    SettingsController::class,
                    'updateNotifications'
                ])->name('notifications.update');

                Route::delete('/deactivate', [
                    SettingsController::class,
                    'deactivate'
                ])->name('deactivate');

                Route::post('/flash', [
                    SettingsController::class,
                    'storeFlash'
                ])->name('flash');
            });

        /*
        |--------------------------------------------------------------------------
        | Role Requests
        |--------------------------------------------------------------------------
        */

        Route::prefix('role-requests')
            ->name('role-requests.')
            ->group(function () {

                Route::get('/', [
                    RoleRequestController::class,
                    'index'
                ])->name('index');

                Route::patch('/{roleRequest}/approve', [
                    RoleRequestController::class,
                    'approve'
                ])->name('approve');

                Route::patch('/{roleRequest}/reject', [
                    RoleRequestController::class,
                    'reject'
                ])->name('reject');

                Route::delete('/{roleRequest}', [
                    RoleRequestController::class,
                    'destroy'
                ])->name('destroy');
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
                 * Main meetings page
                 */
                Route::get('/', [
                    MeetingController::class,
                    'index'
                ])->name('index');

                /*
                 * IMPORTANT:
                 * This must remain BEFORE /{meeting}.
                 */
                Route::get('/invited-meetings', [
                    MeetingController::class,
                    'invited'
                ])->name('invited');

                /*
                 * Admin invited participant live room.
                 */
                Route::get('/{meeting}/attend', [
                    MeetingAttendController::class,
                    'attend'
                ])->name('attend');

                /*
                 * Live meeting signaling.
                 */
                Route::post('/{meeting}/signal', [
                    MeetingAttendController::class,
                    'signal'
                ])->name('signal');

                /*
                 * Live transcript.
                 */
                Route::post('/{meeting}/transcript', [
                    MeetingAttendController::class,
                    'saveTranscript'
                ])->name('transcript');

                /*
                 * Mark Admin/Organizer participant as left.
                 */
                Route::post('/{meeting}/mark-left', [
                    MeetingAttendController::class,
                    'markLeft'
                ])->name('markLeft');

                /*
                 * Automatically complete meeting when scheduled
                 * duration has actually finished.
                 */
                Route::post('/{meeting}/complete-by-time', [
                    MeetingAttendController::class,
                    'completeByTime'
                ])->name('completeByTime');

                /*
                 * Existing Admin meeting details.
                 */
                Route::get('/{meeting}', [
                    MeetingController::class,
                    'show'
                ])->name('show');

                /*
                 * Existing Admin meeting edit.
                 */
                Route::get('/{meeting}/edit', [
                    MeetingController::class,
                    'edit'
                ])->name('edit');

                /*
                 * Existing Admin meeting delete.
                 */
                Route::delete('/{meeting}', [
                    MeetingController::class,
                    'destroy'
                ])->name('destroy');

                /*
                 * Existing Admin cancel action.
                 */
                Route::patch('/{meeting}/cancel', [
                    MeetingController::class,
                    'cancel'
                ])->name('cancel');

                /*
                 * Existing Admin flag action.
                 */
                Route::patch('/{meeting}/flag', [
                    MeetingController::class,
                    'flag'
                ])->name('flag');
            });

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::prefix('users')
            ->name('users.')
            ->group(function () {

                Route::get('/', [
                    UserController::class,
                    'index'
                ])->name('index');

                Route::get('/create', [
                    UserController::class,
                    'create'
                ])->name('create');

                Route::post('/', [
                    UserController::class,
                    'store'
                ])->name('store');

                Route::get('/{user}/meetings', [
                    UserController::class,
                    'meetingHistory'
                ])->name('meetings');

                Route::get('/{user}', [
                    UserController::class,
                    'show'
                ])->name('show');

                Route::get('/{user}/edit', [
                    UserController::class,
                    'edit'
                ])->name('edit');

                Route::put('/{user}', [
                    UserController::class,
                    'update'
                ])->name('update');

                Route::delete('/{user}', [
                    UserController::class,
                    'destroy'
                ])->name('destroy');

                Route::patch('/{user}/change-role', [
                    UserController::class,
                    'change-role'
                ])->name('change-role');

                Route::patch('/{user}/toggle-status', [
                    UserController::class,
                    'toggle-status'
                ])->name('toggle-status');
            });
    });
