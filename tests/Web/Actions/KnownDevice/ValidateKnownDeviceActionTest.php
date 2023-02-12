<?php

namespace Jinya\Tests\Web\Actions\KnownDevice;

use App\Authentication\CurrentUser;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\KnownDevice\ValidateKnownDeviceAction;
use App\Web\Exceptions\BadCredentialsException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class ValidateKnownDeviceActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->getIdAsInt();
        $device->setDeviceKey();
        $device->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new ValidateKnownDeviceAction();
        $result = $action($request, $response, ['key' => $device->deviceKey]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNotFound(): void
    {
        $this->expectException(BadCredentialsException::class);
        $this->expectExceptionMessage('Known device is unknown');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new ValidateKnownDeviceAction();
        $action($request, $response, ['key' => '']);
    }
}
