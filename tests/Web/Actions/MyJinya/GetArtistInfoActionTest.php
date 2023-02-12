<?php

namespace Jinya\Tests\Web\Actions\MyJinya;

use App\Authentication\CurrentUser;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\MyJinya\GetArtistInfoAction;
use App\Web\Middleware\AuthorizationMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetArtistInfoActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request->withAttribute(AuthorizationMiddleware::LOGGED_IN_ARTIST, CurrentUser::$currentUser);
        $response = new Response();
        $action = new GetArtistInfoAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertJsonStringEqualsJsonString(json_encode(CurrentUser::$currentUser->format(), JSON_THROW_ON_ERROR), $result->getBody()->getContents());
    }
}
