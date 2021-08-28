<?php

namespace App\Database;

use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Strategies\PhpSerializeStrategy;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use App\Web\Middleware\RoleMiddleware;
use DateInterval;
use DateTime;
use Exception;
use InvalidArgumentException;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

#[OpenApiModel("Artists are the users of Jinya")]
class Artist extends LoadableEntity implements FormattableEntityInterface
{
    #[OpenApiField(required: true, format: OpenApiField::FORMAT_EMAIL)]
    public string $email = '';
    #[OpenApiField(required: true, defaultValue: false)]
    public bool $enabled = false;
    #[OpenApiHiddenField]
    public ?string $twoFactorToken = '';
    #[OpenApiField(required: true, enumValues: ["ROLE_ADMIN", "ROLE_READER", "ROLE_WRITER"], array: true, arrayType: 'string')]
    public array $roles = [];
    #[OpenApiField(required: true)]
    public string $artistName = '';
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $profilePicture = '';
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $aboutMe = '';
    #[OpenApiHiddenField]
    public ?int $failedLoginAttempts = 0;
    #[OpenApiHiddenField]
    public ?DateTime $loginBlockedUntil = null;
    #[OpenApiHiddenField]
    private string $password = '';

    /**
     * Finds the artist with the given email
     * @param string $email
     * @return Artist|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByEmail(string $email): ?Artist
    {
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me, failed_login_attempts, login_blocked_until FROM users WHERE email = :email';
        $result = self::executeStatement($sql, ['email' => $email]);

        if (count($result) === 0) {
            return null;
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult(
            $result[0],
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
                'loginBlockedUntil' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @param int $id
     * @return Artist|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById(
            'users',
            $id,
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me FROM users WHERE email LIKE :emailKeyword OR artist_name LIKE :nameKeyword';
        $result = self::executeStatement($sql, ['emailKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"]);

        return self::hydrateMultipleResults(
            $result,
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
            ]
        );
    }

    /**
     * Securely sets the two factor code
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
     * Creates the artist
     *
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
            ]
        );
    }

    /**
     * @inheritDoc
     * @throws DeleteLastAdminException
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
     * Counts all available admins
     * @param int $id
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
            if ($id !== $user->getIdAsInt() && $user->enabled && in_array(RoleMiddleware::ROLE_ADMIN, $user->roles, true)) {
                $admins++;
            }
        }

        return $admins;
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray(
            'users',
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
            ]
        );
    }

    /**
     * Formats the artist
     *
     * @return array
     */
    #[Pure] #[ArrayShape([
        'artistName' => "string",
        'email' => "string",
        'profilePicture' => "null|string",
        'roles' => "array",
        'enabled' => "bool",
        'id' => "int",
        'aboutMe' => "null|string"
    ])]
    public function format(): array
    {
        return [
            'artistName' => $this->artistName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'roles' => $this->roles,
            'enabled' => $this->enabled,
            'id' => $this->getIdAsInt(),
            'aboutMe' => $this->aboutMe,
        ];
    }

    /**
     * Validates the given device code for the given user
     *
     * @param string $knownDeviceCode
     * @return bool
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function validateDevice(string $knownDeviceCode): bool
    {
        $device = KnownDevice::findByCode($knownDeviceCode);
        return $device !== null && $device->userId === $this->getIdAsInt();
    }

    /**
     * Changes the password of the user
     *
     * @param string $oldPassword
     * @param string $password
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
            ]
        );
    }

    /**
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