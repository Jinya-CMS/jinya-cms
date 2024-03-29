<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\TransactionFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Logging\Logger;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use DateTime;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;
use League\Uri\Http as HttpUri;
use PDOException;

/**
 * This class contains the information of a blog post
 */
#[JinyaApi]
class BlogPost extends Utils\LoadableEntity
{
    /** @var string The title of the blog post */
    #[JinyaApiField(required: true)]
    public string $title;
    /** @var string The slug of the blog post */
    #[JinyaApiField(required: true)]
    public string $slug;
    /** @var int|null The ID of the file used as header image */
    #[JinyaApiField]
    public ?int $headerImageId = null;
    /** @var bool Whether the post is publicly available */
    #[JinyaApiField]
    public bool $public = false;
    /** @var DateTime The time the post was created */
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    /** @var int The ID of the artist who created the post */
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    /** @var int|null The ID of the category this post belongs to */
    #[JinyaApiField]
    public ?int $categoryId = null;

    /**
     * Finds the blog posts by the given keyword
     *
     * @param string $keyword
     * @return Iterator<BlogPost>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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
     * Finds all public blog posts
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
     * Finds all blog posts. It is not differentiated between public and private posts.
     *
     * @return Iterator<BlogPost>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator('blog_post', new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * Finds the blog post by the given slug
     *
     * @param string $slug The slug to search for
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
     * Replaces all blog post segments with the new segments
     *
     * @param array<int, array<string, int|string>> $newSegments The new segments
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
     * Creates the current blog post
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function create(): void
    {
        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser?->id;

        $this->internalCreate('blog_post', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);

        $this->executeHook();
    }

    /**
     * Executes the webhook defined in the category
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    private function executeHook(): void
    {
        $category = $this->getCategory();
        if ($this->public && $category !== null && $category->webhookEnabled && $category->webhookUrl !== null) {
            $logger = Logger::getLogger();
            $url = HttpUri::createFromServer($_SERVER);
            $body = [
                'post' => $this->format(),
                'url' => $url->getScheme() . '://' . $url->getHost() . '/' . $this->createdAt->format('Y/m/d') . "/$this->slug",
            ];

            try {
                $category = $this->getCategory();
                $url = HttpUri::createFromString($category?->webhookUrl);
                $postBody = json_encode($body, JSON_THROW_ON_ERROR);

                $scheme = $url->getScheme() === 'https' ? 'ssl://' : '';
                $host = $scheme . $url->getHost();
                $port = $url->getPort() ?: $this->getDefaultPort($url->getScheme());
                $requestUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                $request = 'POST ' . $category?->webhookUrl . " HTTP/1.1\r\n";
                $request .= 'Host: ' . $url->getHost() . "\r\n";
                $request .= "Content-Type: application/json\r\n";
                $request .= 'Content-Length: ' . strlen($postBody) . "\r\n";
                $request .= "Connection: Close\r\n";
                $request .= 'Referer: ' . $requestUrl . "\r\n";
                $request .= "\r\n";
                $request .= $postBody;

                $errno = null;
                $errstr = null;
                $socket = @fsockopen($host, $port, $errno, $errstr, 5);

                if (!$socket) {
                    $logger->warning("Failed to open socket: $errno, $errstr");
                    return;
                }

                fwrite($socket, $request);
                fclose($socket);
            } catch (Exception $exception) {
                $logger->warning('Failed to send webhook: ' . $exception->getMessage());
            }
        }
    }

    /**
     * The category of the post, if the post has no category, null is returned
     *
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
     * Finds the blog post with the given id
     *
     * @param int $id
     * @return BlogPost|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public static function findById(int $id): BlogPost|null
    {
        return self::fetchSingleById('blog_post', $id, new self(), [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
    }

    /**
     * Formats the blog post into an array
     *
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
     * Gets the creator of the blog post
     *
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
     * Gets the header image file of the blog post
     *
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
     * Returns the default port for the given scheme
     *
     * @param string $scheme The scheme to check for, if the scheme is https the return value is 443 otherwise 80
     * @return int
     */
    private function getDefaultPort(string $scheme): int
    {
        if ($scheme === 'https') {
            return 443;
        }

        return 80;
    }

    /**
     * Deletes the current blog post
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function delete(): void
    {
        $this->internalDelete('blog_post');
    }

    /**
     * Updates the current blog post
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        /** @var BlogPost $oldState */
        $oldState = self::findById($this->getIdAsInt());
        $wasPublic = $oldState->public;
        $this->internalUpdate('blog_post', [
            'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            'public' => new BooleanStrategy(1, 0),
        ]);
        if ($wasPublic === false && ($this->public ?? false)) {
            $this->executeHook();
        }
    }

    /**
     * Gets all segments of the current post
     *
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
