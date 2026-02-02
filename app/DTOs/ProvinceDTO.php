<?php

namespace App\DTOs;

class ProvinceDTO
{
    /**
     * @param  MunicipalityDTO[]  $municipalities
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public string $deliveryStatus,
        public array $municipalities = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $municipalities = array_map(
            fn ($muni) => MunicipalityDTO::fromArray($muni),
            $data['municipalities'] ?? []
        );

        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'] ?? '',
            deliveryStatus: $data['delivery_status'] ?? 'inactive',
            municipalities: $municipalities,
        );
    }
}
