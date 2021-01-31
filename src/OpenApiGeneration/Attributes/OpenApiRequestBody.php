<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OpenApiRequestBody
{

    public function __construct(public array $schema)
    {
    }
}