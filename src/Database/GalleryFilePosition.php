<?php

namespace App\Database;

use Exception;
use Iterator;
use RuntimeException;

class GalleryFilePosition extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    public int $galleryId;
    public int $fileId;
    public int $position;

    /**
     * @inheritDoc
     * @return GalleryFilePosition
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
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
     * Gets the gallery file position at the given position
     *
     * @param int $id
     * @param int $position
     * @return GalleryFilePosition|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByPosition(int $id, int $position): ?GalleryFilePosition
    {
        $sql = 'SELECT * FROM gallery_file_position WHERE gallery_id = :id AND position = :position';
        $result = self::executeStatement(
            $sql,
            [
                'id' => $id,
                'position' => $position
            ]
        );

        if (count($result) === 0) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $this->position);
        $this->internalCreate('gallery_file_position');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('gallery_file_position');
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, -1);
    }

    /**
     * @return array
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function format(): array
    {
        $gallery = $this->getGallery();
        $file = $this->getFile();
        return [
            'gallery' => [
                'id' => $this->galleryId,
                'name' => $gallery->name,
                'description' => $gallery->description,
            ],
            'file' => [
                'path' => $file->path,
                'id' => $file->getIdAsInt(),
                'name' => $file->name,
                'type' => $file->type,
            ],
            'id' => $this->getIdAsInt(),
            'position' => $this->position,
        ];
    }

    /**
     * Gets the associated gallery
     *
     * @return Gallery
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getGallery(): Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the associated file
     *
     * @return File
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getFile(): File
    {
        return File::findById($this->fileId);
    }

    /**
     * @inheritDoc
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $newPosition);
        parent::move($newPosition);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('gallery_file_position');
    }
}