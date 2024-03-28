<?php

namespace Jinya\Tests\Web\Controllers;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\EnvironmentController;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

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
