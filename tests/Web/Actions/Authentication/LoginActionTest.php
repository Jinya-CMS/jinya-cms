<?php

namespace Jinya\Tests\Web\Actions\Authentication;

use App\Authentication\CurrentUser;
use App\Database\KnownDevice;
use App\Web\Actions\Authentication\LoginAction;
use App\Web\Exceptions\BadCredentialsException;
use App\Web\Exceptions\UnknownDeviceException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class LoginActionTest extends TestCase
{
    public function test__invokeTwoFactorCode(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456']);
        $action = new LoginAction();
        $result = $action($request, $response, []);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeTwoFactorCodeNoMail(): void
    {
        putenv('MAILER_HOST=none');
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456']);
        $action = new LoginAction();
        $result = $action($request, $response, []);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeTwoFactorCodeUserAgentHeader(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456'])
            ->withAddedHeader('User-Agent', 'Firefox');
        $action = new LoginAction();
        $result = $action($request, $response, []);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(BadCredentialsException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => 'test@example.com', 'twoFactorCode' => '123456']);
        $action = new LoginAction();
        $action($request, $response, []);
    }

    public function test__invokeTwoFactorCodeInvalidCode(): void
    {
        $this->expectException(UnknownDeviceException::class);
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '1234567';
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456']);
        $action = new LoginAction();
        $action($request, $response, []);
    }

    public function test__invokeKnownDevice(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '1234567';
        CurrentUser::$currentUser->update();

        $knownDevice = new KnownDevice();
        $knownDevice->userId = CurrentUser::$currentUser->getIdAsInt();
        $knownDevice->setDeviceKey();
        $knownDevice->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email])
            ->withAddedHeader('JinyaDeviceCode', $knownDevice->deviceKey);
        $action = new LoginAction();
        $result = $action($request, $response, []);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeUnknownDevice(): void
    {
        $this->expectException(BadCredentialsException::class);
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '1234567';
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email])
            ->withAddedHeader('JinyaDeviceCode', '1');
        $action = new LoginAction();
        $action($request, $response, []);
    }

    public function test__invokeNone(): void
    {
        $this->expectException(BadCredentialsException::class);
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email]);
        $action = new LoginAction();
        $action($request, $response, []);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        putenv('MAILER_HOST=jinya-mailhog');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }
}
