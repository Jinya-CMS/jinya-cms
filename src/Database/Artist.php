<?php

namespace App\Database;

use App\Database\Exceptions\DeleteLastAdminException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\Web\Middleware\RoleMiddleware;
use Exception;
use Iterator;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;

class Artist extends LoadableEntity implements FormattableEntityInterface
{
    public string $email = '';
    public bool $enabled = false;
    public ?string $twoFactorToken = '';
    public ?string $confirmationToken = '';
    public array $roles = [];
    public string $artistName = '';
    public ?string $profilePicture = '';
    public ?string $aboutMe = '';
    private string $password = '';

    /**
     * Finds the artist with the given email
     * @param string $email
     * @return Artist
     */
    public static function findByEmail(string $email): ?Artist
    {
        $sql = self::getSql();
        $select = $sql->select()->from('users')->where('email = :email');
        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['email' => $email]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result, new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * @param int $id
     * @return Artist
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('users', $id, new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('users')
            ->where(['email LIKE :keyword', 'artist_name LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * Finds a user for the given api key
     *
     * @param string $apiKey
     * @return Artist
     */
    public static function findByApiKey(string $apiKey): ?Artist
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from(['u' => 'users'])
            ->join(['ak' => 'api_key'], 'ak.user_id = u.id')
            ->where('ak.api_key = :apiKey');
        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['apiKey' => $apiKey]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result, new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
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
     * @throws UniqueFailedException
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('users',
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * @inheritDoc
     * @throws DeleteLastAdminException
     */
    public function delete(): void
    {
        if (self::countAdmins() > 1) {
            $this->internalDelete('users');
        } else {
            throw new DeleteLastAdminException('Cannot delete last admin');
        }
    }

    /**
     * Counts all available admins
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
        return self::fetchArray('users', new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * Updates the artist
     *
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('users',
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    /**
     * Formats the artist
     *
     * @param bool $aboutMe
     * @return array
     */
    public function format(bool $aboutMe = false): array
    {
        $data = [
            'artistName' => $this->artistName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'roles' => $this->roles,
            'enabled' => $this->enabled,
            'id' => $this->id,
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
     */
    public function validateDevice(string $knownDeviceCode): bool
    {
        $device = KnownDevice::findByCode($knownDeviceCode);
        /** @noinspection NullPointerExceptionInspection */
        return $device->userId === $this->id;
    }

    /**
     * Changes the password of the user
     *
     * @param string $token
     * @param string $password
     * @return bool
     * @throws UniqueFailedException
     */
    public function changePassword(string $token, string $password): bool
    {
        if ($this->confirmationToken === $token) {
            $this->setPassword($password);
            $this->confirmationToken = null;
            $this->update();
            return true;
        }

        return false;
    }

    /**
     * Sets the artists password and hashes it
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $bcrypt = new Bcrypt();
        $this->password = $bcrypt->create($password);
    }

    /**
     * Generates the password change token
     *
     * @param string $oldPassword
     * @return string
     * @throws Exception
     */
    public function generatePasswordChangeToken(string $oldPassword): ?string
    {
        if ($this->validatePassword($oldPassword)) {
            $this->confirmationToken = bin2hex(random_bytes(24));
            $this->update();
            return $this->confirmationToken;
        }

        return null;
    }

    /**
     * Validates the given password against the hash in the database
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->verify($password, $this->password) && $this->enabled;
    }
}