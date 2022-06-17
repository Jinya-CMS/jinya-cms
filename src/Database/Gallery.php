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

/**
 * This class contains a gallery, galleries are used to arrange files in a list or masonry layout and horizontal or vertical orientation. They can be embedded into segment pages and blog posts
 */
#[JinyaApi]
class Gallery extends Utils\LoadableEntity
{

    /** @var string Used to mark a gallery for list or sequential layout */
    public const TYPE_SEQUENCE = 'sequence';
    /** @var string Used to mark a gallery for masonry layout */
    public const TYPE_MASONRY = 'masonry';

    /** @var string Used to mark a gallery for horizontal orientation */
    public const ORIENTATION_HORIZONTAL = 'horizontal';
    /** @var string Used to mark a gallery for verticla orientation */
    public const ORIENTATION_VERTICAL = 'vertical';

    /** @var int The ID of the creator of the gallery */
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    /** @var int The ID of the artist that last touched the gallery */
    #[JinyaApiField(ignore: true)]
    public int $updatedById;

    /** @var DateTime The time the gallery was created */
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    /** @var DateTime The time the gallery was last updated */
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;

    /** @var string The name of the gallery */
    #[JinyaApiField(required: true)]
    public string $name;
    /** @var string The description of the gallery. Currently ignored by the default theme. May contain HTML */
    #[JinyaApiField]
    public string $description = '';
    /** @var string The type or layout of the gallery. The selected layout should be respected in themes */
    #[JinyaApiField]
    public string $type = self::TYPE_SEQUENCE;
    /** @var string The orientation of the gallery. The selected orientation should be respected in themes */
    #[JinyaApiField]
    public string $orientation = self::ORIENTATION_HORIZONTAL;

    /**
     * @inheritDoc
     * @param int $id
     * @return Gallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?Gallery
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
     * @return Iterator<Gallery>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, name, description, type, orientation FROM gallery WHERE name LIKE :nameKeyword OR description LIKE :descKeyword';

        try {
            return self::getPdo()->fetchIterator(
                $sql,
                new self(),
                ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"],
                [
                    'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                ]
            );
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @return Iterator<Gallery>
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
     * Formats the gallery into an array
     *
     * @return array<string, array<string, array<string, string|null>|string>|int|string>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'type' => 'string',
        'orientation' => 'string',
        'created' => 'array',
        'updated' => 'array'
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
                    'artistName' => $creator?->artistName,
                    'email' => $creator?->email,
                    'profilePicture' => $creator?->profilePicture,
                ],
                'at' => $this->createdAt->format(DATE_ATOM),
            ],
            'updated' => [
                'by' => [
                    'artistName' => $updatedBy?->artistName,
                    'email' => $updatedBy?->email,
                    'profilePicture' => $updatedBy?->profilePicture,
                ],
                'at' => $this->lastUpdatedAt->format(DATE_ATOM),
            ],
        ];
    }

    /**
     * Gets the creator of this gallery
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
     * Gets the artist that last updated this gallery
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
     * @return Iterator<File>
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