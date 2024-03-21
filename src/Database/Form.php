<?php

namespace App\Database;

use App\Authentication\CurrentUser;
use App\Database\Strategies\JsonStrategy;
use App\Routing\Attributes\JinyaApi;
use App\Routing\Attributes\JinyaApiField;
use DateTime;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

/**
 * This class contains a form, forms allow artists to receive feedback from their site visitors
 */
#[JinyaApi]
class Form extends Utils\LoadableEntity
{
    /** @var int The ID of the creator of the form */
    #[JinyaApiField(ignore: true)]
    public int $creatorId;
    /** @var int The ID of the artist who last touched the form */
    #[JinyaApiField(ignore: true)]
    public int $updatedById;
    /** @var DateTime The time the form was created at */
    #[JinyaApiField(ignore: true)]
    public DateTime $createdAt;
    /** @var DateTime The time the form was last updated at */
    #[JinyaApiField(ignore: true)]
    public DateTime $lastUpdatedAt;

    /** @var string The title of the form */
    #[JinyaApiField(required: true)]
    public string $title;
    /** @var string The description of the form, may contain HTML */
    #[JinyaApiField]
    public string $description = '';
    /** @var string The email address filled out forms should be mailed to */
    #[JinyaApiField(required: true)]
    public string $toAddress;

    /**
     * Finds all forms matching the keyword
     *
     * @param string $keyword
     * @return Iterator<Form>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = 'SELECT id, creator_id, updated_by_id, created_at, last_updated_at, to_address, title, description FROM form f WHERE f.title LIKE :titleKeyword OR description LIKE :descKeyword';

        try {
            return self::getPdo()->fetchIterator(
                $sql,
                new self(),
                ['descKeyword' => "%$keyword%", 'titleKeyword' => "%$keyword%"],
                [
                    'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                    'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                ]
            );
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Finds all forms
     *
     * @return Iterator<Form>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findAll(): Iterator
    {
        return self::fetchAllIterator(
            'form',
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets all form items in the form
     *
     * @return Iterator<FormItem>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getItems(): Iterator
    {
        $sql = 'SELECT id, form_id, type, options, spam_filter, label, help_text, position, is_from_address, is_subject, is_required, placeholder FROM form_item WHERE form_id = :id ORDER BY position';

        try {
            return self::getPdo()->fetchIterator($sql, new FormItem(), ['id' => $this->id], [
                'spamFilter' => new JsonStrategy(),
                'options' => new JsonStrategy(),
            ]);
        } catch (InvalidQueryException $exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Creates the current form
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

        $this->internalCreate(
            'form',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Deletes the current form
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('form');
    }

    /**
     * Updates the current form
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
            'form',
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Formats the form into an array
     *
     * @return array<string, array<string, array<string, string|null>|string>|int|string>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape([
        'id' => 'int',
        'description' => 'string',
        'title' => 'string',
        'toAddress' => 'string',
        'created' => 'array',
        'updated' => 'array'
    ])] public function format(): array
    {
        $creator = $this->getCreator();
        $updatedBy = $this->getUpdatedBy();

        return [
            'id' => $this->getIdAsInt(),
            'description' => $this->description,
            'title' => $this->title,
            'toAddress' => $this->toAddress,
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
     * Gets the creator of this form
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
     * Finds the form with the given ID
     *
     * @param int $id
     * @return Form|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findById(int $id): ?Form
    {
        return self::fetchSingleById(
            'form',
            $id,
            new self(),
            [
                'createdAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
                'lastUpdatedAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
            ]
        );
    }

    /**
     * Gets the artist that last updated this form
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
}
