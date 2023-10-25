<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 * This class contains information about the tags of files
 */
#[JinyaApi]
class FileTag extends LoadableEntity
{

    /**
     * @var string The name of the tag
     */
    #[JinyaApiField(required: true)]
    public string $name;

    /**
     * @var string|null The color of the tag
     */
    #[JinyaApiField(required: false)]
    public ?string $color = null;

    /**
     * @var string|null The emoji of the tag
     */
    #[JinyaApiField(required: false)]
    public ?string $emoji = null;

    /**
     * @inheritDoc
     * @return FileTag|null
     * @throws NoResultException
     */
    public static function findById(int $id): ?object
    {
        return self::fetchSingleById('file_tag', $id, new self());
    }

    /**
     * Finds the given tag by name
     *
     * @param string $name
     * @return FileTag|null
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findByName(string $name): ?FileTag
    {
        return self::getPdo()->fetchObject('SELECT id, name, emoji, color FROM file_tag WHERE name = :name', new self(), ['name' => $name]);
    }

    /**
     * @inheritDoc
     * @return Iterator<FileTag>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, name, color, emoji FROM file_tag WHERE name LIKE :keyword';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['keyword' => "%$keyword%"]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @return Iterator<FileTag>
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('file_tag', new self());
    }

    /**
     * @inheritDoc
     * @return array{'name': string, 'color': string|null, 'emoji': string|null}
     */
    public function format(): array
    {
        return [
            'id' => $this->getIdAsInt(),
            'name' => $this->name,
            'color' => $this->color,
            'emoji' => $this->emoji,
        ];
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('file_tag');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('file_tag');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('file_tag');
    }

    /**
     * Gets the files in this tag
     *
     * @return Iterator<File>
     * @throws ForeignKeyFailedException
     * @throws UniqueFailedException
     * @throws InvalidQueryException
     */
    public function getFiles(): Iterator
    {
        $sql = <<<'SQL'
SELECT 
    id, creator_id, updated_by_id, created_at, last_updated_at, path, name, type 
FROM 
    file f
INNER JOIN 
    file_tag_file ftf
ON
    f.id = ftf.file_id
WHERE
    ftf.file_tag_id = :tag_id
SQL;

        try {
            return self::getPdo()->fetchIterator($sql, new File(), ['tag_id' => $this->id],
                [
                    'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}