<?php

namespace Jinya\Tests\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\MyJinyaController;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;

class MyJinyaControllerTest extends DatabaseAwareTestCase
{
    private function getController(mixed $body): MyJinyaController
    {
        $controller = new MyJinyaController();
        $controller->request = (new ServerRequest('', ''));
        $controller->body = $body;

        return $controller;
    }

    public function testGetMyProfile(): void
    {
        $result = $this->getController([])->getMyProfile();
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(CurrentUser::$currentUser->format(), $body);
    }

    public function testUpdateMyProfile(): void
    {
        $controller = $this->getController([
            'email' => 'test@example.com',
            'artistName' => 'Theo Test',
            'aboutMe' => 'Lorem ipsum',
        ]);
        $result = $controller->updateMyProfile();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdateColorSchemeAuto(): void
    {
        $controller = $this->getController([
            'colorScheme' => '',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertNull(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function testUpdateColorSchemeLight(): void
    {
        $controller = $this->getController([
            'colorScheme' => 'light',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function testUpdateColorSchemeDark(): void
    {
        $controller = $this->getController([
            'colorScheme' => 'dark',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue(CurrentUser::$currentUser->prefersColorScheme);
    }
}
