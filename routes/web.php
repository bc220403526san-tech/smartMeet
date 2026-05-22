<?php
//
//use App\Http\Controllers\Admin\MeetingController;
//use App\Http\Controllers\Admin\UserController;
//use App\Http\Controllers\AuthController;
//
//use App\Http\Controllers\Organizer\MeetingController as OrganizerMeetingController;
//use App\Http\Controllers\Organizer\MeetingAttendController as OrganizerMeetingAttendController;
//
//use App\Http\Controllers\Participant\MeetingController as ParticipantMeetingController;
//use App\Http\Controllers\Participant\SettingsController as ParticipantSettingsController;
//
//use App\Http\Controllers\Participant\MeetingAttendController as ParticipantMeetingAttendController;
//
//use App\Http\Controllers\SocialController;
//
//use Illuminate\Support\Facades\Route;
//
///*
//|--------------------------------------------------------------------------
//| AUTH ROUTES
//|--------------------------------------------------------------------------
//*/
//
//Route::view('/', 'auth.register')->name('register');
//Route::post('/', [AuthController::class, 'register']);
//
//Route::view('/login', 'auth.login')->name('login');
//Route::post('/login', [AuthController::class, 'login']);
//
//Route::post('/logout', [AuthController::class, 'logout'])
//    ->name('logout');
//
//Route::view('/forgot-password', 'auth.forgot-password')
//    ->name('forgot.password');
//
//Route::view('/reset-password', 'auth.reset-password')
//    ->name('reset.password');
//
///*
//|--------------------------------------------------------------------------
//| SOCIAL LOGIN
//|--------------------------------------------------------------------------
//*/
//
//Route::get('/auth/{provider}', [SocialController::class, 'redirect'])
//    ->name('social.redirect')
//    ->where('provider', 'google|facebook');
//
//Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])
//    ->name('social.callback')
//    ->where('provider', 'google|facebook');
//
///*
//|--------------------------------------------------------------------------
//| ADMIN PANEL
//|--------------------------------------------------------------------------
//*/
//
//Route::middleware(['auth', 'role:admin'])
//    ->prefix('admin')
//    ->name('admin.')
//    ->group(function () {
//
//        /*
//        |--------------------------------------------------------------------------
//        | DASHBOARD
//        |--------------------------------------------------------------------------
//        */
//
//        Route::view('/dashboard', 'admin.dashboard')
//            ->name('dashboard');
//
//        /*
//        |--------------------------------------------------------------------------
//        | MEETINGS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('meetings')
//            ->name('meetings.')
//            ->group(function () {
//
//                Route::get('/', [MeetingController::class, 'index'])
//                    ->name('index');
//
//                Route::get('/{meeting}', [MeetingController::class, 'show'])
//                    ->name('show');
//
//                Route::patch('/{meeting}/cancel', [MeetingController::class, 'cancel'])
//                    ->name('cancel');
//
//                Route::patch('/{meeting}/flag', [MeetingController::class, 'flag'])
//                    ->name('flag');
//            });
//
//        /*
//        |--------------------------------------------------------------------------
//        | USERS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('users')
//            ->name('users.')
//            ->group(function () {
//
//                Route::get('/', [UserController::class, 'index'])
//                    ->name('index');
//
//                Route::get('/create', [UserController::class, 'create'])
//                    ->name('create');
//
//                Route::post('/', [UserController::class, 'store'])
//                    ->name('store');
//
//                Route::get('/{user}', [UserController::class, 'show'])
//                    ->name('show');
//
//                Route::get('/{user}/edit', [UserController::class, 'edit'])
//                    ->name('edit');
//
//                Route::put('/{user}', [UserController::class, 'update'])
//                    ->name('update');
//
//                Route::delete('/{user}', [UserController::class, 'destroy'])
//                    ->name('destroy');
//
//                Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])
//                    ->name('toggleStatus');
//            });
//
//        /*
//        |--------------------------------------------------------------------------
//        | EXTRA PAGES
//        |--------------------------------------------------------------------------
//        */
//
//        Route::view('/reports', 'admin.reports.index')
//            ->name('reports');
//
//        Route::view('/settings', 'admin.settings.index')
//            ->name('settings');
//
//        Route::view('/profile', 'admin.profile.index')
//            ->name('profile');
//    });
//
///*
//|--------------------------------------------------------------------------
//| ORGANIZER PANEL
//|--------------------------------------------------------------------------
//*/
//
//Route::middleware(['auth', 'role:organizer'])
//    ->prefix('organizer')
//    ->name('organizer.')
//    ->group(function () {
//
//        /*
//        |--------------------------------------------------------------------------
//        | DASHBOARD
//        |--------------------------------------------------------------------------
//        */
//
//        Route::view('/dashboard', 'organizer.dashboard')
//            ->name('dashboard');
//
//        /*
//        |--------------------------------------------------------------------------
//        | MEETINGS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('meetings')
//            ->name('meetings.')
//            ->group(function () {
//
//                Route::get('/', [OrganizerMeetingController::class, 'index'])
//                    ->name('index');
//
//                Route::get('/create', [OrganizerMeetingController::class, 'create'])
//                    ->name('create');
//
//                Route::post('/', [OrganizerMeetingController::class, 'store'])
//                    ->name('store');
//
//                Route::get('/{meeting}', [OrganizerMeetingController::class, 'show'])
//                    ->name('show');
//
//                Route::get('/{meeting}/edit', [OrganizerMeetingController::class, 'edit'])
//                    ->name('edit');
//
//                Route::put('/{meeting}', [OrganizerMeetingController::class, 'update'])
//                    ->name('update');
//
//                Route::patch('/{meeting}/cancel', [OrganizerMeetingController::class, 'cancel'])
//                    ->name('cancel');
//
//                /*
//                |--------------------------------------------------------------------------
//                | ATTEND MEETING
//                |--------------------------------------------------------------------------
//                */
//
//                Route::get('/{meeting}/attend',
//                    [OrganizerMeetingAttendController::class, 'attend'])
//                    ->name('attend');
//
//                /*
//                |--------------------------------------------------------------------------
//                | SIGNALING
//                |--------------------------------------------------------------------------
//                */
//
//                Route::post('/{meeting}/signal',
//                    [OrganizerMeetingAttendController::class, 'signal'])
//                    ->name('signal');
//
//                /*
//                |--------------------------------------------------------------------------
//                | TRANSCRIPT
//                |--------------------------------------------------------------------------
//                */
//
//                Route::post('/{meeting}/transcript',
//                    [OrganizerMeetingAttendController::class, 'saveTranscript'])
//                    ->name('transcript');
//            });
//
//        /*
//        |--------------------------------------------------------------------------
//        | PARTICIPANTS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('participants')
//            ->name('participants.')
//            ->group(function () {
//
//                Route::view('/', 'organizer.participants.index')
//                    ->name('index');
//
//                Route::view('/create', 'organizer.participants.create')
//                    ->name('create');
//
//                Route::view('/{id}', 'organizer.participants.show')
//                    ->name('show');
//
//                Route::view('/{id}/edit', 'organizer.participants.edit')
//                    ->name('edit');
//            });
//
//        /*
//        |--------------------------------------------------------------------------
//        | SETTINGS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('settings')
//            ->name('settings.')
//            ->group(function () {
//
//                Route::view('/', 'organizer.settings.index')
//                    ->name('index');
//            });
//    });
//
///*
//|--------------------------------------------------------------------------
//| PARTICIPANT PANEL
//|--------------------------------------------------------------------------
//*/
//
//Route::middleware(['auth', 'role:participant'])
//    ->prefix('participant')
//    ->name('participant.')
//    ->group(function () {
//
//        /*
//        |--------------------------------------------------------------------------
//        | DASHBOARD
//        |--------------------------------------------------------------------------
//        */
//
//        Route::get('/dashboard', function () {
//            return view('participant.dashboard');
//        })->name('dashboard');
//
//        /*
//        |--------------------------------------------------------------------------
//        | MEETINGS
//        |--------------------------------------------------------------------------
//        */
//
//        Route::prefix('meetings')
//            ->name('meetings.')
//            ->group(function () {
//
//                Route::get('/', [ParticipantMeetingController::class, 'index'])
//                    ->name('index');
//
////                Route::get('/{meeting}', [ParticipantMeetingController::class, 'show'])
////                    ->name('show');
//
//                /*
//                |--------------------------------------------------------------------------
//                | ATTEND
//                |--------------------------------------------------------------------------
//                */
//
//                Route::get('/{meeting}/attend',
//                    [ParticipantMeetingAttendController::class, 'attend'])
//                    ->name('attend');
//
//                /*
//              |--------------------------------------------------------------------------
//              | TODAY
//              |--------------------------------------------------------------------------
//              */
//
//                Route::get('/today',
//                    [ParticipantMeetingController::class, 'today'])
//                    ->name('today');
//
//
//                /*
//                |--------------------------------------------------------------------------
//                | SIGNALING
//                |--------------------------------------------------------------------------
//                */
//
//                Route::post('/{meeting}/signal',
//                    [ParticipantMeetingAttendController::class, 'signal'])
//                    ->name('signal');
//
//                /*
//                |--------------------------------------------------------------------------
//                | TRANSCRIPT
//                |--------------------------------------------------------------------------
//                */
//
//                Route::post('/{meeting}/transcript',
//                    [ParticipantMeetingAttendController::class, 'saveTranscript'])
//                    ->name('transcript');
//            });
//
//
//        /*
//|--------------------------------------------------------------------------
//| SETTINGS
//|--------------------------------------------------------------------------
//*/
//
//        Route::prefix('settings')
//            ->name('settings.')
//            ->group(function () {
//
//                Route::get('/', [ParticipantSettingsController::class, 'index'])
//                    ->name('index');
//
//                Route::put('/', [ParticipantSettingsController::class, 'update'])
//                    ->name('update');
//
//            });
//
//
//    });


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Auth, social login, and panel route groups are loaded from separate
| files to keep each concern isolated and maintainable.
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/social.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/organizer.php';
require __DIR__ . '/participant.php';
