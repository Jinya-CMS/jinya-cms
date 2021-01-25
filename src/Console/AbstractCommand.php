<?php

namespace App\Console;

use League\CLImate\CLImate;

abstract class AbstractCommand
{
    protected CLImate $climate;

    public function __construct()
    {
        $this->climate = new CLImate();
    }

    abstract public function run(): void;
}