<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Database\Converter\BooleanConverter;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Router\Extensions\Database\Attributes\Create;
use Jinya\Router\Extensions\Database\Attributes\Delete;
use Jinya\Router\Extensions\Database\Attributes\Find;
use Jinya\Router\Extensions\Database\Attributes\Update;
use JsonSerializable;
use PDO;
use PDOException;

/**
 * This class contains a blog category, blog categories can have child categories and have blog posts.
 * Categories can contain a webhook URL, this URL is triggered with a POST request whenever a post is created or updated.
 */
#[Table('blog_category')]
#[Find('/api/blog-category', new AuthorizationMiddleware(ROLE_READER))]
#[Create('/api/blog-category', new AuthorizationMiddleware(ROLE_WRITER))]
#[Update('/api/blog-category', new AuthorizationMiddleware(ROLE_WRITER))]
#[Delete('/api/blog-category', new AuthorizationMiddleware(ROLE_WRITER))]
class BlogCategory extends Entity implements JsonSerializable
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var string The name of the category */
    #[Column]
    public string $name = '';

    /** @var string|null The description of the category */
    #[Column]
    public ?string $description = null;

    /** @var int|null The ID of the parent category */
    #[Column(sqlName: 'parent_id')]
    public ?int $parentId = null;

    /** @var bool Indicates whether the blog post triggers a webhook on create and update */
    #[Column(sqlName: 'webhook_enabled')]
    #[BooleanConverter]
    public bool $webhookEnabled = false;

    /** @var string|null The URL for the webhook to trigger */
    #[Column(sqlName: 'webhook_url')]
    public ?string $webhookUrl = '';

    /**
     * Formats the blog category
     *
     * @return array<string, int|string|null|mixed|bool>
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent' => $parent,
            'webhookEnabled' => $this->webhookEnabled,
            'webhookUrl' => $this->webhookUrl,
        ];
    }

    /**
     * Gets the category parent category, if it has no parent, the result is null
     *
     * @return BlogCategory|null
     */
    public function getParent(): BlogCategory|null
    {
        if ($this->parentId === null) {
            return null;
        }

        return self::findById($this->parentId);
    }

    /**
     * Gets all blog posts in the current category
     *
     * @param bool $includeChildCategories If true, also posts from child categories are selected
     * @param bool $onlyPublic If true, only public posts are selected
     * @return Iterator<BlogPost>
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

        $pdo = self::getPDO();
        $statement = $pdo->prepare($sql);
        if ($statement === false) {
            throw new PDOException('Failed to execute query');
        }

        $result = $statement->execute(['categoryId' => $this->id]);
        if ($result === false) {
            $ex = new PDOException('Failed to execute query');
            $ex->errorInfo = $statement->errorInfo();
            throw $ex;
        }

        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $item) {
            yield BlogPost::fromArray($item);
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->format();
    }
}
