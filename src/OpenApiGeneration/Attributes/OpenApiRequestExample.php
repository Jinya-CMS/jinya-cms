<?php

namespace App\OpenApiGeneration\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class OpenApiRequestExample
{

    /**
     * OpenApiRequestExample constructor.
     * @param string $name
     * @param array $example
     */
    public function __construct(public string $name, public array $example)
    {
    }
}