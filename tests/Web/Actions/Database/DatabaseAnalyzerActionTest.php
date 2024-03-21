<?php

namespace Jinya\Tests\Web\Actions\Database;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Database\DatabaseAnalyzerAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DatabaseAnalyzerActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DatabaseAnalyzerAction();
        $result = $action($request, $response, []);
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
}
