<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 08:09.
 */

namespace Jinya\Services\History;

use Doctrine\ORM\EntityManagerInterface;
use function array_filter;
use function method_exists;

class HistoryService implements HistoryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * HistoryService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getHistory(string $class, int $id): array
    {
        return $this->entityManager->find($this->getFullClassName($class), $id)->getHistory();
    }

    /**
     * @param string $class
     *
     * @return string
     */
    protected function getFullClassName(string $class): string
    {
        $class = "Jinya\\Entity\\$class";

        return $class;
    }

    /**
     * {@inheritdoc}
     */
    public function clearHistory(string $class, int $id): void
    {
        $entity = $this->entityManager->find($this->getFullClassName($class), $id);
        $entity->setHistory([]);

        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function revert(string $class, int $id, string $field, string $timestamp): void
    {
        $entity = $this->entityManager->find($this->getFullClassName($class), $id);

        if (method_exists($entity, "set$field")) {
            $history = $entity->getHistory();
            $entry = array_filter($history, function (array $item) use ($timestamp) {
                return $item['timestamp'] === $timestamp;
            })[0];

            $revertedValue = $entry['entry'][$field][1];
            $setter = "set$field";
            $entity->$setter($revertedValue);

            $this->entityManager->flush();
        }
    }
}
