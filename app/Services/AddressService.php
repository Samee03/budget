<?php

namespace App\Services;

use App\DTOs\AddressDTO;
use App\Models\Address;
use Illuminate\Support\Collection;

class AddressService
{
    public function storeAddress(AddressDTO $dto): Address
    {
        $user = auth()->user();

        return $user->addresses()->create($dto->toArray());
    }

    public function updateAddress(AddressDTO $dto, Address $address): Collection
    {
        $user = auth()->user();

        $user->addresses()->where(['type' => $dto->type])->update(['is_default' => false]);

        $address->update($dto->toArray());

        return $user->addresses;
    }
}
