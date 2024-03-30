<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use DateTime;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Extensions\Database\Attributes\ApiIgnore;
use Jinya\Router\Extensions\Database\Attributes\Create;
use Jinya\Router\Extensions\Database\Attributes\Delete;
use Jinya\Router\Extensions\Database\Attributes\Find;
use Jinya\Router\Extensions\Database\Attributes\Update;

/**
 * This class contains information about files stored with the media manager in Jinya CMS
 */
#[Table('file')]
#[Find('/api/file', new AuthorizationMiddleware(ROLE_READER))]
#[Create('/api/file', new AuthorizationMiddleware(ROLE_WRITER))]
#[Update('/api/file', new AuthorizationMiddleware(ROLE_WRITER))]
#[Delete('/api/file', new AuthorizationMiddleware(ROLE_WRITER))]
class File extends Entity
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var int The ID of the files creator */
    #[Column(sqlName: 'creator_id')]
    #[ApiIgnore]
    public int $creatorId;

    /** @var int The ID of the artist who last touched the file */
    #[Column(sqlName: 'updated_by_id')]
    #[ApiIgnore]
    public int $updatedById;

    /** @var DateTime The time the file was created at */
    #[Column(sqlName: 'created_at')]
    #[ApiIgnore]
    public DateTime $createdAt;

    /** @var DateTime The time the file was last updated at */
    #[Column(sqlName: 'last_updated_at')]
    #[ApiIgnore]
    public DateTime $lastUpdatedAt;

    /** @var string The path where the file is stored, this path is relative to the web root directory */
    #[Column]
    #[ApiIgnore]
    public string $path = '';

    /** @var string The name of the file */
    #[Column]
    public string $name = '';

    /** @var string The type of the file */
    #[Column]
    #[ApiIgnore]
    public string $type = '';

    /** @var string[] The tags of the file */
    public ?array $tags = null;

    /**
     * Gets the uploading chunks
     *
     * @return Iterator<UploadingFileChunk>
     * @throws Exception
     */
    public function getUploadChunks(): Iterator
    {
        return UploadingFileChunk::findByFile($this->id);
    }

    /**
     * Creates the current file
     *
     * @return void
     * @throws NotNullViolationException
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = CurrentUser::$currentUser->id;

        parent::create();

        $this->setTagsInDatabase();
    }

    /**
     * @return void
     */
    public function setTagsInDatabase(): void
    {
        $rows = [];
        foreach ($this->tags as $tag) {
            $tag = FileTag::findByName($tag);
            if ($tag) {
                $rows[] = [
                    'file_id' => $this->id,
                    'file_tag_id' => $tag->id
                ];
            }
        }

        $query = self::getQueryBuilder()
            ->newDelete()
            ->from('file_tag_file')
            ->where('file_id = :fileId', ['fileId' => $this->id]);
        self::executeQuery($query);

        if (!empty($rows)) {
            $query = self::getQueryBuilder()
                ->newInsert()
                ->into('file_tag_file')
                ->addRows($rows);
            self::executeQuery($query);
        }
    }

    /**
     * Updates the current file
     *
     * @return void
     * @throws NotNullViolationException
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = CurrentUser::$currentUser->id;

        parent::update();
        $this->setTagsInDatabase();
    }

    /**
     * Formats the file into an array
     *
     * @return array{id: int, name: string, type: string, path: string, tags: array<array{name: string, color: string|null, emoji: string|null}>, created: array{by: array{artistName: string|null, email: string|null, profilePicture: string|null}, at: non-falsy-string}, updated: array{by: array{artistName: string|null, email: string|null, profilePicture: string|null}, at: non-falsy-string}}
     * @throws Exception
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
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'path' => $this->path,
            'tags' => array_map(static fn (FileTag $tag) => $tag->format(), iterator_to_array($this->getTags())),
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
     */
    public function getCreator(): ?Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist that last updated this file
     *
     * @return Artist|null
     *
     */
    public function getUpdatedBy(): ?Artist
    {
        return Artist::findById($this->updatedById);
    }

    /**
     * Gets all tags for the given file
     *
     * @return Iterator<FileTag>
     * @throws Exception
     */
    public function getTags(): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(FileTag::getTableName() . ' AS ft')
            ->cols([
                'ft.id AS id',
                'ft.name AS name',
                'ft.color AS color',
                'ft.emoji AS emoji'
            ])
            ->innerJoin(
                'file_tag_file AS ftf',
                'ft.id = ftf.file_tag_id AND ftf.file_id = :fileId',
                ['fileId' => $this->id]
            );

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield FileTag::fromArray($item);
        }
    }
}
