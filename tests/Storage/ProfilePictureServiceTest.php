<?php

namespace Jinya\Tests\Storage;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Storage\ProfilePictureService;
use App\Storage\StorageBaseService;
use App\Tests\DatabaseAwareTestCase;
use Faker\Factory;
use Faker\Provider\Uuid;
use RuntimeException;

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
        copy('https://picsum.photos/200/300', $path);

        /** @phpstan-ignore-next-line */
        $this->service->saveProfilePicture($this->artist->id, fopen($path, 'rb+'));
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

    public function testSaveProfilePictureDataNull(): void
    {
        $this->expectException(RuntimeException::class);
        $this->service->saveProfilePicture($this->artist->id, null);
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
