<?php

namespace Jinya\Cms\Database;

use DateTime;
use InvalidArgumentException;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use PDOException;

class ArtistTest extends DatabaseAwareTestCase
{
    public function testValidatePasswordEnabledAndValid(): void
    {
        $artist = $this->createArtist();
        self::assertTrue($artist->validatePassword('test1234'));
    }

    private function createArtist(
        bool $isAdmin = true,
        bool $enabled = true,
        string $email = 'test@example.com',
        bool $execute = true
    ): Artist {
        $artist = new Artist();
        $artist->email = $email;
        $artist->aboutMe = 'About me';
        $artist->profilePicture = 'profilepicture';
        $artist->artistName = 'Test Artist';
        $artist->enabled = $enabled;
        $artist->roles = [];
        $artist->setPassword('test1234');
        if ($isAdmin) {
            $artist->roles[] = 'ROLE_ADMIN';
        }
        $artist->roles[] = 'ROLE_READER';
        $artist->roles[] = 'ROLE_WRITER';

        if ($execute) {
            $artist->create();
        }

        return $artist;
    }

    public function testValidatePasswordNotEnabledButValid(): void
    {
        $artist = $this->createArtist(isAdmin: false, enabled: false);
        self::assertFalse($artist->validatePassword('test1234'));
    }

    public function testValidatePasswordEnabledButNotValid(): void
    {
        $artist = $this->createArtist();
        self::assertFalse($artist->validatePassword('test12345'));
    }

    public function testValidatePasswordNotEnabledAndNotValid(): void
    {
        $artist = $this->createArtist(isAdmin: false, enabled: false);
        self::assertFalse($artist->validatePassword('test12345'));
    }

    public function testCreateAllValid(): void
    {
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->create();

        $savedArtist = Artist::findById($artist->id);
        self::assertEquals($artist, $savedArtist);
    }

    public function testFormatPrefersColorSchemeDark(): void
    {
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->prefersColorScheme = true;
        $artist->create();

        $formatted = $artist->format();
        self::assertEquals('dark', $formatted['colorScheme']);
    }

    public function testFormatPrefersColorSchemeLight(): void
    {
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->prefersColorScheme = false;
        $artist->create();

        $formatted = $artist->format();
        self::assertEquals('light', $formatted['colorScheme']);
    }

    public function testFormatPrefersColorSchemeAuto(): void
    {
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->prefersColorScheme = null;
        $artist->create();

        $formatted = $artist->format();
        self::assertEquals('auto', $formatted['colorScheme']);
    }

    public function testCreateDuplicate(): void
    {
        $this->expectException(PDOException::class);
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
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
        self::assertTrue($valid);
    }

    public function testValidateDeviceInvalid(): void
    {
        $artist = $this->createArtist();

        $valid = $artist->validateDevice('123456789');
        self::assertFalse($valid);
    }

    public function testUnlockAccount(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        self::assertEquals(5, $artist->failedLoginAttempts);
        self::assertNotNull($artist->loginBlockedUntil);
        $artist->unlockAccount();
        self::assertTrue(true);
    }

    public function testRegisterFailedLoginOneFailedAttempt(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        self::assertEquals(1, $artist->failedLoginAttempts);
    }

    public function testRegisterFailedLoginFiveFailedAttempts(): void
    {
        $artist = $this->createArtist();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        $artist->registerFailedLogin();
        self::assertEquals(5, $artist->failedLoginAttempts);
        self::assertNotNull($artist->loginBlockedUntil);
    }

    public function testChangePasswordValidOldPassword(): void
    {
        $artist = $this->createArtist();
        self::assertTrue($artist->changePassword('test1234', 'start1234'));
        self::assertTrue($artist->validatePassword('start1234'));
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

        self::assertTrue($artist->changePassword('test1234', 'start1234'));
        self::assertTrue($artist->validatePassword('start1234'));
        self::assertCount(0, iterator_to_array(ApiKey::findByArtist($artist->id)));
    }

    public function testChangePasswordInvalidOldPassword(): void
    {
        $artist = $this->createArtist();
        self::assertFalse($artist->changePassword('start1234', 'test1234'));
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
        self::assertTrue($artist->validatePassword('start1234'));
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
        $secondArtist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $secondArtist->email = 'test2@example.com';
        $secondArtist->create();
        $artists = Artist::findAll();
        self::assertGreaterThanOrEqual(2, iterator_count($artists));
    }

