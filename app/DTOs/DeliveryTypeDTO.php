<?php

namespace App\DTOs;

class DeliveryTypeDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $deliveryTimes,
        public string $prefix,
        public float $price,
        public bool $isDefault,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            deliveryTimes: $data['delivery_times'],
            prefix: $data['prefix'],
            price: (float) $data['price'],
            isDefault: (bool) $data['is_default'],
        );
    }
}
