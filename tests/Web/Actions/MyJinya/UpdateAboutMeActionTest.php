<?php

namespace Jinya\Tests\Web\Actions\MyJinya;

use App\Authentication\CurrentUser;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\MyJinya\UpdateAboutMeAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class UpdateAboutMeActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request
            ->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser)
            ->withParsedBody([
                'email' => 'test@example.com',
                'artistName' => 'Theo Test',
                'aboutMe' => 'Lorem ipsum',
            ]);
        $response = new Response();
        $action = new UpdateAboutMeAction();
        $result = $action($request, $response, []);
        self::assertEquals(204, $result->getStatusCode());
    }
}
