<?php

namespace App\DTOs;

class CustomerDTO
{
    public function __construct(
        public int $id,
        public bool $privacy_policies_accepted,
        public int $user_id,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            privacy_policies_accepted: $data['privacy_policies_accepted'] ?? false,
            user_id: $data['user_id'],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}
