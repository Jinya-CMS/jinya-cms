<?php

namespace Jinya\Tests\Web\Actions\Authentication;

use App\Authentication\CurrentUser;
use App\Web\Actions\Authentication\TwoFactorAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class TwoFactorActionTest extends TestCase
{

    public function test__invoke(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => CurrentUser::$currentUser->email]);

        $action = new TwoFactorAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeInvalidPassword(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start12345', 'username' => CurrentUser::$currentUser->email]);

        $action = new TwoFactorAction();
        $result = $action($request, $response, []);
        self::assertEquals(401, $result->getStatusCode());
    }

    public function test__invokeArtistNotFound(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start1234', 'username' => 'not-existing']);

        $action = new TwoFactorAction();
        $result = $action($request, $response, []);
        self::assertEquals(401, $result->getStatusCode());
    }
}
