<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\TransactionFailedException;
use App\Routing\Attributes\JinyaApi;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use PDOException;

/**
 *
 */
#[JinyaApi(createEnabled: false, readEnabled: true, updateEnabled: false, deleteEnabled: true)]
class BlogPost extends Utils\LoadableEntity
{
    public string $title;
    public string $slug;
    public ?int $headerImageId = null;
    public bool $public = false;
    public DateTime $createdAt;
    public int $creatorId;
    public ?int $categoryId = null;

    /**
     * @inheritDoc
     * @throws NoResultException
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

        try {
            return self::getPdo()->fetchIterator($sql, new self(), ['titleKeyword' => "%$keyword%", 'slugKeyword' => "%$keyword%"],
                [
                    'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'public' => new BooleanStrategy(1, 0),
                ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     *
     * @return Iterator<BlogPost>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findPublicPosts(): Iterator
    {
        $sql = 'SELECT * FROM blog_post WHERE public = true';

        try {
            return self::getPdo()->fetchIterator($sql, new self(), strategies: [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'public' => new BooleanStrategy(1, 0),
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @inheritDoc
     * @return Iterator<BlogPost>
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('blog_post', new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * @param string $slug
     * @return BlogPost|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     */
    public static function findBySlug(string $slug): BlogPost|null
    {
        $sql = 'SELECT * FROM blog_post WHERE slug = :slug';

        try {
            return self::getPdo()->fetchObject($sql, new self(), ['slug' => $slug], [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'public' => new BooleanStrategy(1, 0),
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * @param array<int, array<string, int|string>> $newSegments
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
            } elseif (array_key_exists('gallery', $newSegment)) {
                $query = 'INSERT INTO blog_post_segment (blog_post_id, gallery_id, position) VALUES (:blogPostId, :galleryId, :position)';
                $params = ['galleryId' => $newSegment['gallery']];
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
     * @return array<string, array<string, array<string, string|null>|int|string>|bool|int|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => 'int|string',
        'title' => 'string',
        'slug' => 'string',
        'headerImage' => 'array|null',
        'category' => 'array|null',
        'public' => 'bool',
        'created' => 'array',
    ])]
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
     * @return Artist|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getCreator(): Artist|null
    {
        return Artist::findById($this->creatorId);
    }

    /**
     * @return File|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getHeaderImage(): File|null
    {
        if ($this->headerImageId === null) {
            return null;
        }

        return File::findById($this->headerImageId);
    }

    /**
     * @return BlogCategory|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getCategory(): BlogCategory|null
    {
        if ($this->categoryId === null) {
            return null;
        }

        return BlogCategory::findById($this->categoryId);
    }

    /**
     * @return Iterator<BlogPostSegment>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getSegments(): Iterator
    {
        $sql = 'SELECT * FROM blog_post_segment WHERE blog_post_id = :postId ORDER BY position';

        try {
            return self::getPdo()->fetchIterator($sql, new BlogPostSegment(), ['postId' => $this->id]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }
}