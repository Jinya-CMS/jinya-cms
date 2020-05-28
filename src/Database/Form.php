<?php

namespace App\Database;

use DateTime;
use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\Json;

class Form extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    public int $creatorId;
    public int $updatedById;
    public DateTime $createdAt;
    public DateTime $lastUpdatedAt;

    public string $title;
    public string $description;
    public string $emailTemplate;
    public string $slug;
    public string $toAddress;

    /**
     * @inheritDoc
     * @return Form
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('form', $id, new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }

    /**
     * Gets the form with the given slug
     *
     * @param string $slug
     * @return Form
     */
    public static function findBySlug(string $slug): Form
    {
        return self::fetchSingleBySlug('form', $slug, new self(),
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
            ->from('form')
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
        return self::fetchArray('form', new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
    }

    /**
     * Gets all form items in the form
     *
     * @return Iterator
     */
    public function getItems(): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('form_item')
            ->where(['form_id = :id'])
            ->order('position ASC');

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $this->id]);

        return self::hydrateMultipleResults($result, new FormItem(), [
            'spamFilter' => new SerializableStrategy(new Json()),
            'options' => new SerializableStrategy(new Json()),
        ]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('form',
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
        $this->internalDelete('form');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('form',
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
            'description' => $this->description,
            'title' => $this->title,
            'slug' => $this->slug,
            'toAddress' => $this->toAddress,
            'emailTemplate' => $this->emailTemplate,
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
}