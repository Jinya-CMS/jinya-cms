<?php

namespace Jinya\Tests\Web\Actions\KnownDevice;

use App\Authentication\CurrentUser;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\KnownDevice\ListAllKnownDevicesAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class ListAllKnownDevicesActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $device = new KnownDevice();
        $device->userId = CurrentUser::$currentUser->getIdAsInt();
        $device->setDeviceKey();
        $device->create();

        $request = new ServerRequest('', '');
        $request = $request->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser);
        $response = new Response();
        $action = new ListAllKnownDevicesAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();
        self::assertEquals(200, $result->getStatusCode());
        self::assertNotEmpty($result->getBody()->getContents());
    }
}
