<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OpenApiModel
{
    public function __construct(
        public string $description,
        public bool $hasId = true,
        public string $idType = 'integer'
    ) {
    }
}