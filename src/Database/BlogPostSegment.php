<?php

namespace App\Database;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

#[OpenApiModel('A blog post segment', hasId: false)]
class BlogPostSegment extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'description' => ['type' => 'string'],
        'type' => ['type' => 'string', 'enum' => [Gallery::TYPE_SEQUENCE, Gallery::TYPE_MASONRY]],
        'orientation' => [
            'type' => 'string',
            'enum' => [Gallery::ORIENTATION_VERTICAL, Gallery::ORIENTATION_HORIZONTAL]
        ],
    ], name: 'gallery')]
    public ?int $galleryId = null;
    #[OpenApiField(required: false, structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'type' => ['type' => 'string'],
        'path' => ['type' => 'string'],
    ], name: 'file')]
    public ?int $fileId = null;
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $html = null;
    #[OpenApiField(required: false, defaultValue: '')]
    public ?string $link = null;
    #[OpenApiHiddenField]
    public int $blogPostId;
    #[OpenApiField(required: true)]
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

    public static function findByPosition(int $id, int $position): BlogPostSegment|null
    {
        $sql = 'SELECT * FROM blog_post_segment WHERE blog_post_id = :id AND position = :position';
        $result = self::executeStatement($sql, [
                'id' => $id,
                'position' => $position,
            ]
        );

        if ((is_countable($result) && count($result) === 0) || !is_countable($result)) {
            return null;
        }

        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * @inheritDoc
     * @psalm-suppress MoreSpecificReturnType
     * @return array{position: int, id: int, gallery: array, link: string, html: string, file: array}
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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

        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress LessSpecificReturnStatement
         */
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
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('blog_post_segment');
    }

    /**
     * @return BlogPost|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
}