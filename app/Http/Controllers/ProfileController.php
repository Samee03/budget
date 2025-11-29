<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use App\Helper\ApiResponse;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function getProfile()
    {
        $user = auth()->user()->load(['addresses']);

        return ApiResponse::success(new CustomerResource($user));
    }

    public function updateUser(UpdateUserProfileRequest $request)
    {
        $dto = UserDTO::fromArray($request->validated());

        $updatedUser = $this->profileService->updateUser($dto);

        return ApiResponse::success(
            new CustomerResource($updatedUser),
            'Customer profile updated successfully'
        );
    }
}
