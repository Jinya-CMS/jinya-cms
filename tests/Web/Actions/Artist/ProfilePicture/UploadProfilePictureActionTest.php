<?php

namespace Jinya\Tests\Web\Actions\Artist\ProfilePicture;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Storage\StorageBaseService;
use App\Web\Actions\Artist\ProfilePicture\UploadProfilePictureAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class UploadProfilePictureActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $request = $request->withBody(Stream::create('Test'));
        $response = new Response();
        $action = new UploadProfilePictureAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();

        $user = Artist::findById(CurrentUser::$currentUser->getIdAsInt());

        self::assertEquals('Test', file_get_contents(StorageBaseService::BASE_PATH . '/public/' . $user->profilePicture));
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Artist not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UploadProfilePictureAction();
        $result = $action($request, $response, ['id' => -1]);
        $result->getBody()->rewind();
    }
}
