<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Utils\LoadableEntity;
use DateTime;
use Exception;
use Iterator;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class File extends LoadableEntity
{
    public int $creatorId;
    public int $updatedById;
    public DateTime $createdAt;
    public DateTime $lastUpdatedAt;
    public string $path = '';
    public string $name = '';
    public string $type = '';

    /**
     * @inheritDoc
     * @return File
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('file', $id, new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('file')
            ->where(['name LIKE :keyword']);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('file', new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
            ]);
    }

    /**
     * Gets the uploading chunks
     *
     * @return Iterator
     */
    public function getUploadChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->id);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->id = $this->internalCreate('file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
            ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('file');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;
        $this->internalUpdate('file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATA_FORMAT),
            ]);
    }

    public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'created' => [
                'by' => [
                    'artistName' => $creator->artistName,
                    'email' => $creator->email,
                    'profilePicture' => $creator->profilePicture,
                ],
                'at' => $this->createdAt->format(DATE_ATOM),
            ],
            'updated' => [
                'by' => [
                    'artistName' => $updatedBy->artistName,
                    'email' => $updatedBy->email,
                    'profilePicture' => $updatedBy->profilePicture,
                ],
                'at' => $this->lastUpdatedAt->format(DATE_ATOM),
            ],
        ];
    }

    /**
     * Gets the creator of this file
     *
     * @return Artist
     */
    public function getCreator(): Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist
     */
    public function getUpdatedBy(): Artist
    {
        return Artist::findById($this->updatedById);
    }
}