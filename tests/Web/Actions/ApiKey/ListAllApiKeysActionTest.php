<?php

namespace Jinya\Tests\Web\Actions\ApiKey;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\ApiKey\ListAllApiKeysAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class ListAllApiKeysActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new \DateTime();
        $apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new \DateTime();
        $apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new \DateTime();
        $apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser);

        $action = new ListAllApiKeysAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(0, $body['offset']);
        self::assertEquals(3, $body['itemsCount']);
        self::assertEquals(3, $body['totalCount']);
        self::assertCount(3, $body['items']);
    }
}
