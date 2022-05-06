<?php

namespace App\Database;

use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

class UploadingFileChunk extends Utils\LoadableEntity
{
    public string $uploadingFileId;
    public string $chunkPath;
    public int $chunkPosition;

    /**
     * {@inheritDoc}
     * @throws NoResultException
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById('uploading_file_chunk', $id, new self());
    }

    /**
     * {@inheritDoc}
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('uploading_file_chunk');
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file_chunk');
    }

    /**
     * {@inheritDoc}
     */
    public function update(): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function format(): array
    {
        return [];
    }
}
