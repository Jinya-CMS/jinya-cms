<?php

namespace App\Database;

use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\EntityTrait;

/**
 * This class contains a segment of segment pages. Segments can contain different kinds of data, currently supported are galleries, files, and static HTML content. If the data is a file, it is also possible to specify an action which can be either script or link. Depending on the action, the fields have to be set accordingly.
 */
#[Table('segment')]
class ModernPageSection
{
    use EntityTrait;

    #[Id]
    #[Column]
    public int $id;

    #[Column]
    public int $position;

    /** @var int|null The gallery in the segment */
    #[Column(sqlName: 'gallery_id')]
    public ?int $galleryId = null;

    /** @var int|null The file in the segment */
    #[Column(sqlName: 'file_id')]
    public ?int $fileId = null;

    /** @var string|null The html content in the segment */
    #[Column]
    public ?string $html = null;

    /** @var int The ID of the segment page this segment belongs to */
    #[Column(sqlName: 'page_id')]
    public int $pageId;

    /** @var string|null The action type to execute when the file is clicked. It can be either script, link or none */
    #[Column]
    public ?string $action = null;

    /** @var string|null The script to execute when the file is clicked */
    #[Column]
    public ?string $script = null;

    /** @var string|null The link to open when the file is clicked */
    #[Column]
    public ?string $target = null;

    /**
     * Gets the file
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
     * Gets the gallery
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

    /**
     * Gets the corresponding segment page
     *
     * @return ModernPage|null
     * @deprecated Use getModernPage instead
     */
    public function getSegmentPage(): ?ModernPage {
        return $this->getModernPage();
    }

    /**
     * Gets the corresponding segment page
     *
     * @return ModernPage|null
     */
    public function getModernPage(): ?ModernPage
    {
        return ModernPage::findById($this->pageId);
    }

    /**
     * Formats the segment into an array
     *
     * @return array<string, array<string, int|string|null>|int|string|null>
     */
    #[ArrayShape([
        'position' => 'int',
        'id' => 'int',
        'gallery' => 'array',
        'link' => 'null|string',
        'html' => 'null|string',
        'script' => 'null|string',
        'file' => 'array'
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
            if ($this->action === 'script') {
                $data['script'] = $this->script;
            }
            if ($this->action === 'link') {
                $data['link'] = $this->target;
            }
        } else {
            $data['html'] = $this->html;
        }

        return $data;
    }
}
