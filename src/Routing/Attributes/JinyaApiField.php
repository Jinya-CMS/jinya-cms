<?php

namespace App\Routing\Attributes;

use Attribute;

/**
 * Specifies whether a field in a JinyaApi entity is required or ignored
 */
#[Attribute(flags: Attribute::TARGET_PROPERTY)]
class JinyaApiField
{
    /**
     * Creates a new JinyaApiField
     *
     * @param bool $ignore If true, the field will be ignored by JinyaModelToRouteResolver
     * @param bool $required If true, the field will be treated required by JinyaModelToRouteResolver
     */
    public function __construct(
        public readonly bool $ignore = false,
        public readonly bool $required = false,
    )
    {
    }
}