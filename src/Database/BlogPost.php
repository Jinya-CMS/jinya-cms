<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Utils\FormattableEntityInterface;
use DateTime;
use Iterator;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class BlogPost extends Utils\LoadableEntity implements FormattableEntityInterface
{
    public string $title;
    public string $slug;
    public ?int $headerImageId = null;
    public bool $public = false;
    public DateTime $createdAt;
    public int $creatorId;
    public int $categoryId;

    /**
     * @inheritDoc
     */
    public static function findById(int $id): ?object
    {
        // TODO: Implement findById() method.
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        // TODO: Implement findByKeyword() method.
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        // TODO: Implement findAll() method.
    }

    public static function findBySlug(string $slug): BlogPost
    {

    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser?->id;

        $this->internalCreate('blog_post', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function format(): array
    {
        // TODO: Implement format() method.
    }

    public function getCategory(): BlogCategory
    {

    }

    public function getCreator(): Artist
    {

    }

    public function getHeaderImage(): File|null
    {

    }
}