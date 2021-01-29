<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

#[OpenApiModel('In galleries users can arrange files for display')]
class Gallery extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{

    public const TYPE_SEQUENCE = 'sequence';
    public const TYPE_MASONRY = 'masonry';

    public const ORIENTATION_HORIZONTAL = 'horizontal';
    public const ORIENTATION_VERTICAL = 'vertical';

    #[OpenApiField(required: true, array: true, structure: OpenApiField::CHANGED_BY_STRUCTURE, name: 'created')]
    public int $creatorId;
    #[OpenApiField(required: true, array: true, structure: OpenApiField::CHANGED_BY_STRUCTURE, name: 'updated')]
    public int $updatedById;
    #[OpenApiHiddenField]
    public DateTime $createdAt;
    #[OpenApiHiddenField]
    public DateTime $lastUpdatedAt;
    #[OpenApiField(required: true)]
    public string $name;
    #[OpenApiField(required: false, defaultValue: '')]
    public string $description = '';
    #[OpenApiField(required: true, enumValues: [self::TYPE_MASONRY, self::TYPE_SEQUENCE])]
    public string $type = self::TYPE_SEQUENCE;
    #[OpenApiField(required: true, enumValues: [self::ORIENTATION_HORIZONTAL, self::ORIENTATION_VERTICAL])]
    public string $orientation = self::ORIENTATION_HORIZONTAL;

    /**
     * @inheritDoc
     * @return Gallery
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

        $result = self::executeStatement($sql, ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"]);

        return self::hydrateMultipleResults(
            $result,
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
    public static function findAll(): Iterator
    {
        return self::fetchArray(
            'gallery',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

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
     * @return Artist
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getCreator(): Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getUpdatedBy(): Artist
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getFiles(): Iterator
    {
        $sql = 'SELECT id, gallery_id, file_id, position FROM gallery_file_position WHERE gallery_id = :id ORDER BY position';

        $result = self::executeStatement($sql, ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new GalleryFilePosition());
    }
}