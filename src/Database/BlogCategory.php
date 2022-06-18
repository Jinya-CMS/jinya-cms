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
 * This class contains a blog category, blog categories can have child categories and have blog posts.
 * Categories can contain a webhook URL, this URL is triggered with a POST request whenever a post is created or updated.
 */
#[JinyaApi]
class BlogCategory extends Utils\LoadableEntity
{
    /** @var string The name of the category */
    #[JinyaApiField(required: true)]
    public string $name = '';
    /** @var string|null The description of the category */
    #[JinyaApiField]
    public ?string $description = null;
    /** @var int|null The ID of the parent category */
    #[JinyaApiField]
    public ?int $parentId = null;
    /** @var bool Indicates whether the blog post trigger a webhook on create and update */
    #[JinyaApiField]
    public bool $webhookEnabled = false;
    /** @var string|null The URL for the webhook to trigger */
    #[JinyaApiField]
    public ?string $webhookUrl = '';

    /**
     * Finds the blog categories by keyword
     *
     * @param string $keyword
     * @return Iterator<BlogCategory>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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
     * Finds all blog categories
     *
     * @return Iterator<BlogCategory>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('blog_category', new self());
    }

    /**
     * Creates the current blog category
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function create(): void
    {
        $this->internalCreate('blog_category', [
            'webhookEnabled' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * Deletes the current blog category
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function delete(): void
    {
        $this->internalDelete('blog_category');
    }

    /**
     * Updates the current blog category
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('blog_category', [
            'webhookEnabled' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * Formats the blog category
     *
     * @return array<string, int|string|null|mixed|bool>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    #[ArrayShape([
        'id' => 'int',
        'name' => 'string',
        'description' => 'null|string',
        'parent' => 'array',
        'webhookEnabled' => 'bool',
        'webhookUrl' => 'null|string',
    ])]
    public function format(): array
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
     * Gets the categories parent category, if it has no parent the result is null
     *
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
     * Finds the blog category with the given ID
     *
     * @param int $id
     * @return BlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public static function findById(int $id): BlogCategory|null
    {
        return parent::fetchSingleById('blog_category', $id, new self());
    }

    /**
     * Gets all blog posts in the current category
     *
     * @param bool $includeChildCategories If true, also posts from child categories are selected
     * @param bool $onlyPublic If true, only public posts are selected
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
