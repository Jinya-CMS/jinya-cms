<?php

namespace Jinya\Tests\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\ApiKeyController;
use DateTime;
use Nyholm\Psr7\ServerRequest;

class ApiKeyControllerTest extends DatabaseAwareTestCase
{
    private function getController(): ApiKeyController
    {
        $controller = new ApiKeyController();

        $request = new ServerRequest('', '');

        $controller->request = $request;

        return $controller;
    }

    public function testDeleteApiKeyApiKeyFound(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new DateTime();
        $apiKey->userId = CurrentUser::$currentUser->id;
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();

        $controller = $this->getController();
        $response = $controller->deleteApiKey($apiKey->apiKey);

        self::assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteApiKeyApiKeyNotFound(): void
    {
        $controller = $this->getController();
        $response = $controller->deleteApiKey('test');

        $response->getBody()->rewind();

        self::assertEquals(404, $response->getStatusCode());
        self::assertGreaterThan(0, $response->getBody()->getSize());
    }

    public function testListAllApiKeys(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new DateTime();
        $apiKey->userId = CurrentUser::$currentUser->id;
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new DateTime();
        $apiKey->userId = CurrentUser::$currentUser->id;
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new DateTime();
        $apiKey->userId = CurrentUser::$currentUser->id;
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();

        $controller = $this->getController();
        $result = $controller->getApiKeys();
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(0, $body['offset']);
        self::assertEquals(3, $body['itemsCount']);
        self::assertEquals(3, $body['totalCount']);
        self::assertCount(3, $body['items']);
    }
}
