<?php

namespace App\DTOs;

class MarketplaceHomeDTO
{
    /**
     * @param  ProductDTO[]  $recommendedProducts
     * @param  ProductDTO[]  $newArrivals
     */
    public function __construct(
        public array $recommendedProducts = [],
        public array $newArrivals = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recommendedProducts: array_map(
                fn ($product) => ProductDTO::fromArray($product),
                $data['recommended_products'] ?? []
            ),
            newArrivals: array_map(
                fn ($product) => ProductDTO::fromArray($product),
                $data['new_arrivals'] ?? []
            ),
        );
    }
}
