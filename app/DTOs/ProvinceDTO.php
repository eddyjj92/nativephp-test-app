<?php

namespace App\DTOs;

class ProvinceDTO
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}