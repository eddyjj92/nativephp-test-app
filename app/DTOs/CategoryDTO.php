<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public string $image,
        public int $products_count,
        public ?array $department = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            description: $data['description'] ?? null,
            image: $data['image'],
            products_count: $data['products_count'] ?? 0,
            department: $data['department'] ?? null,
        );
    }
}
