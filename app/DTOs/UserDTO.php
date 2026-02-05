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
        public ?CustomerDTO $customer,
        public array $permissions,
    ) {}

    public function isCustomer(): bool
    {
        return $this->customer !== null;
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone_country_code = $data['phone_country_code'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->status = $data['status'] ?? 'enabled';
        $this->email_verified_at = $data['email_verified_at'] ?? null;
        $this->avatar = $data['avatar'] ?? null;
        $this->google_id = $data['google_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->avatar_backup = $data['avatar_backup'] ?? null;
        $this->admin = $data['admin'] ?? null;
        $this->permissions = $data['permissions'] ?? [];

        // Handle customer - convert array to CustomerDTO if needed
        $customer = $data['customer'] ?? null;
        if ($customer instanceof CustomerDTO) {
            $this->customer = $customer;
        } elseif (is_array($customer)) {
            $this->customer = CustomerDTO::fromArray($customer);
        } else {
            $this->customer = null;
        }
    }

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
            customer: isset($data['customer']) ? CustomerDTO::fromArray($data['customer']) : null,
            permissions: $data['permissions'] ?? [],
        );
    }
}
