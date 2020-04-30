<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use DateTime;
use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class SimplePage extends Utils\LoadableEntity
{

    public int $creatorId;
    public int $updatedById;
    public DateTime $createdAt;
    public DateTime $lastUpdatedAt;
    public string $content;
    public string $title;
    public string $slug;
    private string $name;

    /**
     * @inheritDoc
     * @return SimplePage
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('page', $id, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * Gets the simple page by the given slug
     *
     * @param string $slug
     * @return SimplePage
     */
    public static function findBySlug(string $slug): ?SimplePage
    {
        return self::fetchSingleBySlug('page', $slug, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('page')
            ->where(['title LIKE :keyword', 'content LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('page', new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->name = $this->title;
        $this->slug = $this->generateSlug($this->title);

        $this->id = $this->internalCreate('page', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('page');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->name = $this->title;

        $this->internalUpdate('page', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * Formats the current page
     *
     * @return array
     */
    public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
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
     * Gets the creator
     *
     * @return Artist
     */
    public function getCreator(): Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist who last updated the page
     *
     * @return Artist
     */
    public function getUpdatedBy(): Artist
    {
        return Artist::findById($this->updatedById);
    }
}