<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

#[JinyaApi]
class Gallery extends Utils\LoadableEntity
{

    public const TYPE_SEQUENCE = 'sequence';
    public const TYPE_MASONRY = 'masonry';

    public const ORIENTATION_HORIZONTAL = 'horizontal';
    public const ORIENTATION_VERTICAL = 'vertical';

    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    #[JinyaApiField(ignore: true)]
    public int $updatedById;

    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;

    #[JinyaApiField(required: true)]
    public string $name;
    #[JinyaApiField]
    public string $description = '';
    #[JinyaApiField]
    public string $type = self::TYPE_SEQUENCE;
    #[JinyaApiField]
    public string $orientation = self::ORIENTATION_HORIZONTAL;

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
            'gallery',
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
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, name, description, type, orientation FROM gallery WHERE name LIKE :nameKeyword OR description LIKE :descKeyword';

        try {
            return self::getPdo()->fetchIterator($sql,
                new self(),
                ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"],
                [
                    'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'gallery',
            new self(),
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
        'description' => "string",
        'type' => "string",
        'orientation' => "string",
        'created' => "array",
        'updated' => "array"
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'orientation' => $this->orientation,
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
            'gallery',
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
        $this->internalDelete('gallery');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;
        $this->internalUpdate(
            'gallery',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Get all files in gallery
     *
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getFiles(): Iterator
    {
        $sql = 'SELECT id, gallery_id, file_id, position FROM gallery_file_position WHERE gallery_id = :id ORDER BY position';

        try {
            return self::getPdo()->fetchIterator($sql, new GalleryFilePosition(), ['id' => $this->id]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}