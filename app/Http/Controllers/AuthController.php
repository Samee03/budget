<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(LoginRequest $request)
    {
        $response = $this->authService->attemptLogin(
            $request->email,
            $request->password,
            $request->remember_me ?? false
        );

        $user = User::find($response['user']['id']);

        return ApiResponse::success($response, 'Logged in successfully');
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return $user
            ? ApiResponse::success($user, 'User created successfully', 201)
            : ApiResponse::error('Registration failed', 400);
    }

    public function getAuthUser(Request $request)
    {
        return ApiResponse::success($request->user(), 'User authenticated');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $this->authService->changePassword($request->password, $request->current_password);

        return ApiResponse::success(null, 'Password changed successfully');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword($request->validated());

        return ApiResponse::success(null, 'Password reset successful');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->sendResetLink($request->email);

        return ApiResponse::success(null, 'Password reset link sent');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
