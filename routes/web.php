<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MeetingJoinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Auth, social login, admin, organizer and participant routes are
| loaded from their separate route files.
|
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/social.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/organizer.php';
require __DIR__ . '/participant.php';


/*
|--------------------------------------------------------------------------
| Test Mail Route
|--------------------------------------------------------------------------
|
| Development/testing route for checking Laravel mail configuration.
|
*/

Route::get('/test-mail', function () {
    Mail::raw(
        'Hello, this is a quick test email from Laravel!',
        function ($message) {
            $message->to('your-test-email@example.com')
                ->subject('Laravel Live Mail Test');
        }
    );

    return 'Test email sent!';
});


/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
|
| Notification routes are available only to authenticated users.
|
*/

Route::middleware(['auth'])->group(function () {

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markRead']
    )->name('notifications.read');

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllRead']
    )->name('notifications.readAll');

    Route::get(
        '/notifications/{notification}/open',
        [NotificationController::class, 'open']
    )->name('notifications.open');
});


/*
|--------------------------------------------------------------------------
| Public Meeting Invite Link
|--------------------------------------------------------------------------
|
| IMPORTANT:
| This route must remain public because a user may click an invitation
| link before logging in.
|
| Flow:
|
| Invite link
|      ↓
| MeetingJoinController
|      ↓
| Not logged in → pending_meeting_code stored in session
|      ↓
| Login / Register
|      ↓
| User added to meeting participants
|      ↓
| Upcoming → Participant My Meetings
| Active   → Direct Meeting Room
|
*/

Route::get(
    '/meetings/join/{code}',
    [MeetingJoinController::class, 'handleJoinLink']
)->name('meetings.join.link');


/*
|--------------------------------------------------------------------------
| SmartMeet Legal Pages
|--------------------------------------------------------------------------
|
| These public pages are also required for Meta/Facebook login setup.
|
*/

Route::view(
    '/privacy-policy',
    'legal.privacy'
)->name('privacy-policy');

Route::view(
    '/terms',
    'legal.terms'
)->name('terms');

Route::view(
    '/data-deletion',
    'legal.data-deletion'
)->name('data-deletion');
