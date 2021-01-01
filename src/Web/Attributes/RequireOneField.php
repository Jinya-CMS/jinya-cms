<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RequireOneField
{
    /**
     * RequiredFields constructor.
     * @param array $validFields
     */
    public function __construct(public array $validFields)
    {
    }
}