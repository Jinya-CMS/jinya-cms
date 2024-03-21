<?php

namespace Jinya\Tests\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Artist\DeactivateArtistAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DectivateArtistActionTest extends DatabaseAwareTestCase
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

        $action = new DeactivateArtistAction();
        $action($request, $response, ['id' => -1]);
    }

    public function test__invokeDeactivateLastAdmin(): void
    {
        $artist = new Artist();
        $artist->email = 'test@example.com';
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'Test Artist';
        $artist->enabled = true;
        $artist->roles = ['ROLE_ADMIN'];
        $artist->setPassword('start1234');
        $artist->create();

        $this->expectException(ConflictException::class);
        $request = new ServerRequest('', '');
        $response = new Response();

        CurrentUser::$currentUser->roles = ['ROLE_ADMIN'];
        CurrentUser::$currentUser->update();

        $action = new DeactivateArtistAction();
        $action($request, $response, ['id' => $artist->getIdAsInt()]);
    }
}
