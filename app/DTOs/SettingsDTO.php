<?php

namespace App\DTOs;

class SettingsDTO
{
    public function __construct(
        public string $site_name,
        public bool $site_active,
        public string $email,
        public ?string $phone,
        public string $address,
        public ?string $facebook,
        public ?string $x,
        public ?string $instagram,
        public string $logo_light,
        public string $logo_dark,
        public string $terms_conditions,
        public string $privacy_policies,
        public string $cookies_policies,
        public string $legal_notice,
        /** @var FrequentlyAskedQuestionDTO[] */
        public array $frequently_questions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            site_name: $data['site_name'],
            site_active: (bool) $data['site_active'],
            email: $data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'],
            facebook: $data['facebook'] ?? null,
            x: $data['x'] ?? null,
            instagram: $data['instagram'] ?? null,
            logo_light: $data['logo_light'],
            logo_dark: $data['logo_dark'],
            terms_conditions: $data['terms_conditions'],
            privacy_policies: $data['privacy_policies'],
            cookies_policies: $data['cookies_policies'],
            legal_notice: $data['legal_notice'],
            frequently_questions: array_map(
                fn (array $faq) => FrequentlyAskedQuestionDTO::fromArray($faq),
                $data['frequently_questions'] ?? []
            ),
        );
    }
}
