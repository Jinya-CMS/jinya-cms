<?php

namespace Jinya\Tests\Web\Actions\MyJinya;

use App\Authentication\CurrentUser;
use App\Web\Actions\MyJinya\UpdateColorSchemeAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class UpdateColorSchemeActionTest extends TestCase
{

    public function test__invokeAuto(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser)
            ->withParsedBody([
                'colorScheme' => '',
            ]);
        $response = new Response();
        $action = new UpdateColorSchemeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
        self::assertNull(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function test__invokeLight(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser)
            ->withParsedBody([
                'colorScheme' => 'light',
            ]);
        $response = new Response();
        $action = new UpdateColorSchemeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse(CurrentUser::$currentUser->prefersColorScheme);
    }

    public function test__invokeDark(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser)
            ->withParsedBody([
                'colorScheme' => 'dark',
            ]);
        $response = new Response();
        $action = new UpdateColorSchemeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue(CurrentUser::$currentUser->prefersColorScheme);
    }
}
