<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Mobile\DashboardController as MobileDashboardController;
use App\Http\Controllers\Mobile\AccountController as MobileAccountController;
use App\Http\Controllers\Mobile\IncomeController as MobileIncomeController;
use App\Http\Controllers\Mobile\ExpenseController as MobileExpenseController;
use App\Http\Controllers\Mobile\ProjectController as MobileProjectController;
use App\Http\Controllers\Mobile\ProjectPaymentController as MobileProjectPaymentController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

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

    Route::prefix('mobile')->group(function () {
        // Dashboard snapshot (income, expenses, net, balances, accounts)
        Route::get('/dashboard', [MobileDashboardController::class, 'show']);

        // Accounts & balances
        Route::get('/accounts', [MobileAccountController::class, 'index']);

        // Incomes (salary + other income flows)
        Route::get('/incomes', [MobileIncomeController::class, 'index']);
        Route::post('/incomes', [MobileIncomeController::class, 'store']);

        // Expenses (personal + project-linked)
        Route::get('/expenses', [MobileExpenseController::class, 'index']);
        Route::post('/expenses', [MobileExpenseController::class, 'store']);

        // Projects and project payments (optional but handy in mobile)
        Route::get('/projects', [MobileProjectController::class, 'index']);
        Route::post('/projects/{project}/payments', [MobileProjectPaymentController::class, 'store']);
    });
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