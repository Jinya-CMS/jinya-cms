<?php

namespace App\Database;

use App\Authentication\AuthenticationChecker;
use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Strategies\NullableBooleanStrategy;
use App\Database\Strategies\PhpSerializeStrategy;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 * This class contains all information relevant for an artist. Artists are the users of Jinya CMS
 */
#[JinyaApi(createRole: AuthenticationChecker::ROLE_ADMIN, readRole: AuthenticationChecker::ROLE_READER, updateRole: AuthenticationChecker::ROLE_ADMIN, deleteRole: AuthenticationChecker::ROLE_ADMIN)]
class Artist extends LoadableEntity
{
    /** @var string The email address of the artist */
    #[JinyaApiField(required: true)]
    public string $email = '';
    /** @var bool Indicates whether the artist is allowed to log in */
    public bool $enabled = false;
    /** @var string|null The current two-factor code, gets reset after successful validation */
    #[JinyaApiField(ignore: true)]
    public ?string $twoFactorToken = '';
    /** @var array<string> $roles The roles of the artist */
    #[JinyaApiField(required: true)]
    public array $roles = [];
    /** @var string The artist name */
    #[JinyaApiField(required: true)]
    public string $artistName = '';
    /** @var string|null The path to the profile picture */
    #[JinyaApiField(ignore: true)]
    public ?string $profilePicture = '';
    /** @var string|null The about me text */
    #[JinyaApiField(ignore: true)]
    public ?string $aboutMe = '';
    /** @var int|null The count of failed login attempts, after 5 the artist can't log in anymore */
    #[JinyaApiField(ignore: true)]
    public ?int $failedLoginAttempts = 0;
    /** @var DateTime|null Contains the time until the login is blocked, if the login is not blocked this field is null */
    #[JinyaApiField(ignore: true)]
    public ?DateTime $loginBlockedUntil = null;
    /** @var bool|null The artist preferred color scheme, null means auto, true means light and false means dark */
    #[JinyaApiField(ignore: true)]
    public ?bool $prefersColorScheme = null;
    /** @var string The artists password */
    #[JinyaApiField(ignore: true)]
    public string $password = '';

    /**
     * Finds the artist with the given email
     *
     * @param string $email The email to search for
     * @return Artist|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByEmail(string $email): ?Artist
    {
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me, failed_login_attempts, login_blocked_until FROM users WHERE email = :email';

        try {
            return self::getPdo()->fetchObject($sql, new self(), ['email' => $email],
                [
                    'enabled' => new BooleanStrategy(1, 0),
                    'roles' => new PhpSerializeStrategy(),
                    'loginBlockedUntil' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'prefersColorScheme' => new NullableBooleanStrategy(),
                ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds the artist that belongs to the given ID
     *
     * @param int $id The ID to search for
     * @return Artist|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public static function findById(int $id): ?Artist
    {
        return self::fetchSingleById(
            'users',
            $id,
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
                'prefersColorScheme' => new NullableBooleanStrategy(),
            ]
        );
    }

    /**
     * Finds the artists by the given keyword
     *
     * @param string $keyword
     * @return Iterator<Artist>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me, prefers_color_scheme FROM users WHERE email LIKE :emailKeyword OR artist_name LIKE :nameKeyword';
        try {
            return self::getPdo()->fetchIterator($sql,
                new self(),
                ['emailKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"],
                [
                    'enabled' => new BooleanStrategy(1, 0),
                    'roles' => new PhpSerializeStrategy(),
                    'prefersColorScheme' => new NullableBooleanStrategy(),
                ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
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
     * Creates the current artist
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function create(): void
    {
        $this->id = $this->internalCreate(
            'users',
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
                'prefersColorScheme' => new NullableBooleanStrategy(),
            ]
        );
    }

    /**
     * Deletes the current artist
     *
     * @return void
     * @throws DeleteLastAdminException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function delete(): void
    {
        if (self::countAdmins($this->getIdAsInt()) >= 1) {
            $this->internalDelete('users');
        } else {
            throw new DeleteLastAdminException('Cannot delete last admin');
        }
    }

    /**
     * Counts all available admins, excluding the given artist
     *
     * @param int $id The artist to exclude from check
     * @return int
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function countAdmins(int $id): int
    {
        $users = self::findAll();
        $admins = 0;
        foreach ($users as $user) {
            if ($user->enabled && $id !== $user->getIdAsInt() && in_array(AuthenticationChecker::ROLE_ADMIN, $user->roles, true)) {
                $admins++;
            }
        }

        return $admins;
    }

    /**
     * Finds all artists
     *
     * @return Iterator<Artist>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'users',
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
                'prefersColorScheme' => new NullableBooleanStrategy(),
            ]
        );
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
            'id' => $this->getIdAsInt(),
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
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public function validateDevice(string $knownDeviceCode): bool
    {
        $device = KnownDevice::findByCode($knownDeviceCode);
        return $device !== null && $device->userId === $this->getIdAsInt();
    }

    /**
     * Changes the password of the artist
     *
     * @param string $oldPassword The old password of the artist to validate the password before changing
     * @param string $password The new password
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function changePassword(string $oldPassword, string $password): bool
    {
        if ($this->validatePassword($oldPassword)) {
            $this->setPassword($password);
            $this->update();

            $apiKeys = ApiKey::findByArtist($this->getIdAsInt());
            foreach ($apiKeys as $apiKey) {
                /** @var ApiKey $apiKey */
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
     * Updates the artist
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate(
            'users',
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
                'loginBlockedUntil' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'prefersColorScheme' => new NullableBooleanStrategy(),
            ]
        );
    }

    /**
     * Registers a failed login, after the fifth login failed the login will be blocked for 10 minutes
     *
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
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
     *
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    public function unlockAccount(): void
    {
        $this->failedLoginAttempts = null;
        $this->loginBlockedUntil = null;
        $this->update();
    }
}