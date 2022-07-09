<?php

namespace Jinya\Tests\Web\Actions\Database;

use App\Web\Actions\Database\ExecuteQueryAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ExecuteQueryActionTest extends TestCase
{

    public function test__invokeAllowedMethod(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['query' => 'EXPLAIN menu']);
        $action = new ExecuteQueryAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $body);
        $item = $body[0];
        self::assertArrayHasKey('statement', $item);
        self::assertArrayHasKey('result', $item);
    }

    public function test__invokeAllowedMethodTwoQueries(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['query' => 'EXPLAIN menu; EXPLAIN users;']);
        $action = new ExecuteQueryAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(2, $body);
        $item = $body[0];
        self::assertArrayHasKey('statement', $item);
        self::assertArrayHasKey('result', $item);
    }

    public function test__invokeDisallowedMethod(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['query' => 'DROP TABLE foobar']);
        $action = new ExecuteQueryAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        self::assertCount(1, $body);
        $item = $body[0];
        self::assertArrayHasKey('statement', $item);
        self::assertArrayHasKey('result', $item);
        self::assertEquals('Query not allowed', $item['result']);
    }
}
