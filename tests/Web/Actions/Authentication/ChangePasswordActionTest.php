<?php

namespace Jinya\Tests\Web\Actions\Authentication;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Authentication\ChangePasswordAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpForbiddenException;

class ChangePasswordActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        CurrentUser::$currentUser->setPassword('start1234');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start', 'oldPassword' => 'start1234'])
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser);
        $action = new ChangePasswordAction();
        $result = $action($request, $response, []);

        $user = Artist::findById(CurrentUser::$currentUser->getIdAsInt());

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue(password_verify('start', $user->password));
    }


    public function test__invokeWrongPassword(): void
    {
        $this->expectException(HttpForbiddenException::class);
        $this->expectExceptionMessage('Old password is invalid');
        CurrentUser::$currentUser->setPassword('start12345');
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request
            ->withParsedBody(['password' => 'start', 'oldPassword' => 'start1234'])
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser);
        $action = new ChangePasswordAction();
        $action($request, $response, []);
    }
}
