<?php

namespace App\Database;

use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 *
 */
class BlogPostSegment extends Utils\RearrangableEntity
{
    public ?int $galleryId = null;
    public ?int $fileId = null;
    public ?string $html = null;
    public ?string $link = null;
    public int $blogPostId;
    public int $position;

    /**
     * @inheritDoc
     */
    public static function findById(int $id): BlogPostSegment|null
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogPostSegment>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogPostSegment>
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @param int $id
     * @param int $position
     * @return BlogPostSegment|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findByPosition(int $id, int $position): BlogPostSegment|null
    {
        $sql = 'SELECT * FROM blog_post_segment WHERE blog_post_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql, new self(), [
                'id' => $id,
                'position' => $position,
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @return array<string, array<string, int|string|null>|int|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'position' => 'int',
        'id' => 'int',
        'gallery' => 'array',
        'link' => 'null|string',
        'html' => 'null|string',
        'file' => 'array',
    ])] public function format(): array
    {
        $data = [
            'position' => $this->position,
            'id' => $this->getIdAsInt(),
        ];
        if (isset($this->galleryId)) {
            $gallery = Gallery::findById($this->galleryId);
            $data['gallery'] = [
                'id' => $this->galleryId,
                'name' => $gallery?->name,
                'description' => $gallery?->description,
                'type' => $gallery?->type,
                'orientation' => $gallery?->orientation,
            ];
        } elseif (isset($this->fileId)) {
            $file = File::findById($this->fileId);
            $data['file'] = [
                'id' => $this->fileId,
                'name' => $file?->name,
                'type' => $file?->type,
                'path' => $file?->path,
            ];
            $data['link'] = $this->link;
        } else {
            $data['html'] = $this->html;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, $this->position);
        $this->internalCreate('blog_post_segment');
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('blog_post_segment');
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, -1);
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * @return BlogPost|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getBlogPost(): BlogPost|null
    {
        return BlogPost::findById($this->blogPostId);
    }

    /**
     * @inheritDoc
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, $newPosition);
        $this->update();
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('blog_post_segment');
    }

    /**
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFile(): ?File
    {
        if ($this->fileId === null) {
            return null;
        }

        return File::findById($this->fileId);
    }

    /**
     * @return Gallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getGallery(): ?Gallery
    {
        if ($this->galleryId === null) {
            return null;
        }

        return Gallery::findById($this->galleryId);
    }
}