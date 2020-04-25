<?php

namespace App\Database;

use Exception;
use Iterator;
use RuntimeException;

class KnownDevice extends Utils\LoadableEntity
{
    public int $userId;
    public string $deviceKey;
    public string $userAgent = '';
    public string $remoteAddress = '';

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('known_device', $id, new self());
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('known_device', new self());
    }

    /**
     * Gets a known device by code
     *
     * @param string $knownDeviceCode
     * @return KnownDevice
     */
    public static function findByCode(string $knownDeviceCode): KnownDevice
    {
        $sql = self::getSql();
        $select = $sql->select()->from('known_device')->where('device_key = :knownDeviceCode');
        $result = self::executeStatement($sql->prepareStatementForSqlObject($select),
            ['knownDeviceCode' => $knownDeviceCode]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result, new self());
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('known_device');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('known_device');
    }

    /**
     * @inheritDoc
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
}