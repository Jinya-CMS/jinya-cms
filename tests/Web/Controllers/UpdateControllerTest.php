<?php

namespace Jinya\Tests\Web\Controllers;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\UpdateController;

class UpdateControllerTest extends DatabaseAwareTestCase
{
    public function testGetVersion(): void
    {
        $controller = new UpdateController();
        $result = $controller->getVersionInfo();
        self::assertEquals(200, $result->getStatusCode());
    }
}
