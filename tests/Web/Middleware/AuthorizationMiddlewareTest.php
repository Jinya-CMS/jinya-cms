<?php

namespace Jinya\Cms\Web\Middleware;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Tests\TestRequestHandler;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
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

    public function testProcessApiKeyInvalid(): void
    {
        $apiKey = $this->createApiKey();
        $apiKey->validSince = DateTime::createFromFormat('Y-m-d\TH:i:s', '1970-01-01T00:00:00');
        $apiKey->update();

        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $apiKey->apiKey]);
        $middleware = new AuthorizationMiddleware('ROLE_ADMIN');

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $response = $middleware->process($request, $handler);

        $response->getBody()->rewind();
        $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $response->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'API key is invalid',
                'type' => 'invalid-api-key'
            ]
        ], $body);
    }
}
