<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\TotpMode;
use Jinya\Cms\Storage\ProfilePictureService;
use Jinya\Cms\Storage\StorageBaseService;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;

class ArtistControllerTest extends DatabaseAwareTestCase
{
    private function getController(mixed $body): ArtistController
    {
        $controller = new ArtistController();
        $request = new ServerRequest('', '');
        $controller->request = $request->withParsedBody($body);
        if ($body instanceof StreamInterface) {
            $controller->request = $controller->request->withBody($body);
        } elseif (is_string($body)) {
            $controller->request = $controller->request->withBody(Stream::create($body));
        }
        $controller->body = $body;

        return $controller;
    }

    public function testActivateArtist(): void
    {
        CurrentUser::$currentUser->enabled = false;
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->activateArtist(CurrentUser::$currentUser->id);
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertTrue($user->enabled);
    }

    public function testActivateArtistArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->activateArtist(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeactivateArtist(): void
    {
        CurrentUser::$currentUser->enabled = true;
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->deactivateArtist(CurrentUser::$currentUser->id);
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertFalse($user->enabled);
    }

    public function testDeactivateArtistArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->deactivateArtist(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeactivateArtistDeactivateLastAdmin(): void
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

        CurrentUser::$currentUser->roles = ['ROLE_WRITER'];
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->deactivateArtist(CurrentUser::$currentUser->id);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(409, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'You cannot deactivate the last admin',
                'type' => 'cannot-deactivate-last-admin',
            ],
        ], $body);
    }

    public function testResetTotp(): void
    {
        CurrentUser::$currentUser->totpMode = TotpMode::Otphp;
        CurrentUser::$currentUser->totpSecret = 'test';
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->resetTotp(CurrentUser::$currentUser->id);
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertNull($user->totpSecret);
        self::assertEquals(TotpMode::Email, $user->totpMode);
    }

    public function testResetTotpArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->resetTotp(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeleteArtist(): void
    {
        CurrentUser::$currentUser->enabled = true;
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->deleteArtist(CurrentUser::$currentUser->id);
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertNull($user);
    }

    public function testDeleteArtistArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->deleteArtist(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeleteArtistDeleteLastAdmin(): void
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

        CurrentUser::$currentUser->roles = ['ROLE_WRITER'];
        CurrentUser::$currentUser->update();

        $controller = $this->getController(null);
        $result = $controller->deleteArtist(CurrentUser::$currentUser->id);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(409, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'You cannot delete the last admin',
                'type' => 'cannot-delete-last-admin',
            ],
        ], $body);
    }

    public function testUpdate(): void
    {
        $controller = $this->getController(
            ['artistName' => 'test', 'password' => 'test', 'email' => 'test@test.de', 'roles' => ['ROLE_READER']]
        );
        $result = $controller->updateArtist(CurrentUser::$currentUser->id);
        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(204, $result->getStatusCode());
        self::assertEquals('test', $user->artistName);
        self::assertEquals('test@test.de', $user->email);
        self::assertEquals(['ROLE_READER'], $user->roles);
    }

    public function testUpdateArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->updateArtist(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateArtistUniqueFailed(): void
    {
        $artist = new Artist();
        $artist->artistName = 'test';
        $artist->email = 'test@test.de';
        $artist->setPassword('test');
        $artist->create();

        $controller = $this->getController(
            ['artistName' => 'test', 'password' => 'test', 'email' => CurrentUser::$currentUser->email, 'roles' => ['ROLE_READER']]
        );
        $result = $controller->updateArtist($artist->id);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(409, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Email already used',
                'type' => 'unique-failed',
            ],
        ], $body);
    }

    public function testCreate(): void
    {
        $controller = $this->getController(
            ['artistName' => 'test', 'password' => 'test', 'email' => 'test@test.de', 'roles' => ['ROLE_READER']]
        );
        $result = $controller->createArtist();
        $result->getBody()->rewind();
        $user = Artist::findById(json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)['id']);

        self::assertEquals(201, $result->getStatusCode());
        self::assertEquals('test', $user->artistName);
        self::assertEquals('test@test.de', $user->email);
        self::assertEquals(['ROLE_READER'], $user->roles);
    }

    public function testCreateArtistUniqueFailed(): void
    {
        $controller = $this->getController(
            ['artistName' => 'test', 'password' => 'test', 'email' => CurrentUser::$currentUser->email, 'roles' => ['ROLE_READER']]
        );
        $result = $controller->createArtist();

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(409, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Email already used',
                'type' => 'unique-failed',
            ],
        ], $body);
    }

    public function testGetProfilePicture(): void
    {
        $service = new ProfilePictureService();
        $profilePictureAsText = 'Test';
        $service->saveProfilePicture(CurrentUser::$currentUser->id, $profilePictureAsText);

        $controller = $this->getController(null);
        $result = $controller->getProfilePicture(CurrentUser::$currentUser->id);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($profilePictureAsText, $result->getBody()->getContents());
    }

    public function testGetProfilePictureArtistNotFound(): void
    {
        $service = new ProfilePictureService();
        $profilePictureAsText = 'Test';
        $service->saveProfilePicture(CurrentUser::$currentUser->id, $profilePictureAsText);

        $controller = $this->getController(null);
        $result = $controller->getProfilePicture(-1);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testDeleteProfilePicture(): void
    {
        $service = new ProfilePictureService();
        $profilePictureAsText = 'Test';
        $service->saveProfilePicture(CurrentUser::$currentUser->id, $profilePictureAsText);

        $controller = $this->getController(null);
        $result = $controller->deleteProfilePicture(CurrentUser::$currentUser->id);
        $result->getBody()->rewind();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testDeleteProfilePictureNoProfilePicture(): void
    {
        $controller = $this->getController(null);
        $result = $controller->deleteProfilePicture(CurrentUser::$currentUser->id);
        $result->getBody()->rewind();

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testDeleteProfilePictureArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->deleteProfilePicture(-1);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUploadProfilePicture(): void
    {
        $controller = $this->getController(Stream::create('Test'));
        $result = $controller->uploadProfilePicture(CurrentUser::$currentUser->id);

        $user = Artist::findById(CurrentUser::$currentUser->id);

        self::assertEquals(
            'Test',
            file_get_contents(StorageBaseService::BASE_PATH . '/public/' . $user->profilePicture)
        );
        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUploadProfilePictureArtistNotFound(): void
    {
        $controller = $this->getController(null);
        $result = $controller->uploadProfilePicture(-1);
        $result->getBody()->rewind();

        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Artist not found',
                'type' => 'not-found',
            ],
        ], $body);
    }
}
