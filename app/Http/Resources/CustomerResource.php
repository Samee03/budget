<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company,
            'date_of_birth' => $this->date_of_birth,

            // Optionally
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
