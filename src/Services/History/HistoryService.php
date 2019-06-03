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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function array_filter;
use function method_exists;

class HistoryService implements HistoryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

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
        $this->eventDispatcher->dispatch(HistoryEvent::PRE_GET, new HistoryEvent($class, $id, []));
        $history = $this->entityManager->find($this->getFullClassName($class), $id)->getHistory();
        $this->eventDispatcher->dispatch(HistoryEvent::POST_GET, new HistoryEvent($class, $id, $history));

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

        $pre = $this->eventDispatcher->dispatch(
            HistoryEvent::PRE_CLEAR,
            new HistoryEvent($class, $id, $entity->getHistory())
        );

        if (!$pre->isCancel()) {
            $entity->setHistory([]);

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(HistoryEvent::POST_CLEAR, new HistoryEvent($class, $id, []));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revert(string $class, int $id, string $field, string $timestamp): void
    {
        $entity = $this->entityManager->find($this->getFullClassName($class), $id);

        if (method_exists($entity, "set$field")) {
            $history = $entity->getHistory();
            /** @var array $entry */
            $entry = array_filter($history, function (array $item) use ($timestamp) {
                return $item['timestamp'] === $timestamp;
            })[0];

            if (array_key_exists($field, $entry['entry'])) {
                $pre = $this->eventDispatcher->dispatch(
                    HistoryRevertEvent::PRE_REVERT,
                    new HistoryRevertEvent($class, $id, $field, $timestamp, $entry)
                );
                if (!$pre->isCancel()) {
                    $revertedValue = $entry['entry'][$field][1];
                    $setter = "set$field";
                    $entity->$setter($revertedValue);

                    $this->entityManager->flush();
                    $this->eventDispatcher->dispatch(
                        HistoryRevertEvent::POST_REVERT,
                        new HistoryRevertEvent($class, $id, $field, $timestamp, $entry)
                    );
                }
            }
        }
    }
}
