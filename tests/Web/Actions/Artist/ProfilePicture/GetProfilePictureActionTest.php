<?php

namespace Jinya\Tests\Web\Actions\Artist\ProfilePicture;

use App\Authentication\CurrentUser;
use App\Storage\ProfilePictureService;
use App\Web\Actions\Artist\ProfilePicture\GetProfilePictureAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetProfilePictureActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $service = new ProfilePictureService();
        $profilePictureAsText = 'Test';
        $service->saveProfilePicture(CurrentUser::$currentUser->getIdAsInt(), $profilePictureAsText);

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetProfilePictureAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($profilePictureAsText, $result->getBody()->getContents());
    }

    public function test__invokeNoProfilePicture(): void
    {
        $this->expectError();
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetProfilePictureAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Artist not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetProfilePictureAction();
        $result = $action($request, $response, ['id' => -1]);
        $result->getBody()->rewind();
    }
}
