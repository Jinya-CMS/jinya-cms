<?php

namespace Jinya\Tests\Web\Actions\Artist\ProfilePicture;

use App\Authentication\CurrentUser;
use App\Storage\ProfilePictureService;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Artist\ProfilePicture\DeleteProfilePictureAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class DeleteProfilePictureActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $service = new ProfilePictureService();
        $profilePictureAsText = 'Test';
        $service->saveProfilePicture(CurrentUser::$currentUser->getIdAsInt(), $profilePictureAsText);

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteProfilePictureAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNoProfilePicture(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteProfilePictureAction();
        $result = $action($request, $response, ['id' => CurrentUser::$currentUser->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeArtistNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('Artist not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteProfilePictureAction();
        $result = $action($request, $response, ['id' => -1]);
        $result->getBody()->rewind();
    }
}
