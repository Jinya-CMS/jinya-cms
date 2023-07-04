<?php

namespace Jinya\Tests\Web\Actions\Artist;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Artist\CreateArtistAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateArtistActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();

        $action = new CreateArtistAction();
        $result = $action($request->withParsedBody(['artistName' => 'test', 'password' => 'test', 'email' => 'test@test.de', 'roles' => ['ROLE_READER']]), $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();
        $user = Artist::findById(json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['id']);

        self::assertEquals(201, $result->getStatusCode());
        self::assertEquals($user->artistName, 'test');
        self::assertEquals($user->email, 'test@test.de');
        self::assertEquals($user->roles, ['ROLE_READER']);
    }
}
