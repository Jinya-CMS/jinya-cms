<?php

namespace Jinya\Tests\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Web\Actions\Artist\ActivateArtistAction;
use App\Web\Actions\Artist\DeactivateArtistAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class DectivateArtistActionTest extends TestCase
{

    public function test__invoke(): void
    {
        CurrentUser::$currentUser->enabled = true;
        CurrentUser::$currentUser->update();

        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new DeactivateArtistAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $user = Artist::findById(CurrentUser::$currentUser->getIdAsInt());

        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse($user->enabled);
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