    public function testFormat(): void
    {
        $artist = $this->createArtist();
        self::assertEquals([
            'artistName' => $artist->artistName,
            'email' => $artist->email,
            'profilePicture' => $artist->profilePicture,
            'roles' => $artist->roles,
            'enabled' => $artist->enabled,
            'id' => $artist->id,
            'aboutMe' => $artist->aboutMe,
            'colorScheme' => 'auto',
            'totpMode' => $artist->totpMode->string(),
            'loginMailEnabled' => true,
            'newDeviceMailEnabled' => true,
        ], $artist->format());
    }

    public function testSetTwoFactorCode(): void
    {
        $artist = $this->createArtist();
        $artist->setTwoFactorCode();
        self::assertMatchesRegularExpression('/\d{6}/', $artist->twoFactorToken);
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
        self::assertEquals($artist, $savedArtist);
    }

    public function testUpdateUnsaved(): void
    {
        $this->expectError();
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->email = 'test@test.de';
        $artist->aboutMe = 'aboutme';
        $artist->profilePicture = '_profilepicture';
        $artist->artistName = 'artist';
        $artist->enabled = true;
        $artist->roles = [];
        $artist->setPassword('test');
        $artist->update();
    }

    public function testFindById(): void
    {
        $artist = $this->createArtist();
        $foundArtist = Artist::findById($artist->id);

        self::assertEquals($artist, $foundArtist);
    }

    public function testFindByIdNotExist(): void
    {
        $foundArtist = Artist::findById(-1);

        self::assertNull($foundArtist);
    }

    public function testDelete(): void
    {
        $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $artist = $this->createArtist();
        $artist->delete();

        $foundArtist = Artist::findById($artist->id);
        self::assertNull($foundArtist);
    }

    public function testDeleteWithApiKey(): void
    {
        $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
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
        self::assertNull($foundArtist);
    }

    public function testDeleteUnsaved(): void
    {
        $this->expectError();
        $this->createArtist();
        $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $artist = $this->createArtist(
            isAdmin: false,
            execute: false
        );
        $artist->delete();
    }

    public function testDeleteLastNormalUserAdminNotDeleted(): void
    {
        $this->createArtist();
        $artist = $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $artist->delete();

        self::assertTrue(true);
    }

    public function testCountAdmins(): void
    {
        $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $this->createArtist();
        self::assertEquals(1, Artist::countAdmins());
    }

    public function testFindByEmail(): void
    {
        $this->createArtist();
        $artist = $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $foundArtist = Artist::findByEmail('test2@example.com');
        self::assertEquals($artist, $foundArtist);
    }

    public function testFindByEmailNotExist(): void
    {
        $this->createArtist();
        $this->createArtist(
            isAdmin: false,
            email: 'test2@example.com'
        );
        $foundArtist = Artist::findByEmail('notexists@example.com');
        self::assertNull($foundArtist);
    }

    public function testTotpActivateFlow(): void
    {
        $artist = $this->createArtist();
        $artist->twoFactorToken = '123456';
        $artist->update();

        $emailVerify = $artist->verifyTotpCode('123456');
        self::assertTrue($emailVerify);

        $otp = $artist->setTotpSecret();

        $artist = Artist::findById($artist->id);
        self::assertEquals($otp->getSecret(), $artist->totpSecret);

        $activate = $artist->activateOtphp($otp->now());
        self::assertTrue($activate);

        $artist = Artist::findById($artist->id);
        self::assertEquals(TotpMode::Otphp, $artist->totpMode);

        $otphpVerify = $artist->verifyTotpCode($otp->now());
        self::assertTrue($otphpVerify);
    }

    public function testTotpActivateFlowInvalidVerifyCode(): void
    {
        $artist = $this->createArtist();
        $otp = $artist->setTotpSecret();

        $artist = Artist::findById($artist->id);
        self::assertEquals($otp->getSecret(), $artist->totpSecret);

        $activate = $artist->activateOtphp('asdasd');
        self::assertFalse($activate);
    }
}
