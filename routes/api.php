<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

Route::group(['middleware' => ['guest']], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password');
    Route::get('/user', [AuthController::class, 'getAuthUser'])->name('user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'getProfile'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'updateUser'])->name('update.profile');

    Route::apiResource('/addresses', AddressController::class);
});