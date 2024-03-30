<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Web\Controllers\DatabaseController;
use Nyholm\Psr7\ServerRequest;

class DatabaseControllerTest extends DatabaseAwareTestCase
{
    private function getController(mixed $body): DatabaseController
    {
        $controller = new DatabaseController();
        $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        $controller->body = $body;

        return $controller;
    }

    public function testAnalyzeDatabaseTest(): void
    {
        $controller = $this->getController([]);
        $result = $controller->analyzeDatabase();
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertArrayHasKey('tables', $body);
        self::assertArrayHasKey('server', $body);
        self::assertArrayHasKey('variables', $body);
        self::assertArrayHasKey('local', $body['variables']);
        self::assertArrayHasKey('session', $body['variables']);
        self::assertArrayHasKey('global', $body['variables']);
    }

    public function testQueryDatabaseAllowedMethod(): void
    {
        $controller = $this->getController(['query' => 'EXPLAIN menu']);
        $result = $controller->queryDatabase();
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $body);

        $item = $body[0];
        self::assertArrayHasKey('statement', $item);
        self::assertArrayHasKey('result', $item);
    }

    public function testQueryDatabaseDisallowedMethod(): void
    {
        $controller = $this->getController(['query' => 'DROP TABLE foobar']);
        $result = $controller->queryDatabase();
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $body);
        $item = $body[0];
        self::assertArrayHasKey('statement', $item);
        self::assertArrayHasKey('result', $item);
        self::assertEquals("Query hasn't been allowed", $item['result']);
    }
}
