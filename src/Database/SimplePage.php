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
 * This class contains a simple page. Simple pages only contain HTML content and a title
 */
#[JinyaApi]
class SimplePage extends Utils\LoadableEntity
{
    /** @var int The ID of the creator */
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    /** @var int The ID of the artist that last updated the simple page */
    #[JinyaApiField(ignore: true)]
    public int $updatedById;

    /** @var DateTime The time the simple page was created */
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    /** @var DateTime The time the simple page was last updated */
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;

    /** @var string The content of the simple page */
    #[JinyaApiField(required: true)]
    public string $content;
    /** @var string The title of the simple page */
    #[JinyaApiField(required: true)]
    public string $title;

    /**
     * Finds all simple pages matching the given keyword
     *
     * @param string $keyword
     * @return Iterator<SimplePage>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, content, title FROM page WHERE title LIKE :titleKeyword OR content LIKE :contentKeyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['contentKeyword' => "%$keyword%", 'titleKeyword' => "%$keyword%"], [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds all simple pages
     *
     * @return Iterator<SimplePage>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'page',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Creates the current simple page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->id = $this->internalCreate(
            'page',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Deletes the current simple page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('page');
    }

    /**
     * Updates the current simple page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->internalUpdate(
            'page',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Formats the current page
     *
     * @return array<string, array<string, array<string, string|null>|string>|int|string>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => 'int',
        'title' => 'string',
        'content' => 'string',
        'created' => 'array',
        'updated' => 'array'
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'title' => $this->title,
            'content' => $this->content,
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
     * Gets the creator
     *
     * @return Artist|null
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
     * Finds the simple page with the matching ID
     *
     * @param int $id
     * @return SimplePage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?SimplePage
    {
        return self::fetchSingleById(
            'page',
            $id,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the artist who last updated the page
     *
     * @return Artist|null
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
