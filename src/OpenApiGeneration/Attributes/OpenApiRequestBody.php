<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OpenApiRequestBody
{

    public function __construct(public array $schema, public string $exampleName, public array $example)
    {
    }
}