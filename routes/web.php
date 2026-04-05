<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Forget Password page
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot.password');

// Reset Password page
Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('reset.password');
