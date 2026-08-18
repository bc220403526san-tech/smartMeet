<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::view('/', 'auth.register')->name('register');
    Route::post('/', [AuthController::class, 'register']);

    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // FORGOT PASSWORD — form dikhana + link bhejna
    Route::view('/forgot-password', 'auth.forgot-password')->name('forgot.password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

    // RESET PASSWORD — mail wale link se form khulega + password update hoga
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('reset.password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
