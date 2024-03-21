<?php

namespace Jinya\Tests\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Artist\UpdateArtistAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class UpdateArtistActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new UpdateArtistAction();
        $result = $action($request->withParsedBody(['artistName' => 'test', 'password' => 'test', 'email' => 'test@test.de', 'roles' => ['ROLE_READER']]), $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $user = Artist::findById(CurrentUser::$currentUser->getIdAsInt());

        self::assertEquals(204, $result->getStatusCode());
        self::assertEquals($user->artistName, 'test');
        self::assertEquals($user->email, 'test@test.de');
        self::assertEquals($user->roles, ['ROLE_READER']);
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Artist not found');
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new UpdateArtistAction();
        $action($request, $response, ['id' => -1]);
    }
}
