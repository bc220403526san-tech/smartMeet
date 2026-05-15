<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Organizer\OrganizerMeetingController;


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::view('/', 'auth.register')->name('register');
Route::post('/', [AuthController::class, 'register']); // form submit

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::view('/forgot-password', 'auth.forgot-password')->name('forgot.password');
Route::view('/reset-password', 'auth.reset-password')->name('reset.password');



Route::get('/auth/{provider}', [SocialController::class, 'redirect'])
    ->name('social.redirect')
    ->where('provider', 'google|facebook');

Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])
    ->name('social.callback')
    ->where('provider', 'google|facebook');

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {

        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MEETINGS
    |--------------------------------------------------------------------------
    */
    Route::prefix('meetings')->name('meetings.')->group(function () {

        Route::get('/', [MeetingController::class, 'index'])
            ->name('index');

        Route::get('/{meeting}', [MeetingController::class, 'show'])
            ->name('show');

        Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])
            ->name('cancel');

        Route::patch('/{meeting}/flag', [MeetingController::class, 'flag'])
            ->name('flag');
    });

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
        Route::prefix('users')->middleware('auth')->name('users.')->group(function () {

            // TOGGLE STATUS — yeh sahi hai
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('toggleStatus');

            // INDEX
            Route::get('/', [UserController::class, 'index'])
                ->name('index');

            // CREATE
            Route::get('/create', [UserController::class, 'create'])
                ->name('create');

            // STORE
            Route::post('/', [UserController::class, 'store'])
                ->name('store');

            // SHOW
            Route::get('/{user}', [UserController::class, 'show'])
                ->name('show');

            // EDIT
            Route::get('/{user}/edit', [UserController::class, 'edit'])
                ->name('edit');

            // UPDATE
            Route::put('/{user}', [UserController::class, 'update'])
                ->name('update');

            // DELETE
            Route::delete('/{user}', [UserController::class, 'destroy'])
                ->name('destroy');

        });

    // Extra Pages
    Route::view('/reports', 'admin.reports.index')->name('reports');
    Route::view('/settings', 'admin.settings.index')->name('settings');
    Route::view('/profile', 'admin.profile.index')->name('profile');
});


/*
|--------------------------------------------------------------------------
| ORGANIZER PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:organizer'])->prefix('organizer')->name('organizer.')->group(function () {

    // Dashboard
    Route::view('/dashboard', 'organizer.dashboard')->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MEETINGS
    |--------------------------------------------------------------------------
    */
    Route::prefix('meetings')->name('meetings.')->group(function () {

        Route::get('/', [OrganizerMeetingController::class, 'index'])->name('index');
        Route::get('/create', [OrganizerMeetingController::class, 'create'])->name('create');
        Route::view('/attend', 'organizer.meetings.attend')->name('attend');
        Route::post('/', [OrganizerMeetingController::class, 'store'])->name('store');
        Route::get('/{meeting}', [OrganizerMeetingController::class, 'show'])->name('show');
        Route::get('/{meeting}/edit', [OrganizerMeetingController::class, 'edit'])->name('edit');
        Route::put('/{meeting}', [OrganizerMeetingController::class, 'update'])->name('update');
        Route::patch('/{meeting}/cancel', [OrganizerMeetingController::class, 'cancel'])
            ->name('cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | PARTICIPANTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('participants')->name('participants.')->group(function () {

        Route::view('/', 'organizer.participants.index')->name('index');
        Route::view('/create', 'organizer.participants.create')->name('create');
        Route::view('/{id}', 'organizer.participants.show')->name('show');
        Route::view('/{id}/edit', 'organizer.participants.edit')->name('edit');
    });

    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {

        Route::view('/', 'organizer.settings.index')->name('index');

    });

});

/*
|--------------------------------------------------------------------------
| PARTICIPANT PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {

    Route::view('/dashboard', 'participant.dashboard')->name('dashboard');
});
