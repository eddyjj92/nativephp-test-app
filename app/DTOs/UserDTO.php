<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $phone_country_code,
        public ?string $phone,
        public ?string $address,
        public string $status,
        public ?string $email_verified_at,
        public ?string $avatar,
        public ?string $google_id,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $avatar_backup,
        public ?array $admin,
        public array $permissions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            phone_country_code: $data['phone_country_code'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            status: $data['status'] ?? 'enabled',
            email_verified_at: $data['email_verified_at'] ?? null,
            avatar: $data['avatar'] ?? null,
            google_id: $data['google_id'] ?? null,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            avatar_backup: $data['avatar_backup'] ?? null,
            admin: $data['admin'] ?? null,
            permissions: $data['permissions'] ?? [],
        );
    }
}
