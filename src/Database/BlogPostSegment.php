<?php

namespace App\Database;

use Iterator;

class BlogPostSegment extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    public int $blogPostId;
    public int $position;

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

    /**
     * @inheritDoc
     */
    public function format(): array
    {
        // TODO: Implement format() method.
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('blog_post_segment');
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
}