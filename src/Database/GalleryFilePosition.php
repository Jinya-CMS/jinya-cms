<?php

namespace App\Database;

use Iterator;
use RuntimeException;

class GalleryFilePosition extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    public int $galleryId;
    public int $fileId;
    public int $position;

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('gallery_file_position', $id, new self());
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('gallery_file_position');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('gallery_file_position');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('gallery_file_position');
    }

    public function format(): array
    {
        $gallery = $this->getGallery();
        $file = $this->getFile();
        return [
            'gallery' => [
                'id' => $this->galleryId,
                'name' => $gallery->name,
                'slug' => $gallery->slug,
                'description' => $gallery->description,
            ],
            'file' => [
                'path' => $file->path,
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
            ],
            'id' => $this->id,
            'position' => $this->position,
        ];
    }

    /**
     * Gets the associated gallery
     *
     * @return Gallery
     */
    public function getGallery(): Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the associated file
     *
     * @return File
     */
    public function getFile(): File
    {
        return File::findById($this->fileId);
    }
}