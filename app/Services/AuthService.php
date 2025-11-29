<?php

namespace App\Services;

use App\Helper\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function attemptLogin($email, $password, $remember): array
    {
        $user = User::where('email', $email)
            ->select('id', 'name', 'email', 'password', 'backoffice_access')->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        } else {
            $userData = $user->toArray();
            $user->tokens()->delete();
        }

        $token = $user->createToken("$user->id-$user->name", [],
            $remember ? now()->addWeek() : now()->addDay()
        );

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
            'user' => $userData
        ];
    }

    public function register(array $data): ?User
    {
        $data['password'] = Hash::make($data['password']);
        unset($data['confirm_password']);
        $data['new_user'] = true;

        return User::create($data);
    }

    public function changePassword($password, $current_password): JsonResponse|bool
    {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }

        if (!Hash::check($current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect'],
            ]);
        }

        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    public function resetPassword(array $credentials): void
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $user->update(['password' => Hash::make($password)]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function sendResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }
}
