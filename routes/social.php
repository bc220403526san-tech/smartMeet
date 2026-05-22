<?php

use App\Http\Controllers\SocialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Social Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth/{provider}')
    ->where(['provider' => 'google|facebook'])
    ->group(function () {

        Route::get('/', [SocialController::class, 'redirect'])->name('social.redirect');
        Route::get('/callback', [SocialController::class, 'callback'])->name('social.callback');

    });
