<?php

namespace App\Routing\Attributes;

use Attribute;

/**
 *
 */
#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class JinyaApiField
{
    public function __construct(
        public readonly bool $ignore = false,
        public readonly bool $required = false,
    )
    {
    }
}