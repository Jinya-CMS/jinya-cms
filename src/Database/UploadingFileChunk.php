<?php

namespace App\Database;

use Iterator;
use RuntimeException;

class UploadingFileChunk extends Utils\LoadableEntity
{
    public string $uploadingFileId;
    public string $chunkPath;
    public int $chunkPosition;

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('uploading_file_chunk', $id, new self());
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
     * Gets all chunks for the given file ordered by position
     *
     * @param int $fileId
     * @return Iterator
     */
    public static function findByFile(int $fileId): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->columns([
                'id' => 'id',
                'chunk_path' => 'chunk_path',
                'chunk_position' => 'chunk_position',
            ])
            ->from(['ufc' => 'uploading_file_chunk'])
            ->join(['uf' => 'uploading_file'], 'ufc.uploading_file_id = uf.id', ['uploading_file_id' => 'id'])
            ->where('uf.file_id = :fileId')
            ->order(['chunk_position']);
        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['fileId' => $fileId]);

        return self::hydrateMultipleResults($result, new self());
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->id = $this->internalCreate('uploading_file_chunk');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('uploading_file_chunk');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        throw new RuntimeException('Not implemented');
    }
}