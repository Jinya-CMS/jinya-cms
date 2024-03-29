<?php

namespace Jinya\Tests\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Controllers\AuthenticationController;
use Nyholm\Psr7\ServerRequest;

class AuthenticationControllerTest extends DatabaseAwareTestCase
{
    private function getController(
        mixed $body,
        bool $withUserAgent = false,
        string $deviceKey = null
    ): AuthenticationController {
        $controller = new AuthenticationController();
        $controller->request = (new ServerRequest('', '', serverParams: $_SERVER))
            ->withParsedBody($body);
        $controller->body = $body;
        if ($withUserAgent) {
            $controller->request = $controller->request->withHeader(
                'User-Agent',
                'Mozilla/5.0 (X11; Linux x86_64; rv:125.0) Gecko/20100101 Firefox/125.0'
            );
        }
        if ($deviceKey) {
            $controller->request = $controller->request->withAddedHeader('JinyaDeviceCode', $deviceKey);
        }

        return $controller;
    }

    private string $mailerHost = '';

    protected function tearDown(): void
    {
        parent::tearDown();
        putenv('MAILER_HOST=' . $this->mailerHost);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['REMOTE_ADDR'] = '185.216.179.123';
        $mailerHost = getenv('MAILER_HOST');
    }

    public function testChangePassword(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(['password' => 'start', 'oldPassword' => 'start1234']);
        $result = $controller->changePassword();

        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue($user->validatePassword('start'));
    }

    public function testChangePasswordWrongPassword(): void
    {
        CurrentUser::$currentUser->setPassword('start12345');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(['password' => 'start', 'oldPassword' => 'start1234']);
        $result = $controller->changePassword();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(403, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'The old password is wrong',
                'type' => 'wrong-password'
            ]
        ], $body);
    }

    public function testLoginTwoFactorCode(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456']
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function testLoginTwoFactorCodeNoMail(): void
    {
        putenv('MAILER_HOST=none');
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456']
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function testLoginTwoFactorCodeUserAgentHeader(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123456'],
            true
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function testLoginArtistNotFound(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => 'test@example.com', 'twoFactorCode' => '123456'],
            true
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }

    public function testLoginTwoFactorCodeInvalidCode(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '123456';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email, 'twoFactorCode' => '123457'],
            true
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }

    public function testLoginKnownDevice(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $knownDevice = new KnownDevice();
        $knownDevice->userId = CurrentUser::$currentUser->id;
        $knownDevice->create();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email],
            true,
            $knownDevice->deviceKey
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('apiKey', $body);
        self::assertArrayHasKey('deviceCode', $body);
        self::assertArrayHasKey('roles', $body);
        self::assertEquals(200, $result->getStatusCode());
    }

    public function testLoginUnknownDevice(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->twoFactorToken = '1234567';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start1234', 'username' => CurrentUser::$currentUser->email],
            true,
            '1'
        );
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }

    public function testLoginNone(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(['password' => 'start1234', 'username' =>  CurrentUser::$currentUser->email], true);
        $result = $controller->login();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }

    public function testTwoFactor(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email]);
        $result = $controller->twoFactorCode();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testTwoFactorInvalidPassword(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(
            ['password' => 'start12345', 'username' => CurrentUser::$currentUser->email]
        );
        $result = $controller->twoFactorCode();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }

    public function testTwoFactorArtistNotFound(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $controller = $this->getController(['password' => 'start1234', 'username' => 'test@example.com']);
        $result = $controller->twoFactorCode();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(401, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], $body);
    }
}
