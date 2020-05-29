<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use DateTime;
use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class Gallery extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{

    public const TYPE_SEQUENCE = 'sequence';
    public const TYPE_MASONRY = 'masonry';

    public const ORIENTATION_HORIZONTAL = 'horizontal';
    public const ORIENTATION_VERTICAL = 'vertical';

    public int $creatorId;
    public int $updatedById;
    public DateTime $createdAt;
    public DateTime $lastUpdatedAt;
    public string $name;
    public string $description = '';
    public string $type = self::TYPE_SEQUENCE;
    public string $orientation = self::ORIENTATION_HORIZONTAL;

    /**
     * @inheritDoc
     * @return Gallery
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('gallery', $id, new self(),
            [
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
            ->from('gallery')
            ->where(['name LIKE :keyword', 'description LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('gallery', new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }

    public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->id,
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
     */
    public function getCreator(): Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist
     */
    public function getUpdatedBy(): Artist
    {
        return Artist::findById($this->updatedById);
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

        $this->id = $this->internalCreate('gallery',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
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
        $this->internalUpdate('gallery',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }

    /**
     * Get all file positions in gallery
     *
     * @return Iterator
     */
    public function getFiles(): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('gallery_file_position')
            ->where(['gallery_id = :id'])
            ->order('position ASC');

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new GalleryFilePosition(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }
}