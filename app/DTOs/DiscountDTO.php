<?php

namespace App\DTOs;

class DiscountDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $description,
        public string $type,
        public float $value,
        public string $applicableTo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? '',
            type: $data['type'] ?? 'percentage',
            value: (float) ($data['value'] ?? 0),
            applicableTo: $data['applicable_to'] ?? '',
        );
    }
}
