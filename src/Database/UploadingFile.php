<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use Exception;
use Iterator;
use RuntimeException;

class UploadingFile extends Utils\LoadableEntity
{
    public int $fileId;

    /**
     * @param int $fileId
     * @return UploadingFile|null
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     */
    public static function findByFile(int $fileId): ?UploadingFile
    {
        $sql = 'SELECT id, file_id FROM uploading_file WHERE file_id = :fileId';
        $result = self::executeStatement($sql, ['fileId' => $fileId]);

        if (count($result) === 0) {
            return null;
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result[0], new self());
    }

    /**
     * @inheritDoc
     * @return UploadingFile
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
     * Gets all chunks
     *
     * @return Iterator
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     */
    public function getChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->fileId);
    }

    /**
     * Gets the corresponding file
     *
     * @return File
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     */
    public function getFile(): File
    {
        return File::findById($this->fileId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @throws ForeignKeyFailedException
     */
    public function create(): void
    {
        $sql = 'INSERT INTO uploading_file (id, file_id) VALUES (:id, :fileId)';
        self::executeStatement($sql, ['id' => $this->id, 'fileId' => $this->fileId]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        throw new RuntimeException('Not implemented');
    }
}