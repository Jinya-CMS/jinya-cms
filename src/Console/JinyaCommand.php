<?php

namespace App\Console;

use Attribute;

/**
 *
 */
#[Attribute(Attribute::TARGET_CLASS)]
class JinyaCommand
{
    public function __construct(public string $command)
    {
    }
}