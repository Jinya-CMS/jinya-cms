<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Converter\BooleanConverter;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\TransactionFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Logging\Logger;
use DateTime;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Id;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Entity;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use League\Uri\Http as HttpUri;
use PDOException;

/**
 * This class contains the information of a blog post
 */
#[Table('blog_post')]
class BlogPost extends Entity
{
    #[Id]
    #[Column(autogenerated: true)]
    public int $id;

    /** @var string The title of the blog post */
    #[Column]
    public string $title;

    /** @var string The slug of the blog post */
    #[Column]
    public string $slug;

    /** @var int|null The ID of the file used as header image */
    #[Column(sqlName: 'header_image_id')]
    public ?int $headerImageId = null;

    /** @var bool Whether the post is publicly available */
    #[Column]
    #[BooleanConverter]
    public bool $public = false;

    /** @var DateTime The time the post was created */
    #[Column(sqlName: 'created_at')]
    public DateTime $createdAt;

    /** @var int The ID of the artist who created the post */
    #[Column(sqlName: 'creator_id')]
    public int $creatorId;

    /** @var int|null The ID of the category this post belongs to */
    #[Column(sqlName: 'category_id')]
    public ?int $categoryId = null;

    /**
     * Finds all public blog posts
     *
     * @return Iterator<BlogPost>
     */
    public static function findPublicPosts(): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols(['*'])
            ->where('public = true');
        $data = self::executeQuery($query);

        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Finds the blog post by the given slug
     *
     * @param string $slug The slug to search for
     * @return BlogPost|null
     */
    public static function findBySlug(string $slug): BlogPost|null
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols(['*'])
            ->where('slug = :slug', ['slug' => $slug]);
        $data = self::executeQuery($query);

        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
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
            if (array_key_exists('file', $newSegment)) {
                $newSegmentQueries[] = self::getQueryBuilder()
                    ->newInsert()
                    ->into(BlogPostSegment::getTableName())
                    ->addRow([
                        'position' => $idx,
                        'file_id' => $newSegment['file'],
                        'link' => $newSegment['link'] ?? null,
                        'blog_post_id' => $this->id
                    ]);
            } elseif (array_key_exists('gallery', $newSegment)) {
                $newSegmentQueries[] = self::getQueryBuilder()
                    ->newInsert()
                    ->into(BlogPostSegment::getTableName())
                    ->addRow([
                        'position' => $idx,
                        'gallery_id' => $newSegment['gallery'],
                        'blog_post_id' => $this->id
                    ]);
            } else {
                $newSegmentQueries[] = self::getQueryBuilder()
                    ->newInsert()
                    ->into(BlogPostSegment::getTableName())
                    ->addRow([
                        'position' => $idx,
                        'html' => $newSegment['html'] ?? '',
                        'blog_post_id' => $this->id
                    ]);
            }
        }

        try {
            $query = self::getQueryBuilder()
                ->newDelete()
                ->from(BlogPostSegment::getTableName())
                ->where('blog_post_id = :blogPostId', ['blogPostId' => $this->id]);

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

    /**
     * Creates the current blog post
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws NotNullViolationException
     */
    public function create(): void
    {
        $this->createdAt = new DateTime();
        $this->creatorId = (int)CurrentUser::$currentUser?->id;

        parent::create();

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
            $url = HttpUri::fromServer($_SERVER);
            $body = [
                'post' => $this->format(),
                'url' => $url->getScheme() . '://' . $url->getHost() . '/' . $this->createdAt->format(
                    'Y/m/d'
                ) . "/$this->slug",
            ];

            try {
                $category = $this->getCategory();
                $url = HttpUri::new($category?->webhookUrl);
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
     */
    public function getCategory(): BlogCategory|null
    {
        if ($this->categoryId === null) {
            return null;
        }

        return BlogCategory::findById($this->categoryId);
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
     * Updates the current blog post
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws NotNullViolationException
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        /** @var BlogPost $oldState */
        $oldState = self::findById($this->id);
        $wasPublic = $oldState->public;
        parent::update();
        if ($wasPublic === false && ($this->public ?? false)) {
            $this->executeHook();
        }
    }

    /**
     * Gets all segments of the current post
     *
     * @return Iterator<BlogPostSegment>
     */
    public function getSegments(): Iterator
    {
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(BlogPostSegment::getTableName())
            ->cols(['*'])
            ->where('blog_post_id = :postId', ['postId' => $this->id])
            ->orderBy(['position']);
        $data = self::executeQuery($query);

        foreach ($data as $item) {
            yield BlogPostSegment::fromArray($item);
        }
    }
}
