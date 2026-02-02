<?php

namespace App\DTOs;

class InformativeBannerDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $description,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? '',
        );
    }
}
