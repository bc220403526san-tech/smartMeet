<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MeetingEndController;

/*
|--------------------------------------------------------------------------
| Public Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Route Files
|--------------------------------------------------------------------------
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
*/

Route::get('/test-mail', function () {
    Mail::raw('Hello, this is a quick test email from Laravel!', function ($message) {
        $message->to('your-test-email@example.com')
            ->subject('Laravel Live Mail Test');
    });

    return 'Test email sent!';
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.readAll');

    Route::get('/notifications/{notification}/open', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::get('/meetings/{meeting}/ended', [MeetingEndController::class, 'show'])
        ->name('meetings.ended');

    Route::get('/meetings/{meeting}/cancelled', [MeetingEndController::class, 'cancelled'])
        ->name('meetings.cancelled');
});

/*
|--------------------------------------------------------------------------
| Public Meeting Invite Route
|--------------------------------------------------------------------------
*/

Route::get(
    '/meetings/join/{code}',
    [App\Http\Controllers\MeetingJoinController::class, 'handleJoinLink']
)->name('meetings.join.link');

/*
|--------------------------------------------------------------------------
| Legal Pages
|--------------------------------------------------------------------------
*/

Route::view('/privacy-policy', 'legal.privacy')
    ->name('privacy-policy');

Route::view('/terms', 'legal.terms')
    ->name('terms');

Route::view('/data-deletion', 'legal.data-deletion')
    ->name('data-deletion');


Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap')
        ->header('Content-Type', 'application/xml');
});
