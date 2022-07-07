<?php

namespace Jinya\Tests\Web\Middleware;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\TestRequestHandler;
use App\Web\Middleware\AuthorizationMiddleware;
use DateInterval;
use DateTime;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpForbiddenException;

class AuthorizationMiddlewareTest extends TestCase
{

    public function testProcess(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $middleware = new AuthorizationMiddleware('ROLE_WRITER');

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware->process($request, $handler);

        self::assertEquals($handler->request->getAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST)->getIdAsInt(), CurrentUser::$currentUser->getIdAsInt());
    }

    private function createApiKey(): ApiKey
    {
        $apiKey = new ApiKey();
        $apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $apiKey->validSince = (new DateTime())->add(new DateInterval('PT5M'));
        $apiKey->setApiKey();
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->userAgent = 'PHPUnit';
        $apiKey->create();

        return $apiKey;
    }

    public function testProcessNotEnoughPermission(): void
    {
        $this->expectException(HttpForbiddenException::class);

        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $middleware = new AuthorizationMiddleware('ROLE_ADMIN');

        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware->process($request, $handler);
    }
}
