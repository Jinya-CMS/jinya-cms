<?php

namespace App\Web\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_FUNCTION)]
class Route
{
    /**
     * The url of the route
     *
     * @var string
     */
    private string $url;
    /**
     * The name of the route
     *
     * @var string
     */
    private string $name;

    /**
     * Route constructor.
     * @param string $url
     * @param string $name
     */
    public function __construct(string $url, string $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

}