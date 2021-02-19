<?php

namespace App\OpenApiGeneration;

class OpenApiEndpoint
{
    /**
     * OpenApiEndpoint constructor.
     * @param string $url
     * @param OpenApiMethod[] $methods
     */
    public function __construct(public string $url, public array $methods = [])
    {
    }
}