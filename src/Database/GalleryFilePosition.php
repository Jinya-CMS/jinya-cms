<?php

namespace App\Database;

use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 * This class is a mapper class to combine galleries and files, while maintaining a position the file is placed at in the gallery
 */
class GalleryFilePosition extends Utils\RearrangableEntity
{
    /** @var int The ID of the gallery */
    public int $galleryId;
    /** @var int The ID of the file */
    public int $fileId;

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
     * Gets the gallery file position at the given position in the given gallery
     *
     * @param int $id The gallery ID
     * @param int $position The files position
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
            return self::getPdo()->fetchObject(
                $sql,
                new self(),
                [
                    'id' => $id,
                    'position' => $position
                ]
            );
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Creates the current gallery file position, also moves the position of the other gallery file positions according to the new position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $this->position);
        $this->internalCreate('gallery_file_position');
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * Deletes the current gallery file position, also resets the position of the other gallery file positions according to the new position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('gallery_file_position');
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, -1);
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * Formats the gallery file position into an array
     *
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
     * Not implemented
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
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
     * Moves the current gallery file position to the new position. All other gallery file positions are rearranged accordingly
     *
     * @param int $newPosition
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->internalRearrange('gallery_file_position', 'gallery_id', $this->galleryId, $newPosition);
        $this->update();
        $this->resetOrder('gallery_file_position', 'gallery_id', $this->galleryId);
    }

    /**
     * Updates the current gallery file position
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('gallery_file_position');
    }
}
