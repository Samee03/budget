<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;

class ProfileService
{
    public function updateUser(UserDTO $dto): User
    {
        $user = auth()->user();

        $user->update($dto->toUserArray());

        return $user->load( 'addresses');
    }
}
