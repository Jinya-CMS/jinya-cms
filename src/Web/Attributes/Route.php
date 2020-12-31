<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_FUNCTION)]
class Route
{
    /**
     * Route constructor.
     * @param string $url
     * @param string $name
     */
    public function __construct(private string $url, private string $name)
    {
    }
}