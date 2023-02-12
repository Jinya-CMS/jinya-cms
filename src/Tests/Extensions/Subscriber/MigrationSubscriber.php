<?php

namespace App\Tests\Extensions\Subscriber;

use App\Database\Migrations\Migrator;
use PHPUnit\Event\Test\BeforeFirstTestMethodCalled;
use PHPUnit\Event\Test\BeforeFirstTestMethodCalledSubscriber;

class MigrationSubscriber implements BeforeFirstTestMethodCalledSubscriber
{
    public function notify(BeforeFirstTestMethodCalled $event): void
    {
        Migrator::migrate();
    }
}