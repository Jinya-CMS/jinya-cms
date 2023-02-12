<?php

namespace Jinya\Tests\Database;

use App\Authentication\CurrentUser;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use App\Utils\UuidGenerator;
use RuntimeException;

class KnownDeviceTest extends DatabaseAwareTestCase
{

    private function createKnownDevice(bool $execute = true): KnownDevice
    {
        $knownDevice = new KnownDevice();
        $knownDevice->userId = CurrentUser::$currentUser->getIdAsInt();
        $knownDevice->userAgent = 'PHPUnit';
        $knownDevice->remoteAddress = '127.0.0.1';
        $knownDevice->setDeviceKey();

        if ($execute) {
            $knownDevice->create();
        }

        return $knownDevice;
    }

    public function testFindById(): void
    {
        $this->expectException(RuntimeException::class);
        KnownDevice::findById(1);
    }

    public function testFindAll(): void
    {
        $this->expectException(RuntimeException::class);
        KnownDevice::findAll();
    }

    public function testUpdate(): void
    {
        $knownDevice = $this->createKnownDevice();
        $oldKey = $knownDevice->deviceKey;
        $knownDevice->setDeviceKey();
        $knownDevice->update();

        $this->assertNotEquals($oldKey, $knownDevice->deviceKey);
    }

    public function testFindByKeyword(): void
    {
        $this->expectException(RuntimeException::class);
        KnownDevice::findByKeyword('Test');
    }

    public function testFormat(): void
    {
        $device = $this->createKnownDevice();
        $this->assertEquals([
            'remoteAddress' => $device->remoteAddress,
            'userAgent' => $device->userAgent,
            'key' => $device->deviceKey,
        ], $device->format());
    }

    public function testDelete(): void
    {
        $device = $this->createKnownDevice();
        $device->delete();

        $this->assertTrue(true);
    }

    public function testFindByArtist(): void
    {
        $this->createKnownDevice();
        $this->createKnownDevice();
        $this->createKnownDevice();

        $found = KnownDevice::findByArtist(CurrentUser::$currentUser->getIdAsInt());
        $this->assertCount(3, iterator_to_array($found));
    }

    public function testFindByArtistNotExists(): void
    {
        $this->createKnownDevice();
        $this->createKnownDevice();
        $this->createKnownDevice();

        $found = KnownDevice::findByArtist(CurrentUser::$currentUser->getIdAsInt());
        $this->assertCount(3, iterator_to_array($found));
    }

    public function testCreate(): void
    {
        $artist = new Artist();
        $artist->email = 'firstuser1@example.com';
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'First user1';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('start1234');
        $artist->roles[] = 'ROLE_READER';
        $artist->roles[] = 'ROLE_WRITER';

        $artist->create();
        $this->createKnownDevice();
        $this->createKnownDevice();
        $this->createKnownDevice();

        $found = KnownDevice::findByArtist($artist->getIdAsInt());
        $this->assertCount(0, iterator_to_array($found));
    }

    public function testSetDeviceKey(): void
    {
        $knownDevice = $this->createKnownDevice();
        $oldKey = $knownDevice->deviceKey;
        $knownDevice->setDeviceKey();

        $this->assertNotEquals($oldKey, $knownDevice->deviceKey);
    }

    public function testFindByCode(): void
    {
        $knownDevice = $this->createKnownDevice();
        $this->createKnownDevice();
        $this->createKnownDevice();

        $foundDevice = KnownDevice::findByCode($knownDevice->deviceKey);
        $this->assertEquals($knownDevice, $foundDevice);
    }

    public function testFindByCodeNotFound(): void
    {
        $this->createKnownDevice();
        $this->createKnownDevice();
        $this->createKnownDevice();

        $foundDevice = KnownDevice::findByCode(UuidGenerator::generateV4());
        $this->assertNull($foundDevice);
    }
}
