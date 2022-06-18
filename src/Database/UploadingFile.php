<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Utils\UuidGenerator;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use RuntimeException;

/**
 * This class contains and uploading file. Uploading files are temporary entities used to handle the chunked upload feature of Jinya CMS
 */
class UploadingFile extends Utils\LoadableEntity
{
    /** @var int The ID of the file the uploading file belongs to */
    public int $fileId;

    /**
     * Finds an uploading file by the ID of the connected file
     *
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
        return File::findById($this->fileId);
    }

    /**
     * Creates the current uploading file
     *
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->id = UuidGenerator::generateV4();
        $sql = 'INSERT INTO uploading_file (id, file_id) VALUES (:id, :fileId)';
        self::executeStatement($sql, ['id' => $this->id, 'fileId' => $this->fileId]);
    }

    /**
     * Deletes the current uploading file
     *
     * @return void
     * @throws Exceptions\UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file');
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

    /**
     * Converts the uploading file ID to string
     *
     * @return string
     */
    public function getIdAsString(): string
    {
        return (string)$this->id;
    }
}
