<?php

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Form\Message;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Form\MessageEvent;
use Jinya\Services\Base\BaseService;
use Jinya\Services\Base\BaseSlugEntityService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MessageService implements MessageServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

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
     * Gets the specified @param string $slug
     * @return Message
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see Message by slug
     */
    public function get(string $slug): Message
    {
        $this->eventDispatcher->dispatch(MessageEvent::PRE_GET, new MessageEvent(null));

        $message = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(MessageEvent::POST_GET, new MessageEvent($message));

        return $message;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param int $formId
     * @return Message[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', int $formId = -1): array
    {
        $this->eventDispatcher->dispatch(new ListEvent($offset, $count, $keyword, []), ListEvent::MESSAGES_PRE_GET_ALL);

        $items = $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('message')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            new ListEvent($offset, $count, $keyword, $items),
            ListEvent::MESSAGES_POST_GET_ALL
        );

        return $items;
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @param int $formId
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword, int $formId = -1): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Message::class, 'message')
            ->where('message.title LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @param int $formId
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = '', int $formId = -1): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::MESSAGES_PRE_COUNT);

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(message)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::MESSAGES_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given @param Message $message
     * @return Message
     * @throws EmptySlugException
     * @see Message
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
