<?php

namespace App\Database;

use App\Database\Utils\CountedResult;
use DateTime;
use Exception;
use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

class Message extends Utils\LoadableEntity implements Utils\FormattableEntityInterface
{
    public const SPAM = 'spam';
    public const DELETED = 'deleted';
    public const ARCHIVED = 'archived';
    public int $formId;
    public string $subject;
    public string $content;
    public string $fromAddress;
    public string $targetAddress;
    public ?string $answer = null;
    public DateTime $sendAt;
    public bool $isArchived = false;
    public bool $isDeleted = false;
    public bool $isRead = false;
    public bool $spam = false;

    /**
     * @inheritDoc
     * @return Message
     */
    public static function findById(int $id)
    {
        return self::fetchSingleById('message', $id, new self(), [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('message')
            ->where(['content LIKE :keyword', 'subject LIKE :keyword'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['keyword' => "%$keyword%"]);

        return self::hydrateMultipleResults($result, new self(), [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * Gets the content of the given inbox
     *
     * @param string $inbox
     * @param string $keyword
     * @param int $offset
     * @param int $count
     * @return CountedResult
     */
    public static function findByInboxAndKeyword(string $inbox, string $keyword, int $offset, int $count): CountedResult
    {
        $where = [];
        $parameter = [];

        if ($keyword !== '') {
            $where[] = '(content LIKE :keyword OR subject LIKE :keyword)';
            $parameter['keyword'] = "%$keyword%";
        }
        switch ($inbox) {
            case self::ARCHIVED:
                $where[] = 'is_archived = 1';
                break;
            case self::DELETED:
                $where[] = 'is_deleted = 1';
                break;
            default:
                $where[] = 'spam = 1';
                break;
        }

        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from('message')
            ->where($where, PredicateSet::OP_AND)
            ->limit($count)
            ->offset($offset);

        $countData = $sql
            ->select('message')
            ->columns(['id'])
            ->where($where, PredicateSet::OP_AND);

        $items = self::executeStatement($sql->prepareStatementForSqlObject($select), $parameter);
        $itemCount = self::executeStatement($sql->prepareStatementForSqlObject($countData), $parameter);

        $countedResult = new CountedResult();
        $countedResult->items = self::hydrateMultipleResults($items, new self(), [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
        $countedResult->totalCount = $itemCount->count();

        return $countedResult;
    }

    /**
     * Gets all messages in the given form
     *
     * @param int $formId
     * @param string $keyword
     * @param int $offset
     * @param int $count
     * @return CountedResult
     */
    public static function findByFormAndKeyword(?int $formId, string $keyword, int $offset, int $count): CountedResult
    {
        $sql = self::getSql();
        $where = ['(content LIKE :keyword OR subject LIKE :keyword)', 'spam = 0', 'is_deleted = 0', 'is_archived = 0'];
        $parameters = ['keyword' => "%$keyword%"];
        if ($formId !== null) {
            $where[] = 'form_id = :formId';
            $parameters['formId'] = $formId;
        }

        $select = $sql
            ->select()
            ->from('message')
            ->offset($offset)
            ->limit($count)
            ->where($where, PredicateSet::OP_AND);

        $countData = $sql
            ->select('message')
            ->columns(['id'])
            ->where($where, PredicateSet::OP_AND);

        $items = self::executeStatement($sql->prepareStatementForSqlObject($select), $parameters);
        $itemCount = self::executeStatement($sql->prepareStatementForSqlObject($countData), $parameters);

        $countedResult = new CountedResult();
        $countedResult->items = self::hydrateMultipleResults($items, new self(), [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
        $countedResult->totalCount = $itemCount->count();

        return $countedResult;
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        return self::fetchArray('message', new self(), [
            'sendAt' => new DateTimeFormatterStrategy(),
        ]);
    }

    /**
     * Moves the given message in the trash
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function moveToTrash(): void
    {
        $this->isArchived = false;
        $this->spam = false;
        $this->isDeleted = true;
        $this->update();
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('message', [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * Moves the given message in the archive
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function moveToArchive(): void
    {
        $this->isArchived = true;
        $this->spam = false;
        $this->isDeleted = false;
        $this->update();
    }

    /**
     * Moves the given message in the archive
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function moveToInbox(): void
    {
        $this->isArchived = false;
        $this->spam = false;
        $this->isDeleted = false;
        $this->update();
    }

    /**
     * Marks the given message as spam
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function markAsSpam(): void
    {
        $this->isArchived = false;
        $this->spam = true;
        $this->isDeleted = false;
        $this->update();
    }

    /**
     * Marks the given message as read
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function markAsRead(): void
    {
        $this->isRead = true;
        $this->update();
    }

    /**
     * Marks the given message as read
     *
     * @throws Exceptions\UniqueFailedException
     */
    public function markAsNotRead(): void
    {
        $this->isRead = false;
        $this->update();
    }

    public function format(): array
    {
        return [
            'subject' => $this->subject,
            'content' => $this->content,
            'fromAddress' => $this->fromAddress,
            'targetAddress' => $this->targetAddress,
            'sendAt' => $this->sendAt->format(DATE_ATOM),
            'archived' => $this->isArchived,
            'trash' => $this->isDeleted,
            'read' => $this->isRead,
            'spam' => $this->spam,
            'id' => $this->getIdAsInt(),
            'answer' => $this->answer,
        ];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('message', [
            'sendAt' => new DateTimeFormatterStrategy(self::MYSQL_DATE_FORMAT),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('message');
    }
}