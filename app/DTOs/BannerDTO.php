<?php

namespace App\DTOs;

class BannerDTO
{
    public function __construct(
        public int $id,
        public string $image,
        public string $mobileImage,
        public string $status,
        public string $type,
        public int $bannerableId,
        public string $bannerableType,
        public DiscountDTO|InformativeBannerDTO|null $bannerable = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $bannerable = null;

        if (isset($data['bannerable'])) {
            $bannerable = match ($data['type']) {
                'discount' => DiscountDTO::fromArray($data['bannerable']),
                'informative' => InformativeBannerDTO::fromArray($data['bannerable']),
                default => null,
            };
        }

        return new self(
            id: $data['id'],
            image: $data['image'] ?? '',
            mobileImage: $data['mobile_image'] ?? '',
            status: $data['status'] ?? 'inactive',
            type: $data['type'] ?? 'informative',
            bannerableId: $data['bannerable_id'] ?? 0,
            bannerableType: $data['bannerable_type'] ?? '',
            bannerable: $bannerable,
        );
    }
}
