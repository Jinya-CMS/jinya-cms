<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 * This class contains information about files stored with the media manager in Jinya CMS
 */
#[JinyaApi]
class File extends LoadableEntity
{
    /** @var int The ID of the files creator */
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    /** @var int The ID of the artist who last touched the file */
    #[JinyaApiField(ignore: true)]
    public int $updatedById;
    /** @var DateTime The time the file was created at */
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    /** @var DateTime The time the file was last updated at */
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;
    /** @var string The path where the file is stored, this path is relative to the web root directory */
    #[JinyaApiField(ignore: true)]
    public string $path = '';
    /** @var string The name of the file */
    #[JinyaApiField(required: true)]
    public string $name = '';
    /** @var string The type of the file */
    #[JinyaApiField(ignore: true)]
    public string $type = '';
    /** @var string[] The tags of the file */
    #[JinyaApiField(ignore: false)]
    public ?array $tags = null;

    /**
     * Finds all files matching the given keyword
     *
     * @param string $keyword
     * @return Iterator<File>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, path, name, type FROM file WHERE name LIKE :keyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['keyword' => "%$keyword%"], [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds all files
     *
     * @return Iterator<File>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'file',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the uploading chunks
     *
     * @return Iterator<UploadingFileChunk>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getUploadChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->getIdAsInt());
    }

    /**
     * Creates the current file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        $this->id = $this->internalCreate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ],
            [
                'tags',
            ]
        );

        foreach ($this->tags as $tag) {
            $tag = FileTag::findByName($tag);
            if ($tag) {
                self::executeStatement('INSERT INTO file_tag_file (file_id, file_tag_id) VALUES (:file_id, :tag_id)', ['file_id' => $this->id, 'tag_id' => $tag->id]);
            }
        }
    }

    /**
     * Deletes the current file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('file');
    }

    /**
     * Updates the current file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;
        $this->internalUpdate(
            'file',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ],
            [
                'tags',
            ]
        );

        if (is_array($this->tags)) {
            self::executeStatement('DELETE FROM file_tag_file WHERE file_id = :file_id', ['file_id' => $this->id]);
            foreach ($this->tags as $tag) {
                $tag = FileTag::findByName($tag);
                if ($tag) {
                    self::executeStatement('INSERT INTO file_tag_file (file_id, file_tag_id) VALUES (:file_id, :tag_id)', ['file_id' => $this->id, 'tag_id' => $tag->id]);
                }
            }
        }
    }

    /**
     * Formats the file into an array
     *
     * @return array{id: int, name: string, type: string, path: string, tags: array<array{name: string, color: string|null, emoji: string|null}>, created: array{by: array{artistName: string|null, email: string|null, profilePicture: string|null}, at: non-falsy-string}, updated: array{by: array{artistName: string|null, email: string|null, profilePicture: string|null}, at: non-falsy-string}}
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => 'int',
        'name' => 'string',
        'type' => 'string',
        'path' => 'string',
        'tags' => 'array',
        'created' => 'array',
        'updated' => 'array'
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'tags' => array_map(static fn(FileTag $tag) => $tag->format(), iterator_to_array($this->getTags())),
            'created' => [
                'by' => [
                    'artistName' => $creator?->artistName,
                    'email' => $creator?->email,
                    'profilePicture' => $creator?->profilePicture,
                ],
                'at' => $this->createdAt->format(DATE_ATOM),
            ],
            'updated' => [
                'by' => [
                    'artistName' => $updatedBy?->artistName,
                    'email' => $updatedBy?->email,
                    'profilePicture' => $updatedBy?->profilePicture,
                ],
                'at' => $this->lastUpdatedAt->format(DATE_ATOM),
            ],
        ];
    }

    /**
     * Gets the creator of this file
     *
     * @return Artist|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getCreator(): ?Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Finds the file with the given id
     *
     * @param int $id
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?File
    {
        return self::fetchSingleById(
            'file',
            $id,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getUpdatedBy(): ?Artist
    {
        return Artist::findById($this->updatedById);
    }

    /**
     * Gets all tags for the given file
     *
     * @return Iterator<FileTag>
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getTags(): Iterator
    {
        $sql = <<<'SQL'
SELECT 
    id, name, color, emoji 
FROM 
    file_tag ft
INNER JOIN 
    file_tag_file ftf
ON
    ft.id = ftf.file_tag_id
WHERE
    ftf.file_id = :file_id
SQL;

        try {
            return self::getPdo()->fetchIterator($sql, new FileTag(), ['file_id' => $this->id]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}
