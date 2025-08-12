<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Faker\Factory;
use Faker\Provider\Uuid;

class ProfilePictureServiceTest extends DatabaseAwareTestCase
{
    private ProfilePictureService $service;
    private Artist $artist;

    public function testSaveAndDeleteProfilePictureString(): void
    {
        $profilePictureAsText = 'Test';
        $this->service->saveProfilePicture($this->artist->id, $profilePictureAsText);
        $loadedArtist = Artist::findByEmail($this->artist->email);
        self::assertFileExists(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        $this->service->deleteProfilePicture($loadedArtist->id);
        self::assertFileDoesNotExist(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);
    }

    public function testSaveAndDeleteProfilePictureFile(): void
    {
        $path = __ROOT__ . '/tmp/' . Uuid::uuid();
        copy('https://jinya.de/img/startpage.webp', $path);

        /** @phpstan-ignore-next-line */
        $this->service->saveProfilePicture($this->artist->id, file_get_contents($path));
        $loadedArtist = Artist::findByEmail($this->artist->email);
        self::assertFileExists(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        $this->service->deleteProfilePicture($loadedArtist->id);
        self::assertFileDoesNotExist(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        @unlink($path);
    }

    public function testSaveProfilePictureArtistNotFound(): void
    {
        $this->expectException(EmptyResultException::class);
        $this->service->saveProfilePicture(-1, '');
    }

    public function testDeleteProfilePictureArtistNotFound(): void
    {
        $this->expectException(EmptyResultException::class);
        $this->service->deleteProfilePicture(-1);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProfilePictureService();

        $this->artist = new Artist();
        $this->artist->email = Uuid::uuid() . Factory::create()->safeEmail();
        $this->artist->aboutMe = 'About me';
        $this->artist->profilePicture = 'profilepicture';
        $this->artist->artistName = Uuid::uuid();
        $this->artist->enabled = true;
        $this->artist->roles = [];
        $this->artist->setPassword('start');
        $this->artist->create();
    }
}
