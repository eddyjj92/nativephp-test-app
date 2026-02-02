<?php

namespace App\DTOs;

class CurrencyDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $isoCode,
        public bool $isDefault,
        public float $conversionValue,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            isoCode: $data['iso_code'],
            isDefault: $data['is_default'] ?? false,
            conversionValue: (float) ($data['conversion_value'] ?? 1),
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }
}
