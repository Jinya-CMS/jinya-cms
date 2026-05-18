<?php

namespace Jinya\Cms\Database;

use DateTime;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Cms\Logging\Logger;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\EntityTrait;
use Jinya\Database\Updatable;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * This class contains an api key, used to log in to Jinya CMS api
 */
#[Table('api_key')]
class ApiKey implements Creatable, Deletable, Updatable
{
    use EntityTrait;

    #[Column(sqlName: 'api_key', unique: true)]
    public string $apiKey;
    #[Column(sqlName: 'user_id')]
    public int $userId;
    #[Column(sqlName: 'valid_since')]
    public DateTime $validSince;
    #[Column(sqlName: 'user_agent')]
    public string $userAgent;
    #[Column(sqlName: 'remote_address')]
    public string $remoteAddress;

    public string $plainApiKey;

    private readonly LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * Gets the api key object that belongs to the key
     *
     * @param string $apiKey The api key to search for
     * @return ApiKey|null
     */
    public static function findByApiKey(string $apiKey): ?ApiKey
    {
        $logger = Logger::getLogger();
        $logger->debug('Find api key by key');

        $query = self::getQueryBuilder()
            ->newSelect()
            ->cols([
                'api_key',
                'user_id',
                'valid_since',
                'user_agent',
                'remote_address',
            ])
            ->from(self::getTableName())
            ->where(
                'api_key = :apiKey',
                ['apiKey' => hash('sha512', $apiKey)]
            );

        /** @var array<array<array-key, mixed>> $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Gets the api key object that belongs to the hashed key
     *
     * @param string $apiKey The api key to search for
     * @return ApiKey|null
     */
    public static function findByHashedApiKey(string $apiKey): ?ApiKey
    {
        $logger = Logger::getLogger();
        $logger->debug('Find api key by hashed key');

        $query = self::getQueryBuilder()
            ->newSelect()
            ->cols([
                'api_key',
                'user_id',
                'valid_since',
                'user_agent',
                'remote_address',
            ])
            ->from(self::getTableName())
            ->where(
                'api_key = :apiKey',
                ['apiKey' => $apiKey]
            );

        /** @var array<array<array-key, mixed>> $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Gets all api keys for the given artist
     *
     * @param int $artistId The ID of the artist
     * @return Iterator<ApiKey>
     */
    public static function findByArtist(int $artistId): Iterator
    {
        $logger = Logger::getLogger();
        $logger->debug('Find api keys by artist', ['artistId' => $artistId]);

        $query = self::getQueryBuilder()
            ->newSelect()
            ->cols([
                'api_key',
                'user_id',
                'valid_since',
                'user_agent',
                'remote_address',
            ])
            ->from(self::getTableName())
            ->where(
                'user_id = :artistId',
                ['artistId' => $artistId]
            );

        /** @var array<array<array-key, mixed>> $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Sets the api key securely
     *
     * @throws Exception
     */
    public function setApiKey(): void
    {
        $this->plainApiKey = "jinya-api-token-$this->userId-" . bin2hex(random_bytes(20));
        $this->apiKey = hash('sha512', $this->plainApiKey);
    }

    /**
     * Gets the artist belonging to the api key
     *
     * @return Artist|null
     */
    public function getArtist(): ?Artist
    {
        return Artist::findById($this->userId);
    }

    public function create(): void
    {
        $this->logger->debug('Create api key');
        try {
            $query = self::getQueryBuilder()
                ->newInsert()
                ->into(self::getTableName())
                ->addRow([
                    'api_key' => $this->apiKey,
                    'user_id' => $this->userId,
                    'valid_since' => $this->validSince->format(MYSQL_DATE_FORMAT),
                    'user_agent' => $this->userAgent,
                    'remote_address' => $this->remoteAddress,
                ]);

            self::executeQuery($query);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to create api key', ['exception' => $exception]);
            throw $exception;
        }
    }

    /**
     * Formats the api key into an array
     *
     * @return array<string, string>
     */
    #[ArrayShape([
        'remoteAddress' => 'string',
        'validSince' => 'string',
        'userAgent' => 'string',
        'key' => 'string'
    ])]
    public function format(): array
    {
        return [
            'remoteAddress' => $this->remoteAddress,
            'validSince' => $this->validSince->format(DATE_ATOM),
            'userAgent' => $this->userAgent,
            'key' => $this->apiKey,
        ];
    }

    public function delete(): void
    {
        $this->logger->debug('Delete api key');
        try {
            $query = self::getQueryBuilder()
                ->newDelete()
                ->from(self::getTableName())
                ->where(
                    'api_key = :apiKey',
                    ['apiKey' => $this->apiKey]
                );

            self::executeQuery($query);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to delete api key', ['exception' => $exception]);
            throw $exception;
        }
    }

    public function update(): void
    {
        $this->logger->debug('Update api key');
        try {
            $query = self::getQueryBuilder()
                ->newUpdate()
                ->table(self::getTableName())
                ->cols([
                    'valid_since',
                ])
                ->bindValues([
                    'valid_since' => $this->validSince->format(MYSQL_DATE_FORMAT),
                ])
                ->where('api_key = :apiKey', ['apiKey' => $this->apiKey]);

            self::executeQuery($query);
        } catch (Throwable $exception) {
            $this->logger->error('Failed to update api key', ['exception' => $exception]);
            throw $exception;
        }
    }
}
