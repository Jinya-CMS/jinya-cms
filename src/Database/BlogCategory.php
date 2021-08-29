<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\FormattableEntityInterface;
use Iterator;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class BlogCategory extends Utils\LoadableEntity implements FormattableEntityInterface
{

    public string $name;
    public string $description;
    public ?int $parentId = null;

    /**
     * @inheritDoc
     * @return Iterator<BlogCategory>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT * FROM blog_category WHERE name LIKE :nameKeyword OR description LIKE :descKeyword';
        /** @var array<array> */
        $result = self::executeStatement($sql, ['descKeyword' => "%$keyword%", 'nameKeyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self());
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogCategory>
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('blog_category', new self());
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('blog_category');
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
        $this->internalUpdate('blog_category');
    }

    public function format(): array
    {
        $parent = $this->getParent()?->format();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent' => $parent,
        ];
    }

    public function getParent(): BlogCategory|null
    {
        if ($this->parentId === null) {
            return null;
        }

        return self::findById($this->parentId);
    }

    /**
     * @inheritDoc
     * @noinspection PhpIncompatibleReturnTypeInspection
     * @psalm-suppress MoreSpecificReturnType
     */
    public static function findById(int $id): BlogCategory|null
    {
        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress LessSpecificReturnStatement
         */
        return parent::fetchSingleById('blog_category', $id, new self());
    }

    /**
     * @param bool $includeChildCategories
     * @return Iterator<BlogPost>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function getBlogPosts(bool $includeChildCategories = false): Iterator
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
        /** @var array<array> */
        $result = self::executeStatement($sql, ['categoryId' => $this->id]);

        return self::hydrateMultipleResults($result, new BlogPost(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }
}