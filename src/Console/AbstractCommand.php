<?php

namespace Jinya\Cms\Console;

use League\CLImate\CLImate;

/**
 * This is the base class for all commands, the cli executor expects all commands to extend this class and have a JinyaCommand attribute
 */
abstract class AbstractCommand
{
    /**
     * @var CLImate The {@see CLImate} instance of the command
     */
    protected CLImate $climate;

    /**
     * Creates a new instance of AbstractCommand. Derived classes need to call the constructor to initialize CLImate
     */
    public function __construct()
    {
        $this->climate = new CLImate();
    }

    /**
     * Gets executed by the cli executor
     *
     * @return void
     */
    abstract public function run(): void;
}
