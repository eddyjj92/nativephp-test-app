<?php

namespace App\DTOs;

class MunicipalityDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $provinceId,
    ) {}
}