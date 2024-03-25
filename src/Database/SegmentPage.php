<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\TransactionFailedException;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use PDOException;

/**
 * This class contains information about segment pages
 */
#[Table('segment_page')]
class SegmentPage extends Entity
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var int The ID of the artist who created the segment page */
    #[Column(sqlName: 'creator_id')]
    public int $creatorId;

    /** @var int The ID of the artist who last updated the segment page */
    #[Column(sqlName: 'updated_by_id')]
    public int $updatedById;

    /** @var DateTime The time the segment page was created */
    #[Column(sqlName: 'created_at')]
    public DateTime $createdAt;

    /** @var DateTime The time the segment page was last updated */
    #[Column(sqlName: 'last_updated_at')]
    public DateTime $lastUpdatedAt;

    /** @var string The name of the segment page */
    #[Column]
    public string $name;

    /**
     * Creates the current segment page
     *
     * @return void
     * @throws NotNullViolationException
     */
    public function create(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser->id;

        parent::create();
    }


    /**
     * Updates the current segment page
     *
     * @return void
     * @throws NotNullViolationException
     */
    public function update(): void
    {
        $this->lastUpdatedAt = new DateTime();
        $this->updatedById = (int)CurrentUser::$currentUser->id;

        parent::update();
    }

    /**
     * Formats the current segment_page
     *
     * @return array<string, array<string, array<string, string|null>|string>|int|string>
     */
    #[ArrayShape([
        'id' => 'int',
        'name' => 'string',
        'segmentCount' => 'int',
        'created' => 'array',
        'updated' => 'array'
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'segmentCount' => iterator_count($this->getSegments()),
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
     * Gets the creator
     *
     * @return Artist|null
     */
    public function getCreator(): ?Artist
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * Gets the artist who last updated the segment_page
     *
     * @return Artist|null
     */
    public function getUpdatedBy(): ?Artist
    {
        return Artist::findById($this->updatedById);
    }

    /**
     * Gets all segments of the current page
     *
     * @return Iterator<Segment>
     */
    public function getSegments(): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(Segment::getTableName())
            ->cols([
                'id',
                'page_id',
                'gallery_id',
                'file_id',
                'position',
                'html',
                'action',
                'script',
                'target'
            ])
            ->where('page_id = :pageId', ['pageId' => $this->id])
            ->orderBy(['position']);
        $data = self::executeQuery($query);

        foreach ($data as $item) {
            yield Segment::fromArray($item);
        }
    }

    /**
     * Replaces all blog post segments with the new segments
     *
     * @param array<int, array<string, int|string>> $newSegments The new segments
     * @throws TransactionFailedException
     */
    public function replaceSegments(array $newSegments): void
    {
        $pdo = self::getPdo();
        $begin = $pdo->beginTransaction();
        if (!$begin) {
            throw new TransactionFailedException('Transaction could not be initialized');
        }

        $newSegmentQueries = [];

        foreach ($newSegments as $idx => $newSegment) {
            $row = [
                'position' => $idx,
                'page_id' => $this->id
            ];

            if (array_key_exists('file', $newSegment)) {
                $row['target'] = $newSegment['link'] ?? null;
                $row['script'] = $newSegment['script'] ?? null;
                $row['action'] = 'none';
                if ($row['target']) {
                    $row['action'] = 'link';
                } elseif ($row['script']) {
                    $row['action'] = 'script';
                }

                $row['file_id'] = $newSegment['file'];
            } elseif (array_key_exists('gallery', $newSegment)) {
                $row['gallery_id'] = $newSegment['gallery'];
            } else {
                $row['html'] = $newSegment['html'];
            }

            $newSegmentQueries[] = self::getQueryBuilder()
                ->newInsert()
                ->into(Segment::getTableName())
                ->addRow($row);
        }

        try {
            $query = self::getQueryBuilder()
                ->newDelete()
                ->from(Segment::getTableName())
                ->where('page_id = :pageId', ['pageId' => $this->id]);

            self::executeQuery($query);
            foreach ($newSegmentQueries as $newItem) {
                self::executeQuery($newItem);
            }

            $pdo->commit();
        } catch (PDOException $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }
}
