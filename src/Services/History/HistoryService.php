<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 08:09
 */

namespace Jinya\Services\History;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Framework\Events\History\HistoryEvent;
use Jinya\Framework\Events\History\HistoryRevertEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function array_filter;
use function method_exists;

class HistoryService implements HistoryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * HistoryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getHistory(string $class, int $id): array
    {
        $this->eventDispatcher->dispatch(new HistoryEvent($class, $id, []), HistoryEvent::PRE_GET);
        /** @noinspection NullPointerExceptionInspection */
        $history = $this->entityManager->find($this->getFullClassName($class), $id)->getHistory();
        $this->eventDispatcher->dispatch(new HistoryEvent($class, $id, $history), HistoryEvent::POST_GET);

        return $history;
    }

    /**
     * @param string $class
     * @return string
     */
    protected function getFullClassName(string $class): string
    {
        return "Jinya\\Entity\\$class";
    }

    /**
     * {@inheritdoc}
     */
    public function clearHistory(string $class, int $id): void
    {
        $entity = $this->entityManager->find($this->getFullClassName($class), $id);

        if (!$entity) {
            return;
        }

        $pre = $this->eventDispatcher->dispatch(
            new HistoryEvent($class, $id, $entity->getHistory()),
            HistoryEvent::PRE_CLEAR
        );

        if (!$pre->isCancel()) {
            $entity->setHistory([]);

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new HistoryEvent($class, $id, []), HistoryEvent::POST_CLEAR);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revert(string $class, int $id, string $field, string $timestamp): void
    {
        $entity = $this->entityManager->find($this->getFullClassName($class), $id);

        if (!$entity) {
            return;
        }

        if (method_exists($entity, "set$field")) {
            $history = $entity->getHistory();
            /** @var array $entry */
            $entry = array_filter($history, static function (array $item) use ($timestamp) {
                return $item['timestamp'] === $timestamp;
            })[0];

            if (array_key_exists($field, $entry['entry'])) {
                $pre = $this->eventDispatcher->dispatch(
                    new HistoryRevertEvent($class, $id, $field, $timestamp, $entry),
                    HistoryRevertEvent::PRE_REVERT
                );
                if (!$pre->isCancel()) {
                    $revertedValue = $entry['entry'][$field][1];
                    $setter = "set$field";
                    $entity->$setter($revertedValue);

                    $this->entityManager->flush();
                    $this->eventDispatcher->dispatch(
                        new HistoryRevertEvent($class, $id, $field, $timestamp, $entry),
                        HistoryRevertEvent::POST_REVERT
                    );
                }
            }
        }
    }
}
