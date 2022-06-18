<?php

namespace App\Database;

use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use RuntimeException;

/**
 * This class contains a chunk of an uploading file
 */
class UploadingFileChunk extends Utils\LoadableEntity
{
    /** @var string The ID of the owning uploading file */
    public string $uploadingFileId;
    /** @var string The absolut path to the chunk */
    public string $chunkPath;
    /** @var int The position of the chunk, the chunk position is used to connect the resulting chunks in the correct order */
    public int $chunkPosition;

    /**
     * Not implemented
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

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
     * Gets all chunks for the given file ordered by position
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByFile(int $fileId): Iterator
    {
        $sql = 'SELECT ufc.id AS id, ufc.chunk_position AS chunk_position, ufc.chunk_path AS chunk_path FROM uploading_file_chunk ufc JOIN uploading_file uf on ufc.uploading_file_id = uf.id WHERE uf.file_id = :fileId ORDER BY ufc.chunk_position';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['fileId' => $fileId]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Creates the current uploading file chunk
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('uploading_file_chunk');
    }

    /**
     * Deletes the current uploading file chunk
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file_chunk');
    }

    /**
     * Not implemented
     */
    public function update(): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Always returns an empty array and is not used
     *
     * @return array<string>
     */
    public function format(): array
    {
        return [];
    }
}
