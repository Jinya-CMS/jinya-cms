<?php

namespace Jinya\Tests\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Artist\ActivateArtistAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class ActivateArtistActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new ActivateArtistAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $user = Artist::findById(CurrentUser::$currentUser->getIdAsInt());

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue($user->enabled);
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage("Artist doesn't exist");
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new ActivateArtistAction();
        $action($request, $response, ['id' => -1]);
    }
}
