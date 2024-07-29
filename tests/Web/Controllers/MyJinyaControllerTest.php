<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use OTPHP\TOTP;
use Psr\Http\Message\StreamInterface;

class MyJinyaControllerTest extends DatabaseAwareTestCase
{
    private function getController(mixed $body): MyJinyaController
    {
        $controller = new MyJinyaController();
        $controller->request = (new ServerRequest('', ''));
        if ($body instanceof StreamInterface) {
            $controller->request = $controller->request->withBody($body);
        }
        $controller->body = $body;

        return $controller;
    }

    public function testGetMyProfile(): void
    {
        $result = $this->getController([])->getMyProfile();
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(CurrentUser::$currentUser->format(), $body);
    }

    public function testUpdateMyProfile(): void
    {
        $controller = $this->getController([
            'email' => 'test@example.com',
            'artistName' => 'Theo Test',
            'aboutMe' => 'Lorem ipsum',
        ]);
        $result = $controller->updateMyProfile();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdateColorSchemeAuto(): void
    {
        $controller = $this->getController([
            'colorScheme' => '',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertNull(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function testUpdateColorSchemeLight(): void
    {
        $controller = $this->getController([
            'colorScheme' => 'light',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function testUpdateColorSchemeDark(): void
    {
        $controller = $this->getController([
            'colorScheme' => 'dark',
        ]);
        $result = $controller->updateColorScheme();

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function testUploadProfilePicture(): void
    {
        $controller = $this->getController(Stream::create('Test'));
        $result = $controller->uploadProfilePicture();
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals('Test', file_get_contents(StorageBaseService::PUBLIC_PATH . $user->profilePicture));
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testChangeOtpModeToTotp(): void
    {
        $controller = $this->getController([]);
        $result = $controller->changeOtpModeToTotp();
        $user = Artist::findById(CurrentUser::$currentUser->id);

        $otp = TOTP::createFromSecret($user->totpSecret);
        $otp->setLabel('Jinya CMS');
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($user->totpSecret, $body['secret']);
        self::assertArrayHasKey('qrCode', $body);
    }

    public function testVerifyOtpModeTotp(): void
    {
        $controller = $this->getController([]);
        $controller->changeOtpModeToTotp();

        $user = Artist::findById(CurrentUser::$currentUser->id);

        $otp = TOTP::createFromSecret($user->totpSecret);
        $code = $otp->now();

        $controller = $this->getController(['code' => $code]);
        $result = $controller->verifyOtpModeTotp();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testVerifyOtpModeTotpInvalidCode(): void
    {
        $controller = $this->getController(['code' => 'asdasd']);
        $controller->changeOtpModeToTotp();

        $user = Artist::findById(CurrentUser::$currentUser->id);
        $result = $controller->verifyOtpModeTotp();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(400, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'The code is not valid',
                'type' => 'code-invalid',
            ],
        ], $body);
    }

    public function testUpdatePreferences(): void
    {
        $controller = $this->getController([
            'loginMailEnabled' => false,
            'newDeviceMailEnabled' => true,
        ]);
        $result = $controller->updatePreferences();

        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse(CurrentUser::$currentUser->loginMailEnabled);
        self::assertTrue(CurrentUser::$currentUser->newDeviceMailEnabled);
    }
}
