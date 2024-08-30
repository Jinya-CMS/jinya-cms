<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\KnownDevice;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Nyholm\Psr7\ServerRequest;

class KnownDeviceControllerTest extends DatabaseAwareTestCase
{
    private function getController(): KnownDeviceController
    {
        $controller = new KnownDeviceController();
        $controller->request = new ServerRequest('POST', '');

        return $controller;
    }

    public function testValidateKnownDevice(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->id;
        $device->create();

        $controller = $this->getController();
        $result = $controller->validateKnownDevice($device->deviceKey);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testValidateKnownDeviceNotFound(): void
    {
        $controller = $this->getController();
        $result = $controller->validateKnownDevice('1');

        self::assertEquals(403, $result->getStatusCode());
    }


    public function testGetKnownDevices(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->id;
        $device->create();

        $controller = $this->getController();
        $result = $controller->getKnownDevices();
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertNotEmpty($result->getBody()->getContents());
    }

    public function testDeleteKnownDevice(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->id;
        $device->create();

        $controller = $this->getController();
        $result = $controller->deleteKnownDevice($device->deviceKey);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testDeleteKnownDeviceDeviceDoesNotExist(): void
    {
        $controller = $this->getController();
        $result = $controller->deleteKnownDevice('1');
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Known device not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
