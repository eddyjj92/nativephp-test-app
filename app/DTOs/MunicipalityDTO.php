<?php

namespace App\DTOs;

class MunicipalityDTO
{
    /**
     * @param  DeliveryTypeDTO[]  $deliveryTypes
     */
    public function __construct(
        public int $id,
        public string $name,
        public bool $deliveryExpress,
        public int $provinceId,
        public int $costRingId,
        public array $deliveryTypes = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $deliveryTypes = array_map(
            fn ($type) => DeliveryTypeDTO::fromArray($type),
            $data['delivery_types'] ?? []
        );

        return new self(
            id: $data['id'],
            name: $data['name'],
            deliveryExpress: (bool) ($data['delivery_express'] ?? false),
            provinceId: $data['province_id'],
            costRingId: $data['cost_ring_id'] ?? 0,
            deliveryTypes: $deliveryTypes,
        );
    }
}
