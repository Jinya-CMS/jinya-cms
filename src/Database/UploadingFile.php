<?php

namespace App\Database;

use App\Utils\UuidGenerator;
use Exception;
use Iterator;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\CreatableEntityTrait;
use Jinya\Database\Deletable;
use Jinya\Database\EntityTrait;
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\UniqueFailedException;
use PDOException;

/**
 * This class contains an uploading file.
 * Uploading files are temporary entities used to handle the chunked upload feature of Jinya CMS
 */
#[Table('uploading_file')]
class UploadingFile implements Creatable, Deletable
{
    use EntityTrait;

    #[Column]
    public string $id;

    /** @var int The ID of the file the uploading file belongs to */
    #[Column(sqlName: 'file_id')]
    public int $fileId;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->id = UuidGenerator::generateV4();
    }

    /**
     * Gets all chunks
     *
     * @return Iterator
     * @throws Exception
     */
    public function getChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->fileId);
    }

    /**
     * Finds an uploading file by the ID of the connected file
     *
     * @param int $fileId
     * @return UploadingFile|null
     */
    public static function findByFile(int $fileId): ?UploadingFile
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'file_id'
            ])
            ->where('file_id = :fileId', ['fileId' => $fileId]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Gets the corresponding file
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    public function create(): void
    {
        $insert = self::getQueryBuilder()
            ->newInsert()
            ->into(self::getTableName())
            ->addRow([
                'id' => $this->id,
                'file_id' => $this->fileId
            ]);

        try {
            self::executeQuery($insert);
        } catch (PDOException $exception) {
            $errorInfo = $exception->errorInfo ?? ['', ''];
            if ($errorInfo[1] === 1062) {
                throw new UniqueFailedException($exception, self::getPDO());
            }

            if ($errorInfo[1] === 1452) {
                throw new ForeignKeyFailedException($exception, self::getPDO());
            }

            throw $exception;
        }
    }

    public function delete(): void
    {
        $delete = self::getQueryBuilder()
            ->newDelete()
            ->from(self::getTableName())
            ->where('id = :id', ['id' => $this->id]);

        self::executeQuery($delete);
    }
}
