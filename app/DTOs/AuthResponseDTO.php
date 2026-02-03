<?php

namespace App\DTOs;

class AuthResponseDTO
{
    public function __construct(
        public string $token,
        public UserDTO $user,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'],
            user: UserDTO::fromArray($data['user']),
        );
    }
}
