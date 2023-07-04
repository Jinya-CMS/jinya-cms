<?php

namespace App\Tests\Extensions;

use App\Tests\Extensions\Subscriber\DropAllTablesSubscriber;
use App\Tests\Extensions\Subscriber\MigrationSubscriber;
use PHPUnit\Runner\Extension\Extension as PhpunitExtension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/** @codeCoverageIgnore */
class Extension implements PhpUnitExtension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new DropAllTablesSubscriber());
        $facade->registerSubscriber(new MigrationSubscriber());
    }
}