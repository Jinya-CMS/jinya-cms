<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

#[OpenApiModel('A known devices replaces the two factor code for a browser or device', hasId: false)]
class KnownDevice extends Utils\LoadableEntity implements FormattableEntityInterface
{
    #[OpenApiHiddenField]
    public int $userId;
    #[OpenApiField(required: true)]
    public string $deviceKey;
    #[OpenApiField(required: true)]
    public string $userAgent = '';
    #[OpenApiField(required: true)]
    public string $remoteAddress = '';

    /**
     * {@inheritDoc}
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById('known_device', $id, new self());
    }

    /**
     * {@inheritDoc}
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets all known devices for the given artist
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByArtist(int $artistId): Iterator
    {
        $sql = 'SELECT id, user_id, device_key, user_agent, remote_address FROM known_device WHERE user_id = :artistId';

        $result = self::executeStatement($sql, ['artistId' => $artistId]);

        return self::hydrateMultipleResults($result, new self());
    }

    /**
     * {@inheritDoc}
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets a known device by code
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @noinspection PhpIncompatibleReturnTypeInspection
     */
    public static function findByCode(string $knownDeviceCode): ?KnownDevice
    {
        $sql = 'SELECT id, user_id, device_key, user_agent, remote_address FROM known_device WHERE device_key = :knownDeviceCode';
        $result = self::executeStatement(
            $sql,
            ['knownDeviceCode' => $knownDeviceCode]
        );
        if (0 === count($result)) {
            return null;
        }

        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * {@inheritDoc}
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('known_device');
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('known_device');
    }

    /**
     * {@inheritDoc}
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
     * {@inheritdoc}
     */
    #[ArrayShape(['remoteAddress' => "string", 'userAgent' => "string", 'key' => "string"])]
    public function format(): array
    {
        return [
            'remoteAddress' => $this->remoteAddress,
            'userAgent' => $this->userAgent,
            'key' => $this->deviceKey,
        ];
    }
}
