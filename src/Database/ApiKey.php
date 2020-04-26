<?php

namespace App\Database;

use DateTime;
use Exception;
use Iterator;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use RuntimeException;

class ApiKey extends Utils\LoadableEntity
{
    public string $apiKey;
    public int $userId;
    public DateTime $validSince;
    public string $userAgent;
    public string $remoteAddress;

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Gets the api key object that belongs to the key
     *
     * @param string $apiKey
     * @return ApiKey
     */
    public static function findByApiKey(string $apiKey): ApiKey
    {
        $sql = self::getSql();
        $select = $sql->select()->from('api_key')->where('api_key = :apiKey');
        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['apiKey' => $apiKey]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result, new self(),
            ['validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT)]);
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
        return self::fetchArray('api_key', new self(), ['validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT)]);
    }

    /**
     * Sets the api key securely
     *
     * @throws Exception
     */
    public function setApiKey(): void
    {
        $this->apiKey = "jinya-api-token-$this->userId-" . bin2hex(random_bytes(20));
    }

    public function getArtist(): Artist
    {
        return Artist::findById($this->userId);
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('api_key', ['validSince' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT)], ['id']);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $sql = self::getSql();
        $delete = $sql->delete()->from('api_key')->where(['api_key = :apiKey']);
        self::executeStatement($sql->prepareStatementForSqlObject($delete), ['apiKey' => $this->apiKey]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): void
    {
        $sql = self::getSql();

        $converter = new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT);

        $update = $sql->update('api_key')
            ->where(['api_key = :apiKey'])
            ->set(['valid_since' => $converter->extract($this->validSince)]);

        try {
            self::executeStatement($sql->prepareStatementForSqlObject($update), ['apiKey' => $this->apiKey]);
        } catch (InvalidQueryException $exception) {
            throw $this->convertInvalidQueryExceptionToException($exception);
        }
    }
}