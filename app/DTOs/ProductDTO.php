<?php

namespace App\DTOs;

class ProductDTO
{
    /**
     * @param  array<DiscountDTO>  $activeDiscounts
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $code,
        public ?string $description,
        public ?string $type,
        public float $salePrice,
        public float $weight,
        public bool $freeShipping,
        public string $image,
        public string $status,
        public bool $recommended,
        public ?int $stock,
        public array $activeDiscounts = [],
        public ?CategoryDTO $category = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $activeDiscounts = array_map(
            fn ($discount) => DiscountDTO::fromArray($discount),
            $data['active_discounts'] ?? []
        );

        $category = isset($data['category'])
            ? CategoryDTO::fromArray($data['category'])
            : null;

        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            code: $data['code'] ?? '',
            description: $data['description'] ?? null,
            type: $data['type'] ?? null,
            salePrice: (float) ($data['sale_price'] ?? 0),
            weight: (float) ($data['weight'] ?? 0),
            freeShipping: (bool) ($data['free_shipping'] ?? false),
            image: $data['image'] ?? '',
            status: $data['status'] ?? 'DISABLED',
            recommended: (bool) ($data['recommended'] ?? false),
            stock: isset($data['stock']) ? (int) $data['stock'] : null,
            activeDiscounts: $activeDiscounts,
            category: $category,
        );
    }

    public function isAvailable(): bool
    {
        // If stock is null (not provided by API), assume available if status is ENABLED
        // If stock is provided, check that it's greater than 0
        return $this->status === 'ENABLED' && ($this->stock === null || $this->stock > 0);
    }

    public function hasDiscount(): bool
    {
        return count($this->activeDiscounts) > 0;
    }

    public function getDiscountedPrice(): float
    {
        if (! $this->hasDiscount()) {
            return $this->salePrice;
        }

        $price = $this->salePrice;

        foreach ($this->activeDiscounts as $discount) {
            if ($discount->type === 'percentage') {
                $price -= $price * ($discount->value / 100);
            } else {
                $price -= $discount->value;
            }
        }

        return max(0, $price);
    }
}
