<?php

namespace Jinya\Tests\Storage;

use App\Database\Artist;
use App\Database\Exceptions\EmptyResultException;
use App\Storage\ProfilePictureService;
use App\Storage\StorageBaseService;
use Faker\Factory;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProfilePictureServiceTest extends TestCase
{
    private ProfilePictureService $service;
    private Artist $artist;

    public function testSaveAndDeleteProfilePictureString(): void
    {
        $profilePictureAsText = 'Test';
        $this->service->saveProfilePicture($this->artist->getIdAsInt(), $profilePictureAsText);
        $loadedArtist = Artist::findByEmail($this->artist->email);
        self::assertFileExists(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        $this->service->deleteProfilePicture($loadedArtist->getIdAsInt());
        self::assertFileDoesNotExist(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);
    }

    public function testSaveAndDeleteProfilePictureFile(): void
    {
        copy('https://picsum.photos/200/300', __ROOT__ . '/tmp/file');

        $this->service->saveProfilePicture($this->artist->getIdAsInt(), fopen(__ROOT__ . '/tmp/file', 'rb+'));
        $loadedArtist = Artist::findByEmail($this->artist->email);
        self::assertFileExists(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        $this->service->deleteProfilePicture($loadedArtist->getIdAsInt());
        self::assertFileDoesNotExist(StorageBaseService::BASE_PATH . '/public/' . $loadedArtist->profilePicture);

        @unlink(__ROOT__ . '/tmp/file');
    }

    public function testSaveProfilePictureArtistNotFound(): void
    {
        $this->expectException(EmptyResultException::class);
        $this->service->saveProfilePicture(-1, '');
    }

    public function testSaveProfilePictureDataNull(): void
    {
        $this->expectException(RuntimeException::class);
        $this->service->saveProfilePicture($this->artist->getIdAsInt(), null);
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
