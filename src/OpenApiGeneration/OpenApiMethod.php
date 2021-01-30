<?php

namespace App\OpenApiGeneration;

use ReflectionClass;

class OpenApiMethod
{
    public function __construct(public ReflectionClass $reflectionClass)
    {
    }
}