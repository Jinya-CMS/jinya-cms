<?php

namespace Jinya\Tests\Web\Middleware;

use App\Tests\TestRequestHandler;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class CheckRequiredFieldsMiddlewareTest extends TestCase
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
        try {
            $middleware->process($request->withParsedBody([]), $handler);
        } catch (MissingFieldsException $exception) {
            self::assertContains('test', $exception->fields);
        }
    }
}
