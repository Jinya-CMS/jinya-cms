<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Web\Controllers\UpdateController;

class UpdateControllerTest extends DatabaseAwareTestCase
{
    public function testGetVersion(): void
    {
        $controller = new UpdateController();
        $result = $controller->getVersionInfo();
        self::assertEquals(200, $result->getStatusCode());
    }
}
