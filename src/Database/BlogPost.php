<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\TransactionFailedException;
use App\Database\Utils\FormattableEntityInterface;
use DateTime;
use Iterator;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use PDOException;

class BlogPost extends Utils\LoadableEntity implements FormattableEntityInterface
{
    public string $title;
    public string $slug;
    public ?int $headerImageId = null;
    public bool $public = false;
    public DateTime $createdAt;
    public int $creatorId;
    public ?int $categoryId = null;

    /**
     * @param array<array{html: string, file: integer, gallery: integer, link: string}> $newSegments
     * @throws TransactionFailedException
     */
    public function batchReplaceSegments(array $newSegments): void
    {
        $pdo = self::getPdo();
        $begin = $pdo->beginTransaction();
        if (!$begin) {
            throw new TransactionFailedException('Transaction could not be initialized');
        }

        $newSegmentStatements = [];

        foreach ($newSegments as $idx => $newSegment) {
            if (array_key_exists('file', $newSegment)) {
                $query = 'INSERT INTO blog_post_segment (blog_post_id, file_id, position, link) VALUES (:blogPostId, :fileId, :position, :link)';
                $params = ['fileId' => $newSegment['file'], 'link' => $newSegment['link'] ?? null];
                /** @phpstan-ignore-next-line */
            } else if (array_key_exists('gallery', $newSegment)) {
                $query = 'INSERT INTO blog_post_segment (blog_post_id, gallery_id, position) VALUES (:blogPostId, :galleryId, :position)';
                $params = ['galleryId' => $newSegment['gallery']];
                /** @phpstan-ignore-next-line */
            } else {
                $query = 'INSERT INTO blog_post_segment (blog_post_id, html, position) VALUES (:blogPostId, :html, :position)';
                $params = ['html' => $newSegment['html'] ?? ''];
            }

            $params['position'] = $idx;
            $params['blogPostId'] = $this->id;
            $newSegmentStatements[] = [
                'query' => $query,
                'params' => $params,
            ];
        }

        try {
            $pdo->prepare('DELETE FROM blog_post_segment WHERE blog_post_id = :id')->execute(['id' => $this->id]);
            foreach ($newSegmentStatements as $newSegment) {
                $pdo->prepare($newSegment['query'])->execute($newSegment['params']);
            }

            $pdo->commit();
        } catch (PDOException $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    /**
     * @inheritDoc
     */
    public static function findById(int $id): BlogPost|null
    {
        return self::fetchSingleById('blog_post', $id, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogPost>
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT * FROM blog_post WHERE title LIKE :titleKeyword OR slug LIKE :slugKeyword';
        /** @var array<array> */
        $result = self::executeStatement($sql, ['titleKeyword' => "%$keyword%", 'slugKeyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogPost>
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('blog_post', new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @param string $slug
     * @return BlogPost|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findBySlug(string $slug): BlogPost|null
    {
        $sql = 'SELECT * FROM blog_post WHERE slug = :slug';
        /** @var array<array{id: int, title: string, slug: string, header_image_id: int, public: int, created_at: string, creator_id: int, category_id: int}> */
        $result = self::executeStatement($sql, ['slug' => $slug]);
        if (count($result) !== 1) {
            return null;
        }

        return self::hydrateSingleResult($result[0], new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser?->id;

        $this->internalCreate('blog_post', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('blog_post');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('blog_post', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @inheritDoc
     * @psalm-suppress MoreSpecificReturnType
     * @return array{id: int, title: string, slug: string, headerImage: array, public: bool, created: array, category: array}
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function format(): array
    {
        $creator = $this->getCreator();
        $headerImage = $this->getHeaderImage();
        if ($headerImage !== null) {
            $headerFormat = [
                'id' => $headerImage->id,
                'name' => $headerImage->name,
                'path' => $headerImage->path,
            ];
        } else {
            $headerFormat = null;
        }
        $category = $this->getCategory();
        if ($category !== null) {
            $categoryFormat = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        } else {
            $categoryFormat = null;
        }

        /**
         * @psalm-suppress LessSpecificReturnStatement
         * @phpstan-ignore-next-line
         */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'headerImage' => $headerFormat,
            'category' => $categoryFormat,
            'public' => $this->public,
            'created' => [
                'at' => $this->createdAt->format(DATE_ATOM),
                'by' => [
                    'artistName' => $creator?->artistName,
                    'email' => $creator?->email,
                    'profilePicture' => $creator?->profilePicture,
                ],
            ],
        ];
    }

    /**
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     */
    public function getCategory(): BlogCategory|null
    {
        if ($this->categoryId === null) {
            return null;
        }

        return BlogCategory::findById($this->categoryId);
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\InvalidQueryException
     */
    public function getCreator(): Artist|null
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\InvalidQueryException
     */
    public function getHeaderImage(): File|null
    {
        if ($this->headerImageId === null) {
            return null;
        }

        return File::findById($this->headerImageId);
    }

    /**
     * @return Iterator<BlogPostSegment>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getSegments(): Iterator
    {
        $sql = 'SELECT * FROM blog_post_segment WHERE blog_post_id = :postId ORDER BY position';
        /** @var array<array> */
        $result = self::executeStatement($sql, ['postId' => $this->id]);

        return self::hydrateMultipleResults($result, new BlogPostSegment());
    }
}