<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RequiredFields
{
    /**
     * RequiredFields constructor.
     * @param array $requiredFields
     */
    public function __construct(public array $requiredFields)
    {
    }
}