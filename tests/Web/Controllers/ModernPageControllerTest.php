<?php

namespace Jinya\Tests\Web\Controllers;

use App\Database\BlogCategory;
use App\Database\BlogPost;
use App\Database\ModernPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\BlogController;
use App\Web\Controllers\ModernPageController;
use Faker\Factory;
use Faker\Provider\Uuid;
use Nyholm\Psr7\ServerRequest;

class ModernPageControllerTest extends DatabaseAwareTestCase
{
    private function getController(array $body): ModernPageController
    {
        $controller = new ModernPageController();
        $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testGetSections(): void
    {
        $page = new ModernPage();
        $page->name = 'Title';
        $page->create();

        $page->replaceSections([
            [
                'html' => 'Test'
            ]
        ]);

        $controller = $this->getController([]);
        $result = $controller->getSections($page->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, $body);
    }

    public function testGetSectionsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getSections(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Modern page not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testReplaceSegments(): void
    {
        $page = new ModernPage();
        $page->name = 'Title';
        $page->create();

        $controller = $this->getController([
            [
                'html' => 'Test'
            ]
        ]);
        $result = $controller->replaceSections($page->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testReplaceSegmentsPostNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->replaceSections(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Modern page not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
