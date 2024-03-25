<?php

namespace Jinya\Tests\Database;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\KnownDevice;
use App\Tests\DatabaseAwareTestCase;
use DateTime;
use InvalidArgumentException;

use PDOException;

use function PHPUnit\Framework\assertFalse;

class ArtistTest extends DatabaseAwareTestCase
{
    private function createArtist(
        bool $isAdmin = true,
        bool $isWriter = true,
        bool $isReader = true,
        bool $enabled = true,
        string $email = 'test@example.com',
        string $password = 'test1234',
        bool $execute = true
    ): Artist {
        $artist = new Artist();
        $artist->email = $email;
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'Test Artist';
        $artist->enabled = $enabled;
        $artist->roles = [];
        $artist->setPassword($password);
        if ($isAdmin) {
            $artist->roles[] = 'ROLE_ADMIN';
        }
        if ($isReader) {
            $artist->roles[] = 'ROLE_READER';
        }
        if ($isWriter) {
            $artist->roles[] = 'ROLE_WRITER';
        }

        if ($execute) {
            $artist->create();
        }

        return $artist;
    }

    protected function setUp(): void
    {
    }

    public function testValidatePasswordEnabledAndValid(): void
    {
        $artist = $this->createArtist();
        $this->assertTrue($artist->validatePassword('test1234'));
    }

    public function testValidatePasswordNotEnabledButValid(): void
    {
        $artist = $this->createArtist(enabled: false);
        $this->assertFalse($artist->validatePassword('test1234'));
    }

    public function testValidatePasswordEnabledButNotValid(): void
    {
        $artist = $this->createArtist();
        $this->assertFalse($artist->validatePassword('test12345'));
    }

    public function testValidatePasswordNotEnabledAndNotValid(): void
    {
        $artist = $this->createArtist(enabled: false);
        $this->assertFalse($artist->validatePassword('test12345'));
    }

    public function testCreateAllValid(): void
    {
        $artist = $this->createArtist(execute: false);
        $artist->create();

        $savedArtist = Artist::findById($artist->id);
        $this->assertEquals($artist, $savedArtist);
    }

    public function testFormatPrefersColorSchemeDark(): void
    {
        $artist = $this->createArtist(execute: false);
        $artist->prefersColorScheme = true;
        $artist->create();

        $formatted = $artist->format();
        $this->assertEquals('dark', $formatted['colorScheme']);
    }

    public function testFormatPrefersColorSchemeLight(): void
    {
        $artist = $this->createArtist(execute: false);
        $artist->prefersColorScheme = false;
        $artist->create();

        $formatted = $artist->format();
        $this->assertEquals('light', $formatted['colorScheme']);
    }

    public function testFormatPrefersColorSchemeAuto(): void
    {
        $artist = $this->createArtist(execute: false);
        $artist->prefersColorScheme = null;
        $artist->create();

        $formatted = $artist->format();
        $this->assertEquals('auto', $formatted['colorScheme']);
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $artist = $this->createArtist(execute: false);
        $artist->create();
        $artist->create();
    }

    public function testValidateDeviceValid(): void
    {
        $artist = $this->createArtist();
        $knownDevice = new KnownDevice();
        $knownDevice->userAgent = 'PHPUnit';
        $knownDevice->remoteAddress = '127.0.0.1';
        $knownDevice->userId = $artist->id;

        $knownDevice->create();

        $valid = $artist->validateDevice($knownDevice->deviceKey);
        $this->assertTrue($valid);
    }

    public function testValidateDeviceInvalid(): void
    {
        $artist = $this->createArtist();

        $valid = $artist->validateDevice('123456789');
        $this->assertFalse($valid);
    }

    public function testUnlockAccount(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $this->assertEquals(5, $artist->failedLoginAttempts);
        $this->assertNotNull($artist->loginBlockedUntil);
        $artist->unlockAccount();
        $this->assertTrue(true);
    }

    public function testRegisterFailedLoginOneFailedAttempt(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        $this->assertEquals(1, $artist->failedLoginAttempts);
    }

    public function testRegisterFailedLoginFiveFailedAttempts(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $this->assertEquals(5, $artist->failedLoginAttempts);
        $this->assertNotNull($artist->loginBlockedUntil);
    }

    public function testChangePasswordValidOldPassword(): void
    {
        $artist = $this->createArtist();
        $this->assertTrue($artist->changePassword('test1234', 'start1234'));
        $this->assertTrue($artist->validatePassword('start1234'));
    }

    public function testChangePasswordWithApiKey(): void
    {
        $artist = $this->createArtist();
        $apiKey = new ApiKey();
        $apiKey->userId = $artist->id;
        $apiKey->setApiKey();
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->userAgent = 'PHPUnit';
        $apiKey->validSince = new DateTime();
        $apiKey->create();

        $this->assertTrue($artist->changePassword('test1234', 'start1234'));
        $this->assertTrue($artist->validatePassword('start1234'));
        $this->assertCount(0, iterator_to_array(ApiKey::findByArtist($artist->id)));
    }

    public function testChangePasswordInvalidOldPassword(): void
    {
        $artist = $this->createArtist();
        $this->assertFalse($artist->changePassword('start1234', 'test1234'));
    }

    public function testChangePasswordEmptyNewPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        $artist = $this->createArtist();
        $artist->changePassword('test1234', '');
    }

    public function testSetPasswordValid(): void
    {
        $artist = $this->createArtist();
        $artist->setPassword('start1234');
        $this->assertTrue($artist->validatePassword('start1234'));
    }

    public function testSetPasswordEmptyPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        $artist = $this->createArtist();
        $artist->setPassword('');
    }

    public function testFindAll(): void
    {
        $this->createArtist();
        $secondArtist = $this->createArtist(execute: false);
        $secondArtist->email = 'test2@example.com';
        $secondArtist->create();
        $artists = Artist::findAll();
        $this->assertGreaterThanOrEqual(2, iterator_count($artists));
    }

    public function testFormat(): void
    {
        $artist = $this->createArtist();
        $this->assertEquals([
            'artistName' => $artist->artistName,
            'email' => $artist->email,
            'profilePicture' => $artist->profilePicture,
            'roles' => $artist->roles,
            'enabled' => $artist->enabled,
            'id' => $artist->id,
            'aboutMe' => $artist->aboutMe,
            'colorScheme' => 'auto',
        ], $artist->format());
    }

    public function testSetTwoFactorCode(): void
    {
        $artist = $this->createArtist();
        $artist->setTwoFactorCode();
        $this->assertMatchesRegularExpression('/\d{6}/', $artist->twoFactorToken);
    }

    public function testUpdate(): void
    {
        $artist = $this->createArtist();
        $artist->email = 'test@test.de';
        $artist->aboutMe = 'aboutme';
        $artist->profilePicture = '_profilepicture';
        $artist->artistName = 'artist';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('test');
        $artist->update();

        $savedArtist = Artist::findById($artist->id);
        $this->assertEquals($artist, $savedArtist);
    }

    public function testUpdateUnsaved(): void
    {
        $artist = $this->createArtist(execute: false);
        $artist->email = 'test@test.de';
        $artist->aboutMe = 'aboutme';
        $artist->profilePicture = '_profilepicture';
        $artist->artistName = 'artist';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('test');
        try {
            $artist->update();
        } catch (\Error $error) {
            self::assertTrue(true);
        }
    }

    public function testFindById(): void
    {
        $artist = $this->createArtist();
        $foundArtist = Artist::findById($artist->id);

        $this->assertEquals($artist, $foundArtist);
    }

    public function testFindByIdNotExist(): void
    {
        $foundArtist = Artist::findById(-1);

        $this->assertNull($foundArtist);
    }

    public function testDelete(): void
    {
        $this->createArtist(email: 'test2@example.com');
        $artist = $this->createArtist();
        $artist->delete();

        $foundArtist = Artist::findById($artist->id);
        $this->assertNull($foundArtist);
    }

    public function testDeleteWithApiKey(): void
    {
        $this->createArtist(email: 'test2@example.com');
        $artist = $this->createArtist();
        $apiKey = new ApiKey();
        $apiKey->userId = $artist->id;
        $apiKey->setApiKey();
        $apiKey->remoteAddress = '127.0.0.1';
        $apiKey->userAgent = 'PHPUnit';
        $apiKey->validSince = new DateTime();
        $apiKey->create();
        $artist->delete();

        $foundArtist = Artist::findById($artist->id);
        $this->assertNull($foundArtist);
    }

    public function testDeleteUnsaved(): void
    {
        try {
            $this->createArtist();
            $this->createArtist(email: 'test2@example.com');
            $artist = $this->createArtist(execute: false);
            $artist->delete();
        } catch (\Error $error) {
            self::assertTrue(true);
        }
    }

    public function testDeleteLastUser(): void
    {
        $this->expectException(DeleteLastAdminException::class);
        $artist = $this->createArtist(isAdmin: false, isWriter: true, isReader: true);
        $artist->delete();

        $foundArtist = Artist::findById($artist->id);
        $this->assertNull($foundArtist);
    }

    public function testDeleteLastAdmin(): void
    {
        $this->expectException(DeleteLastAdminException::class);
        $this->createArtist(isAdmin: false, email: 'test2@example.com');
        $artist = $this->createArtist();
        $artist->delete();
    }

    public function testDeleteLastNromalUserAdminNotDeleted(): void
    {
        $this->createArtist();
        $artist = $this->createArtist(isAdmin: false, isWriter: true, isReader: true, email: 'test2@example.com');
        $artist->delete();

        $this->assertTrue(true);
    }

    public function testCountAdmins(): void
    {
        $this->createArtist(isAdmin: false, email: 'test2@example.com');
        $this->createArtist();
        $this->assertEquals(1, Artist::countAdmins(-1));
    }

    public function testFindByEmail(): void
    {
        $this->createArtist();
        $artist = $this->createArtist(isAdmin: false, email: 'test2@example.com');
        $foundArtist = Artist::findByEmail('test2@example.com');
        $this->assertEquals($artist, $foundArtist);
    }

    public function testFindByEmailNotExist(): void
    {
        $this->createArtist();
        $this->createArtist(isAdmin: false, email: 'test2@example.com');
        $foundArtist = Artist::findByEmail('notexists@example.com');
        $this->assertNull($foundArtist);
    }
}
