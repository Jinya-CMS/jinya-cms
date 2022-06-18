<?php

namespace App\Database;

use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 * This class contains a known device, known devices are used to remove the need for a two-factor code. It is up to the API consumer whether they want to use them or not.
 */
class KnownDevice extends Utils\LoadableEntity
{
    /** @var int The ID of the artist this known device belongs to */
    public int $userId;
    /** @var string The key of the known device */
    public string $deviceKey;
    /** @var string The user agent of the browser or API client this known device was issued for */
    public string $userAgent = '';
    /** @var string The remote address of the API consumer this known device was issued for */
    public string $remoteAddress = '';

    /**
     * Not implemented
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Not implemented
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets all known devices for the given artist
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByArtist(int $artistId): Iterator
    {
        $sql = 'SELECT id, user_id, device_key, user_agent, remote_address FROM known_device WHERE user_id = :artistId';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['artistId' => $artistId]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Not implemented
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets a known device by code
     *
     * @param string $knownDeviceCode
     * @return KnownDevice|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findByCode(string $knownDeviceCode): ?KnownDevice
    {
        $sql = 'SELECT id, user_id, device_key, user_agent, remote_address FROM known_device WHERE device_key = :knownDeviceCode';
        try {
            return self::getPdo()->fetchObject($sql, new self(), ['knownDeviceCode' => $knownDeviceCode]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Creates the current known device
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('known_device');
    }

    /**
     * Deletes the current known device
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('known_device');
    }

    /**
     * Updates the current known device
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('known_device');
    }

    /**
     * Securely sets the device code
     * @throws Exception
     */
    public function setDeviceKey(): void
    {
        $this->deviceKey = bin2hex(random_bytes(20));
    }

    /**
     * Formats the known device into an array
     *
     * @return array<string, string>
     */
    #[ArrayShape([
        'remoteAddress' => 'string',
        'userAgent' => 'string',
        'key' => 'string',
    ])]
    public function format(): array
    {
        return [
            'remoteAddress' => $this->remoteAddress,
            'userAgent' => $this->userAgent,
            'key' => $this->deviceKey,
        ];
    }
}
