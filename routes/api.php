<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Models\User;

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

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'admin', 'as' => 'admin'], function () {
    Route::get('/users', function () {
        $users = Cache::remember("users.all", 3600, fn() => User::all());
        return response()->json($users);
    });

    Route::get('/search-user', function () {
        $key = request()->keywords;

        $cacheKey = 'users.search.' . md5($key);

        $results = Cache::remember($cacheKey, 3600, fn() => User::search($key)->get());

        return response()->json($results);
    })->name('search');
});