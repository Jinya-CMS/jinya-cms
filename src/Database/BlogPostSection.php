<?php

namespace Jinya\Cms\Database;

use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\EntityTrait;

/**
 * This class contains a part of a blog post.
 * A blog post consists of several sections.
 * A section must either contain a gallery, a file or html content
 */
#[Table('blog_post_segment')]
class BlogPostSection
{
    use EntityTrait;

    #[Id]
    #[Column]
    public int $id;

    /** @var int|null The gallery in the segment */
    #[Column(sqlName: 'gallery_id')]
    public ?int $galleryId = null;

    /** @var int|null The file in the segment */
    #[Column(sqlName: 'file_id')]
    public ?int $fileId = null;

    /** @var string|null The html content in the segment */
    #[Column]
    public ?string $html = null;

    /** @var string|null The link opened when the file is clicked, implementation is theme-specific */
    #[Column]
    public ?string $link = null;

    /** @var int The blog post the segment belongs to */
    #[Column(sqlName: 'blog_post_id')]
    public int $blogPostId;

    /** @var int The position of the segment */
    #[Column]
    public int $position;

    /**
     * Formats the blog post segment into an array
     *
     * @return array<string, array<string, int|string|null>|int|string|null>
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
            'id' => $this->id,
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
     * Gets the post this segment belongs to
     *
     * @return BlogPost|null
     */
    public function getBlogPost(): BlogPost|null
    {
        return BlogPost::findById($this->blogPostId);
    }

    /**
     * The file this segment contains
     *
     * @return File|null
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
     */
    public function getGallery(): ?Gallery
    {
        if ($this->galleryId === null) {
            return null;
        }

        return Gallery::findById($this->galleryId);
    }
}
