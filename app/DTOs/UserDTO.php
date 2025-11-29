<?php

namespace App\DTOs;

use Carbon\Carbon;

class UserDTO
{
    public function __construct(
        public string  $name,
        public string  $email,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $dateOfBirth = null,
    )
    {
        if ($this->dateOfBirth) {
            $this->dateOfBirth = Carbon::parse($this->dateOfBirth)->format('Y-m-d');
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            company: $data['company'] ?? null,
            dateOfBirth: $data['date_of_birth'] ?? null,
        );
    }

    public function toUserArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'date_of_birth' => $this->dateOfBirth,
        ];
    }
}

