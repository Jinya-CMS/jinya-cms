<?php

namespace App\Console;

use Attribute;

/**
 * This attribute marks a class that extends AbstractCommand as an executable command for the CLI
 */
#[Attribute(Attribute::TARGET_CLASS)]
class JinyaCommand
{
    /**
     * Creates a new JinyaCommand
     *
     * @param string $command The commands name, the name must be unique
     */
    public function __construct(public string $command)
    {
    }
}