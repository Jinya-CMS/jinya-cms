<?php

namespace App\Database;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;

#[OpenApiModel('Gallery file positions contain information about the files in a gallery')]
class GalleryFilePosition extends Utils\RearrangableEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiField(structure: [
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'description' => ['type' => 'string'],
    ], name: 'gallery')]
    public int $galleryId;
    #[OpenApiField(structure: [
        'path' => ['type' => 'string'],
        'id' => ['type' => 'integer'],
        'name' => ['type' => 'string'],
        'type' => ['type' => 'string'],
    ], name: 'file')]
    public int $fileId;
    #[OpenApiField(required: true)]
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
     * @return array
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    #[ArrayShape([
        'gallery' => "array",
        'file' => "array",
        'id' => "int",
        'position' => "int"
    ])] public function format(): array
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
     * @return Gallery|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
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