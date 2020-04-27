<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use Exception;
use Iterator;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use RuntimeException;

class UploadingFile extends Utils\LoadableEntity
{
    public int $fileId;

    public static function findByFile(int $fileId): ?UploadingFile
    {
        $sql = self::getSql();
        $select = $sql->select()->from('uploading_file')->where(['file_id = :fileId']);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['fileId' => $fileId]);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return self::hydrateSingleResult($result, new self());
    }

    /**
     * @inheritDoc
     * @return UploadingFile
     */
    public static function findById(int $id)
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
     */
    public function getChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->fileId);
    }

    /**
     * Gets the corresponding file
     *
     * @return File
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
        $sql = self::getSql();
        $insert = $sql->insert('uploading_file');
        $insert->values(['id' => bin2hex(random_bytes(20)), 'file_id' => $this->fileId]);
        try {
            self::executeStatement($sql->prepareStatementForSqlObject($insert));
        } catch (InvalidQueryException $exception) {
            throw $this->convertInvalidQueryExceptionToException($exception);
        }
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