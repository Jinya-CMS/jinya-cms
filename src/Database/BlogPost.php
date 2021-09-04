<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Utils\FormattableEntityInterface;
use DateTime;
use Iterator;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

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
        $sql = 'SELECT * FROM blog_post_segment WHERE blog_post_id = :postId';
        /** @var array<array> */
        $result = self::executeStatement($sql, ['postId' => $this->id]);

        return self::hydrateMultipleResults($result, new BlogPostSegment());
    }
}