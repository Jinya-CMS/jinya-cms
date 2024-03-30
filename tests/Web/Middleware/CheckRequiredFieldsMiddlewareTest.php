<?php

namespace Jinya\Cms\Web\Middleware;

use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Tests\TestRequestHandler;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CheckRequiredFieldsMiddlewareTest extends DatabaseAwareTestCase
{
    public function testProcess(): void
    {
        $request = new ServerRequest('POST', '');
        $middleware = new CheckRequiredFieldsMiddleware(['test']);

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware->process($request->withParsedBody([
            'test' => Uuid::uuid(),
        ]), $handler);

        self::assertTrue(true);
    }

    public function testProcessFieldMissing(): void
    {
        $request = new ServerRequest('POST', '');
        $middleware = new CheckRequiredFieldsMiddleware(['test']);

        $response = new Response();
        $handler = new TestRequestHandler($response);

        $response = $middleware->process($request->withParsedBody([]), $handler);

        $response->getBody()->rewind();
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals($body, [
            'success' => false,
            'error' => [
                'message' => 'There are required fields missing',
                'type' => 'missing-fields',
                'missingFields' => ['test'],
            ],
        ]);
    }
}
