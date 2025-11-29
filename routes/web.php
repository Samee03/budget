<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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