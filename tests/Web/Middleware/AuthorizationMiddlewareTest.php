<?php

namespace Jinya\Tests\Web\Middleware;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\DatabaseAwareTestCase;
use App\Tests\TestRequestHandler;
use App\Web\Middleware\AuthorizationMiddleware;
use DateInterval;
use DateTime;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class AuthorizationMiddlewareTest extends DatabaseAwareTestCase
{
    public function testProcess(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $middleware = new AuthorizationMiddleware('ROLE_WRITER');

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware->process($request, $handler);

        self::assertEquals(200, $response->getStatusCode());
    }

    private function createApiKey(): ApiKey
    {
        $apiKey = new ApiKey();
        $apiKey->userId = CurrentUser::$currentUser->id;
        $apiKey->validSince = (new DateTime())->add(new DateInterval('PT5M'));
        $apiKey->setApiKey();
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->userAgent = 'PHPUnit';
        $apiKey->create();

        return $apiKey;
    }

    public function testProcessNotEnoughPermission(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $middleware = new AuthorizationMiddleware('ROLE_ADMIN');

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $response = $middleware->process($request, $handler);

        $response->getBody()->rewind();
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(403, $response->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'You do not have enough permissions, please request the role ROLE_ADMIN',
                'type' => 'missing-permissions'
            ]
        ], $body);
    }
}
