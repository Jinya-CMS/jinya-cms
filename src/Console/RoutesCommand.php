<?php

namespace App\Console;

/**
 *
 */
#[JinyaCommand('routes')]
class RoutesCommand extends AbstractCommand
{

    public function run(): void
    {
        $this->climate->info("The route command currently doesn't work, we are working on a fix");
    }
}