<?php

namespace App\Database;

use App\Database\Converter\BooleanConverter;
use App\Database\Converter\NullableBooleanConverter;
use App\Database\Converter\PhpSerializerConverter;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Web\Middleware\AuthorizationMiddleware;
use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Extensions\Database\Attributes\Delete;
use Jinya\Router\Extensions\Database\Attributes\Find;

/**
 * This class contains all information relevant for an artist. Artists are the users of Jinya CMS
 */
#[Table('users')]
#[Find('/api/user', new AuthorizationMiddleware(ROLE_ADMIN))]
class Artist extends Entity
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var string The email address of the artist */
    #[Column]
    public string $email = '';

    /** @var bool Indicates whether the artist is allowed to log in */
    #[Column]
    #[BooleanConverter]
    public bool $enabled = false;

    /** @var string|null The current two-factor code, gets reset after successful validation */
    #[Column(sqlName: 'two_factor_token')]
    public ?string $twoFactorToken = '';

    /** @var array<string> $roles The roles of the artist */
    #[Column]
    #[PhpSerializerConverter]
    public array $roles = [];

    /** @var string The artist name */
    #[Column(sqlName: 'artist_name')]
    public string $artistName = '';

    /** @var string|null The path to the profile picture */
    #[Column(sqlName: 'profile_picture')]
    public ?string $profilePicture = '';

    /** @var string|null The about me text */
    #[Column(sqlName: 'about_me')]
    public ?string $aboutMe = '';

    /** @var int|null The count of failed login attempts, after 5 the artist can't log in anymore */
    #[Column(sqlName: 'failed_login_attempts')]
    public ?int $failedLoginAttempts = 0;

    /** @var DateTime|null Contains the time until the login is blocked, if the login is not blocked, this field is null */
    #[Column(sqlName: 'login_blocked_until')]
    public ?DateTime $loginBlockedUntil = null;

    /** @var bool|null The artist preferred color scheme, null means auto, true means light and false means dark */
    #[Column(sqlName: 'prefers_color_scheme')]
    #[NullableBooleanConverter]
    public ?bool $prefersColorScheme = null;

    /** @var string The artists' password */
    #[Column]
    public string $password = '';

    /**
     * Finds the artist with the given email
     *
     * @param string $email The email to search for
     * @return Artist|null
     */
    public static function findByEmail(string $email): ?Artist
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'email',
                'enabled',
                'two_factor_token',
                'password',
                'roles',
                'artist_name',
                'profile_picture',
                'about_me',
                'failed_login_attempts',
                'login_blocked_until'
            ])
            ->where('email = :email', ['email' => $email]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Securely sets the two-factor code
     *
     * @throws Exception
     */
    public function setTwoFactorCode(): void
    {
        $this->twoFactorToken = '';
        for ($i = 0; $i < 6; ++$i) {
            $this->twoFactorToken .= random_int(0, 9);
        }
    }

    /**
     * Formats the artist
     *
     * @return array<string, array<string>|bool|int|string|null>
     */
    #[Pure]
    #[ArrayShape([
        'artistName' => 'string',
        'email' => 'string',
        'profilePicture' => 'null|string',
        'roles' => 'array',
        'enabled' => 'bool',
        'id' => 'int',
        'aboutMe' => 'null|string',
        'colorScheme' => 'string'
    ])]
    public function format(): array
    {
        $result = [
            'artistName' => $this->artistName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'roles' => $this->roles,
            'enabled' => $this->enabled,
            'id' => $this->id,
            'aboutMe' => $this->aboutMe,
        ];

        if ($this->prefersColorScheme === true) {
            $result['colorScheme'] = 'dark';
        } elseif ($this->prefersColorScheme === false) {
            $result['colorScheme'] = 'light';
        } else {
            $result['colorScheme'] = 'auto';
        }

        return $result;
    }

    /**
     * Validates the given device code for the given artist
     *
     * @param string $knownDeviceCode The device code to validate
     * @return bool
     */
    public function validateDevice(string $knownDeviceCode): bool
    {
        $device = KnownDevice::findByCode($knownDeviceCode);

        return $device !== null && $device->userId === $this->id;
    }

    /**
     * Changes the password of the artist
     *
     * @param string $oldPassword The old password of the artist to validate the password before changing
     * @param string $password The new password
     * @return bool
     * @throws NotNullViolationException
     */
    public function changePassword(string $oldPassword, string $password): bool
    {
        if ($this->validatePassword($oldPassword)) {
            $this->setPassword($password);
            $this->update();

            $apiKeys = ApiKey::findByArtist($this->id);
            foreach ($apiKeys as $apiKey) {
                $apiKey->delete();
            }

            return true;
        }

        return false;
    }

    /**
     * Validates the given password against the hash in the database
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->password) && $this->enabled;
    }

    /**
     * Sets the artists password and hashes it
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        if ($password === '') {
            throw new InvalidArgumentException('Password cannot be empty');
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->password = $hash;
    }

    /**
     * Counts all available admins, excluding the given artist
     *
     * @param int $id The artist to exclude from check
     * @return int
     */
    public static function countAdmins(int $id): int
    {
        /** @var Iterator<Artist> $users */
        $users = self::findAll();
        $admins = 0;
        foreach ($users as $user) {
            if ($user->enabled && $id !== $user->id && in_array(
                ROLE_ADMIN,
                $user->roles,
                true
            )) {
                $admins++;
            }
        }

        return $admins;
    }

    /**
     * Registers a failed login. After the fifth login failed, the login will be blocked for 10 minutes
     * @throws NotNullViolationException
     */
    public function registerFailedLogin(): void
    {
        ++$this->failedLoginAttempts;
        if ($this->failedLoginAttempts >= 5) {
            $this->loginBlockedUntil = (new DateTime('now'))->add(new DateInterval('PT10M'));
        }
        $this->update();
    }

    /**
     * Resets the failed login attempts and the time until the login is blocked
     * @throws NotNullViolationException
     */
    public function unlockAccount(): void
    {
        $this->failedLoginAttempts = null;
        $this->loginBlockedUntil = null;
        $this->update();
    }
}
