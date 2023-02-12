<?php

namespace Jinya\Tests\Web\Actions\ApiKey;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\ApiKey\DeleteApiKeyAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DeleteApiKeyActionTest extends DatabaseAwareTestCase
{

    public function test__invokeApiKeyFound(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = '';
        $apiKey->validSince = new \DateTime();
        $apiKey->userId = CurrentUser::$currentUser->getIdAsInt();
        $apiKey->remoteAddress = '';
        $apiKey->setApiKey();
        $apiKey->create();

        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new DeleteApiKeyAction();
        $response = $action($request, $response, ['key' => $apiKey->apiKey]);
        self::assertEquals(204, $response->getStatusCode());
    }

    public function test__invokeApiKeyNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new DeleteApiKeyAction();
        $action($request, $response, ['key' => 'test']);
    }
}
