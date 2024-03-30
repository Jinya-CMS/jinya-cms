<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Menu;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Nyholm\Psr7\ServerRequest;

class MenuControllerTest extends DatabaseAwareTestCase
{
    private function getController(array $body): MenuController
    {
        $controller = new MenuController();
        $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testGetItems(): void
    {
        $menu = new Menu();
        $menu->name = 'Title';
        $menu->create();

        $menu->replaceItems([
            [
                'route' => '#',
                'title' => 'Testtitle',
                'items' => [
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                    ],
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                    ],
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                        'items' => [
                            [
                                'route' => '#',
                                'title' => 'Testtitle',
                            ]
                        ],
                    ],
                ],
            ]
        ]);

        $controller = $this->getController([]);
        $result = $controller->getItems($menu->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
    }

    public function testGetItemsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getItems(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Menu not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testReplaceSegments(): void
    {
        $menu = new Menu();
        $menu->name = 'Title';
        $menu->create();

        $controller = $this->getController([
            [
                'route' => '#',
                'title' => 'Testtitle',
                'items' => [
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                    ],
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                    ],
                    [
                        'route' => '#',
                        'title' => 'Testtitle',
                        'items' => [
                            [
                                'route' => '#',
                                'title' => 'Testtitle',
                            ]
                        ],
                    ],
                ],
            ]
        ]);
        $result = $controller->replaceItems($menu->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testReplaceSegmentsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->replaceItems(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Menu not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
