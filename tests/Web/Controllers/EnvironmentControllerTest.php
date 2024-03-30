<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Web\Controllers\EnvironmentController;

class EnvironmentControllerTest extends DatabaseAwareTestCase
{
    public function testGetEnvironment(): void
    {
        $controller = new EnvironmentController();
        $result = $controller->getEnvironment();
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        foreach ($body as $item) {
            if (stripos($item['key'], 'password')) {
                self::assertEquals('••••••', $item['value']);
            }
        }
    }
}
