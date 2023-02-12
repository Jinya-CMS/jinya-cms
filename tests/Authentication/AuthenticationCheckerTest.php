<?php

namespace Jinya\Tests\Authentication;

use App\Authentication\AuthenticationChecker;
use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Tests\DatabaseAwareTestCase;
use DateInterval;
use DateTime;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpForbiddenException;

class AuthenticationCheckerTest extends DatabaseAwareTestCase
{

    public function testCheckRequestForUserSuccessfulLogin(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_WRITER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);
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

    public function testCheckRequestForUserSuccessfulLoginRoleReaderCascades(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_READER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);
    }

    public function testCheckRequestForUserInvalidApiKey(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => 'Invalid API key']);
        AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_WRITER);
    }

    public function testCheckRequestForUserApiKeyExpired(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $apiKey = $this->createApiKey();
        $apiKey->validSince = new DateTime('19700101');
        $apiKey->update();

        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $apiKey->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_WRITER);
    }

    public function testCheckRequestForUserUserDisabled(): void
    {
        $this->expectException(HttpForbiddenException::class);
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();
        $apiKey = $this->createApiKey();
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $apiKey->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_WRITER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);
    }

    public function testCheckRequestForUserMissingRole(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, AuthenticationChecker::ROLE_ADMIN);
    }
}
