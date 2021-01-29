<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class OpenApiAdditionalField
{
    public function __construct(public string $name, public string $type)
    {
    }
}