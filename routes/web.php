<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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


Route::get('/test-mail', function () {
    Mail::raw('Hello, this is a quick test email from Laravel!', function ($message) {
        $message->to('your-test-email@example.com')
            ->subject('Laravel Live Mail Test');
    });

    return 'Test email sent!';
});


Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});


// Public route —
Route::get('/meetings/join/{code}', [App\Http\Controllers\MeetingJoinController::class, 'handleJoinLink'])
    ->name('meetings.join.link');
