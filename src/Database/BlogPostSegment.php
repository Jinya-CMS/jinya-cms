<?php

namespace App\Database;

use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 * This class contains a part of a blog post. A blog post consists of several segments.
 * A segment must either contain a gallery, a file or html content
 */
class BlogPostSegment extends Utils\RearrangableEntity
{
    /** @var int|null The gallery in the segment */
    public ?int $galleryId = null;
    /** @var int|null The file in the segment */
    public ?int $fileId = null;
    /** @var string|null The html content in the segment */
    public ?string $html = null;
    /** @var string|null The link opened when the file is clicked, implementation is theme specific */
    public ?string $link = null;
    /** @var int The blog post the segment belongs to */
    public int $blogPostId;
    /** @var int The position of the segment */
    public int $position;

    /**
     * Not implemented
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Not implemented
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Finds the segment by a post and the position
     *
     * @param int $id The ID of the blog post
     * @param int $position The position of the blog post
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
     * Formats the blog post segment into an array
     *
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
     * Not implemented
     */
    public static function findById(int $id): BlogPostSegment|null
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Creates the current blog post segment. During creation the other blog post segments will be moved based on the position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, $this->position);
        $this->internalCreate('blog_post_segment');
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * Deletes the current blog post segment, the order of the remaining segments will be reset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('blog_post_segment');
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, -1);
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * Gets the post this segment belongs to
     *
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
     * Moves the blog post segment to a new position and rearranges the other items accordingly
     *
     * @param int $newPosition
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('blog_post_segment', 'blog_post_id', $this->blogPostId, $newPosition);
        $this->update();
        $this->resetOrder('blog_post_segment', 'blog_post_id', $this->blogPostId);
    }

    /**
     * Updates the current blog post segment
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('blog_post_segment');
    }

    /**
     * The file this segment contains
     *
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
     * The gallery this segment contains
     *
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
