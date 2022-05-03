<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use App\Web\Attributes\Authenticated;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

#[JinyaApi(createRole: Authenticated::WRITER, readRole: Authenticated::READER, updateRole: Authenticated::WRITER, deleteRole: Authenticated::WRITER)]
class File extends LoadableEntity implements FormattableEntityInterface
{
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    #[JinyaApiField(ignore: true)]
    public int $updatedById;
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;
    #[JinyaApiField(ignore: true)]
    public string $path = '';
    #[JinyaApiField(required: true)]
    public string $name = '';
    #[JinyaApiField(ignore: true)]
    public string $type = '';

    /**
     * @inheritDoc
     * @param int $id
     * @return object|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById(
            'file',
            $id,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, path, name, type FROM file WHERE name LIKE :keyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['keyword' => "%$keyword%"], [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'file',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the uploading chunks
     *
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getUploadChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->id);
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->id = $this->internalCreate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('file');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;
        $this->internalUpdate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * @return array
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => "int",
        'name' => "string",
        'type' => "string",
        'path' => "string",
        'created' => "array",
        'updated' => "array"
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'created' => [
                'by' => [
                    'artistName' => $creator->artistName,
                    'email' => $creator->email,
                    'profilePicture' => $creator->profilePicture,
                ],
                'at' => $this->createdAt->format(DATE_ATOM),
            ],
            'updated' => [
                'by' => [
                    'artistName' => $updatedBy->artistName,
                    'email' => $updatedBy->email,
                    'profilePicture' => $updatedBy->profilePicture,
                ],
                'at' => $this->lastUpdatedAt->format(DATE_ATOM),
            ],
        ];
    }

    /**
     * Gets the creator of this file
     *
     * @return Artist|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getCreator(): ?Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getUpdatedBy(): ?Artist
    {
        return Artist::findById($this->updatedById);
    }
}