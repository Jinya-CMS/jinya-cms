<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Utils\UuidGenerator;
use Exception;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

class UploadingFile extends Utils\LoadableEntity
{
    public int $fileId;

    /**
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByFile(int $fileId): ?UploadingFile
    {
        $sql = 'SELECT id, file_id FROM uploading_file WHERE file_id = :fileId';

        try {
            return self::getPdo()->fetchObject($sql, new self(), ['fileId' => $fileId]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * {@inheritDoc}
     * @return UploadingFile
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
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
     * Gets all chunks
     *
     * @throws InvalidQueryException
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
     * @return File|null
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFile(): ?File
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return File::findById($this->fileId);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     * @throws ForeignKeyFailedException
     */
    public function create(): void
    {
        $this->id = UuidGenerator::generateV4();
        $sql = 'INSERT INTO uploading_file (id, file_id) VALUES (:id, :fileId)';
        self::executeStatement($sql, ['id' => $this->id, 'fileId' => $this->fileId]);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file');
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
