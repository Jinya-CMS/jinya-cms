<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OpenApiRequest
{
    public function __construct(public string $summary, public bool $binary = false)
    {
    }
}