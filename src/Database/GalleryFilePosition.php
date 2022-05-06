<?php

namespace App\Database;

use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 *
 */
class GalleryFilePosition extends Utils\RearrangableEntity
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
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByPosition(int $id, int $position): ?GalleryFilePosition
    {
        $sql = 'SELECT * FROM gallery_file_position WHERE gallery_id = :id AND position = :position';

        try {
            return self::getPdo()->fetchObject($sql,
                new self(),
                [
                    'id' => $id,
                    'position' => $position
                ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $this->position);
        $this->internalCreate('gallery_file_position');
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('gallery_file_position');
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, -1);
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * @return array<string, array<string, int|string|null>|int>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'gallery' => 'array',
        'file' => 'array',
        'id' => 'int',
        'position' => 'int'
    ])] public function format(): array
    {
        $gallery = $this->getGallery();
        $file = $this->getFile();
        return [
            'gallery' => [
                'id' => $this->galleryId,
                'name' => $gallery?->name,
                'description' => $gallery?->description,
            ],
            'file' => [
                'path' => $file?->path,
                'id' => $file?->getIdAsInt(),
                'name' => $file?->name,
                'type' => $file?->type,
            ],
            'id' => $this->getIdAsInt(),
            'position' => $this->position,
        ];
    }

    /**
     * Gets the associated gallery
     *
     * @return Gallery|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Gets the associated file
     *
     * @return File|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * @inheritDoc
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $newPosition);
        $this->update();
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('gallery_file_position');
    }
}