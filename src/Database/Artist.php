<?php

namespace App\Database;

use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Strategies\PhpSerializeStrategy;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\Web\Middleware\RoleMiddleware;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use LogicException;

class Artist extends LoadableEntity implements FormattableEntityInterface
{
    public string $email = '';
    public bool $enabled = false;
    public ?string $twoFactorToken = '';
    public array $roles = [];
    public string $artistName = '';
    public ?string $profilePicture = '';
    public ?string $aboutMe = '';
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
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me FROM users WHERE email = :email';
        $result = self::executeStatement($sql, ['email' => $email]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult(
            $result[0],
            new self(),
            [
                'enabled' => new BooleanStrategy(1, 0),
                'roles' => new PhpSerializeStrategy(),
            ]
        );
    }

    /**
     * @param int $id
     * @return object|null
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
     * Finds a user for the given api key
     *
     * @param string $apiKey
     * @return Artist|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByApiKey(string $apiKey): ?Artist
    {
        $sql = 'SELECT id, email, enabled, two_factor_token, password, roles, artist_name, profile_picture, about_me FROM users u JOIN api_key ak on u.id = ak.user_id WHERE ak.api_key = :apiKey';
        $result = self::executeStatement($sql, ['apiKey' => $apiKey]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult(
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
        if (self::countAdmins() >= 1) {
            $this->internalDelete('users');
        } else {
            throw new DeleteLastAdminException('Cannot delete last admin');
        }
    }

    /**
     * Counts all available admins
     * @return int
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function countAdmins(): int
    {
        $users = self::findAll();
        $admins = 0;
        foreach ($users as $user) {
            if ($user->enabled && in_array(RoleMiddleware::ROLE_ADMIN, $user->roles, true)) {
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
     * @param bool $aboutMe
     * @return array
     */
    #[ArrayShape([
        'artistName' => "string",
        'email' => "string",
        'profilePicture' => "null|string",
        'roles' => "array",
        'enabled' => "bool",
        'id' => "int",
        'aboutMe' => "null|string"
    ])]
    public function format(
        bool $aboutMe = false
    ): array {
        $data = [
            'artistName' => $this->artistName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'roles' => $this->roles,
            'enabled' => $this->enabled,
            'id' => $this->getIdAsInt(),
        ];

        if ($aboutMe) {
            $data['aboutMe'] = $this->aboutMe;
        }

        return $data;
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
        /** @noinspection NullPointerExceptionInspection */
        return $device->userId === $this->getIdAsInt();
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
        $hash = password_hash($password, PASSWORD_BCRYPT);
        if (is_string($hash)) {
            $this->password = $hash;
        } else {
            throw new LogicException('The result is must be a string');
        }
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
            ]
        );
    }
}