<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OpenApiRecursiveField
{
    public function __construct(public string $name)
    {
    }
}