<?php

namespace Jinya\Cms\Authentication;

use DateInterval;
use DateTime;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Web\Exceptions\ApiKeyInvalidException;
use Jinya\Cms\Web\Exceptions\MissingPermissionsException;
use Nyholm\Psr7\ServerRequest;

class AuthenticationCheckerTest extends DatabaseAwareTestCase
{
    public function testCheckRequestForUserSuccessfulLogin(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);

        $request = new ServerRequest('POST', '', ['Authorization' => 'Bearer ' . $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);

        $request = new ServerRequest('POST', '');
        $artist = AuthenticationChecker::checkRequestForUser(
            $request->withCookieParams(['JinyaApiKey' => $this->createApiKey()->apiKey]),
            ROLE_WRITER
        );
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);
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

    public function testCheckRequestForUserSuccessfulLoginRoleReaderCascades(): void
    {
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, ROLE_READER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);

        $request = new ServerRequest('POST', '', ['Authorization' => 'Bearer ' . $this->createApiKey()->apiKey]);
        $artist = AuthenticationChecker::checkRequestForUser($request, ROLE_READER);
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);

        $request = new ServerRequest('POST', '');
        $artist = AuthenticationChecker::checkRequestForUser(
            $request->withCookieParams(['JinyaApiKey' => $this->createApiKey()->apiKey]),
            ROLE_READER
        );
        self::assertEquals(CurrentUser::$currentUser->email, $artist->email);
    }

    public function testCheckRequestForUserInvalidApiKey(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => 'Invalid API key']);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserInvalidApiKeyAuthorization(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $request = new ServerRequest('POST', '', ['Authorization' => 'Invalid API key']);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserInvalidApiKeyCookie(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $request = new ServerRequest('POST', '');
        AuthenticationChecker::checkRequestForUser(
            $request->withCookieParams(['JinyaApiKey' => 'Invalid API key']),
            ROLE_READER
        );
    }

    public function testCheckRequestForUserApiKeyExpired(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $apiKey = $this->createApiKey();
        $apiKey->validSince = new DateTime('19700101');
        $apiKey->update();

        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $apiKey->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserApiKeyExpiredAuthorization(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $apiKey = $this->createApiKey();
        $apiKey->validSince = new DateTime('19700101');
        $apiKey->update();

        $request = new ServerRequest('POST', '', ['Authorization' => 'Bearer ' . $apiKey->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserApiKeyExpiredCookie(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        $apiKey = $this->createApiKey();
        $apiKey->validSince = new DateTime('19700101');
        $apiKey->update();

        $request = new ServerRequest('POST', '');
        AuthenticationChecker::checkRequestForUser($request->withCookieParams(['JinyaApiKey' => $apiKey->apiKey]),
            ROLE_WRITER);
    }

    public function testCheckRequestForUserUserDisabled(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();
        $apiKey = $this->createApiKey();
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $apiKey->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserUserDisabledAuthorization(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();
        $apiKey = $this->createApiKey();
        $request = new ServerRequest('POST', '', ['Authorization' => 'Bearer ' . $apiKey->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_WRITER);
    }

    public function testCheckRequestForUserUserDisabledCookie(): void
    {
        $this->expectException(ApiKeyInvalidException::class);
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();
        $apiKey = $this->createApiKey();
        $request = new ServerRequest('POST', '');
        AuthenticationChecker::checkRequestForUser($request->withCookieParams(['JinyaApiKey' => $apiKey->apiKey]),
            ROLE_WRITER);
    }

    public function testCheckRequestForUserMissingRole(): void
    {
        $this->expectException(MissingPermissionsException::class);
        $request = new ServerRequest('POST', '', ['JinyaApiKey' => $this->createApiKey()->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_ADMIN);
    }

    public function testCheckRequestForUserMissingRoleAuthorization(): void
    {
        $this->expectException(MissingPermissionsException::class);
        $request = new ServerRequest('POST', '', ['Authorization' => 'Bearer ' . $this->createApiKey()->apiKey]);
        AuthenticationChecker::checkRequestForUser($request, ROLE_ADMIN);
    }

    public function testCheckRequestForUserMissingRoleCookie(): void
    {
        $this->expectException(MissingPermissionsException::class);
        $request = new ServerRequest('POST', '');
        AuthenticationChecker::checkRequestForUser(
            $request->withCookieParams(['JinyaApiKey' => $this->createApiKey()->apiKey]),
            ROLE_ADMIN
        );
    }
}
