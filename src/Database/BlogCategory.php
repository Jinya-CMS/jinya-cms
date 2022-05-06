<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 *
 */
#[JinyaApi]
class BlogCategory extends Utils\LoadableEntity
{
    #[JinyaApiField(required: true)]
    public string $name = '';
    #[JinyaApiField]
    public ?string $description = null;
    #[JinyaApiField]
    public ?int $parentId = null;
    #[JinyaApiField]
    public bool $webhookEnabled = false;
    #[JinyaApiField]
    public ?string $webhookUrl = '';

    /**
     * @inheritDoc
     * @return Iterator<BlogCategory>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT * FROM blog_category WHERE name LIKE :nameKeyword OR description LIKE :descKeyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogCategory>
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('blog_category', new self());
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('blog_category', [
            'webhookEnabled' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('blog_category');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('blog_category', [
            'webhookEnabled' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @return array{id: int, name: string, description: string|null, parent: array|null, webhookEnabled: bool, webhookUrl: string|null}
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    #[ArrayShape(['id' => 'int', 'name' => 'string', 'description' => 'null|string', 'parent' => 'array', 'webhookEnabled' => 'bool', 'webhookUrl' => 'null|string'])] public function format(): array
    {
        $parent = $this->getParent()?->format();

        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'description' => $this->description,
            'parent' => $parent,
            'webhookEnabled' => $this->webhookEnabled,
            'webhookUrl' => $this->webhookUrl,
        ];
    }

    /**
     * @return BlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function getParent(): BlogCategory|null
    {
        if ($this->parentId === null) {
            return null;
        }

        return self::findById($this->parentId);
    }

    /**
     * @inheritDoc
     * @throws NoResultException
     */
    public static function findById(int $id): BlogCategory|null
    {
        return parent::fetchSingleById('blog_category', $id, new self());
    }

    /**
     * @param bool $includeChildCategories
     * @param bool $onlyPublic
     * @return Iterator<BlogPost>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function getBlogPosts(bool $includeChildCategories = false, bool $onlyPublic = false): Iterator
    {
        if ($includeChildCategories) {
            $sql = <<<'SQL'
WITH RECURSIVE category_cte AS
                   (
                       SELECT bc.*
                       FROM blog_category AS bc
                       WHERE bc.id = :categoryId
                       UNION
                       SELECT bc2.*
                       FROM category_cte
                                INNER JOIN blog_category AS bc2 ON category_cte.id = bc2.parent_id
                   )
SELECT * FROM blog_post WHERE category_id IN (SELECT id FROM category_cte)
SQL;
        } else {
            $sql = 'SELECT * FROM blog_post WHERE category_id = :categoryId';
        }
        if ($onlyPublic) {
            $sql .= ' AND public = true';
        }

        try {
            return self::getPdo()->fetchIterator($sql, new BlogPost(), ['categoryId' => $this->id], [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'created_at' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'public' => new BooleanStrategy(1, 0),
            ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}
