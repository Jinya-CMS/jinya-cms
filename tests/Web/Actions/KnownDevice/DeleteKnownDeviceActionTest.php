<?php

namespace Jinya\Tests\Web\Actions\KnownDevice;

use App\Authentication\CurrentUser;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\KnownDevice\DeleteKnownDeviceAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DeleteKnownDeviceActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->getIdAsInt();
        $device->setDeviceKey();
        $device->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteKnownDeviceAction();
        $result = $action($request, $response, ['key' => $device->deviceKey]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeDeviceDoesNotExist(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Known device not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteKnownDeviceAction();
        $result = $action($request, $response, ['key' => '']);
    }
}
