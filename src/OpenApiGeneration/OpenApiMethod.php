<?php

namespace App\OpenApiGeneration;

use App\Web\Attributes\JinyaAction;
use ReflectionClass;

class OpenApiMethod
{
    public function __construct(public ReflectionClass $reflectionClass, public JinyaAction $jinyaAction)
    {
    }
}