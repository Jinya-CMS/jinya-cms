<?php

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Form\Message;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Form\MessageEvent;
use Jinya\Services\Base\BaseService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MessageService implements MessageServiceInterface
{
    /** @var BaseService */
    private BaseService $baseService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * MessageService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->baseService = new BaseService($entityManager, Message::class);
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the specified
     * @param int $id
     * @return Message
     */
    public function get(int $id): Message
    {
        $this->eventDispatcher->dispatch(new MessageEvent(null), MessageEvent::PRE_GET);

        $message = $this->entityManager->find(Message::class, $id);

        $this->eventDispatcher->dispatch(new MessageEvent($message), MessageEvent::POST_GET);

        return $message;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param string $formSlug
     * @param string $action
     * @return Message[]
     */
    public function getAll(
        int $offset = 0,
        int $count = 10,
        string $keyword = '',
        string $formSlug = '',
        string $action = ''
    ): array {
        $this->eventDispatcher->dispatch(new ListEvent($keyword, []), ListEvent::MESSAGES_PRE_GET_ALL);

        $items = $this->getFilteredQueryBuilder($keyword, $formSlug, $action)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('message')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            new ListEvent($keyword, $items),
            ListEvent::MESSAGES_POST_GET_ALL
        );

        return $items;
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @param string $formSlug
     * @param string $action
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword, string $formSlug = '', string $action = ''): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->from(Message::class, 'message')
            ->where('message.subject LIKE :keyword')
            ->orWhere('message.content LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");

        if ('' !== $formSlug) {
            $queryBuilder
                ->andWhere('form.slug = :slug')
                ->join('message.form', 'form')
                ->setParameter('slug', $formSlug)
                ->andWhere('message.isArchived = 0')
                ->andWhere('message.isDeleted = 0')
                ->andWhere('message.spam = 0');
        }

        if ('spam' === $action) {
            $queryBuilder->andWhere('message.isArchived = 0');
            $queryBuilder->andWhere('message.isDeleted = 0');
            $queryBuilder->andWhere('message.spam = 1');
        } elseif ('deleted' === $action) {
            $queryBuilder->andWhere('message.isArchived = 0');
            $queryBuilder->andWhere('message.isDeleted = 1');
            $queryBuilder->andWhere('message.spam = 0');
        } elseif ('archived' === $action) {
            $queryBuilder->andWhere('message.isArchived = 1');
            $queryBuilder->andWhere('message.isDeleted = 0');
            $queryBuilder->andWhere('message.spam = 0');
        } elseif ('all' === $action) {
            $queryBuilder->andWhere('message.isArchived = 0');
            $queryBuilder->andWhere('message.isDeleted = 0');
            $queryBuilder->andWhere('message.spam = 0');
        }

        return $queryBuilder;
    }

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @param string $formSlug
     * @param string $action
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll(string $keyword = '', string $formSlug = '', string $action = ''): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::MESSAGES_PRE_COUNT);

        $count = $this->getFilteredQueryBuilder($keyword, $formSlug, $action)
            ->select('COUNT(message)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::MESSAGES_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given
     * @param Message $message
     * @return Message @see Message
     */
    public function saveOrUpdate(Message $message): Message
    {
        $pre = $this->eventDispatcher->dispatch(new MessageEvent($message), MessageEvent::PRE_SAVE);

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($message);
            $this->eventDispatcher->dispatch(new MessageEvent($message), MessageEvent::POST_SAVE);
        }

        return $message;
    }

    /**
     * Deletes the given @param Message $message
     * @see Message
     */
    public function delete(Message $message): void
    {
        $pre = $this->eventDispatcher->dispatch(new MessageEvent($message), MessageEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $this->baseService->delete($message);
            $this->eventDispatcher->dispatch(new MessageEvent($message), MessageEvent::POST_DELETE);
        }
    }
}
