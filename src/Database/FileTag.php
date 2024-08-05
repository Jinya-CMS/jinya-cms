<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Exception;
use Iterator;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Router\Extensions\Database\Attributes\Create;
use Jinya\Router\Extensions\Database\Attributes\Delete;
use Jinya\Router\Extensions\Database\Attributes\Find;
use Jinya\Router\Extensions\Database\Attributes\Update;
use JsonSerializable;

/**
 * This class contains information about the tags of files
 */
#[Table('file_tag')]
#[Find('/api/file-tag', new AuthorizationMiddleware(ROLE_READER))]
#[Create('/api/file-tag', new AuthorizationMiddleware(ROLE_WRITER))]
#[Update('/api/file-tag', new AuthorizationMiddleware(ROLE_WRITER))]
#[Delete('/api/file-tag', new AuthorizationMiddleware(ROLE_WRITER))]
class FileTag extends Entity implements JsonSerializable
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var string The name of the tag */
    #[Column]
    public string $name;

    /** @var string|null The color of the tag */
    #[Column]
    public ?string $color = null;

    /** @var string|null The emoji of the tag */
    #[Column]
    public ?string $emoji = null;

    /**
     * Finds the given tag by name
     *
     * @param string $name
     * @return FileTag|null
     */
    public static function findByName(string $name): ?FileTag
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'id',
                'name',
                'emoji',
                'color'
            ])
            ->where('name = :name', ['name' => $name]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * @return array{'name': string, 'color': string|null, 'emoji': string|null}
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->color,
            'emoji' => $this->emoji,
        ];
    }

    /**
     * Gets the files in this tag
     *
     * @return Iterator<File>
     * @throws Exception
     */
    public function getFiles(): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(File::getTableName() . ' AS f')
            ->cols([
                'f.id AS id',
                'f.creator_id AS creator_id',
                'f.updated_by_id AS updated_by_id',
                'f.created_at AS created_at',
                'f.last_updated_at AS last_updated_at',
                'f.path AS path',
                'f.name AS name',
                'f.type AS type'
            ])
            ->innerJoin(
                'file_tag_file AS ftf',
                'f.id = ftf.file_id AND ftf.file_tag_id = :tagId',
                ['tagId' => $this->id]
            );

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield File::fromArray($item);
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->format();
    }
}