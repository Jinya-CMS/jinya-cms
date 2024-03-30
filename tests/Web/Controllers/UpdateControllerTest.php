<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;

class UpdateControllerTest extends DatabaseAwareTestCase
{
    public function testGetVersion(): void
    {
        $controller = new UpdateController();
        $result = $controller->getVersionInfo();
        self::assertEquals(200, $result->getStatusCode());
    }
}
