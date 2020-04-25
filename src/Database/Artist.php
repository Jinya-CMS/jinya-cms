<?php

namespace App\Database;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use DateTime;
use Iterator;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;

class Artist extends LoadableEntity implements FormattableEntityInterface
{
    public ?int $id = null;
    public string $email = '';
    public bool $enabled = false;
    public ?string $twoFactorToken = '';
    public ?DateTime $lastLogin = null;
    public ?string $confirmationToken = '';
    public ?DateTime $passwordRequestedAt = null;
    public array $roles = [];
    public string $artistName = '';
    public ?string $profilePicture = '';
    public ?string $aboutMe = '';
    private string $password = '';

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

    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql->select()->from('users')->where(['email LIKE :keyword', 'artist_name LIKE :keyword'],
            PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    public static function findAll(): Iterator
    {
        return self::fetchArray('users', new self(),
            [
                'enabled' => new BooleanStrategy('1', '0'),
                'roles' => new SerializableStrategy(new PhpSerialize()),
            ]);
    }

    public function validatePassword(string $password): bool
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->verify($password, $this->password);
    }

    public function setPassword(string $password): void
    {
        $bcrypt = new Bcrypt();
        $this->password = $bcrypt->create($password);
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

    public function delete(): void
    {
        $this->internalDelete('users');
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
     * @return array
     */
    public function format(): array
    {
        return [
            'artistName' => $this->artistName,
            'email' => $this->email,
            'profilePicture' => $this->profilePicture,
            'roles' => $this->roles,
            'enabled' => $this->enabled,
            'id' => $this->id,
        ];
    }
}