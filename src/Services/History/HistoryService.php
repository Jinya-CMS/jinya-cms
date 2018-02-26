<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 08:09
 */

namespace Jinya\Services\History;


use Doctrine\ORM\EntityManagerInterface;
use function method_exists;

class HistoryService implements HistoryServiceInterface
{

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @inheritdoc
     */
    public function getHistory(string $class, int $id): array
    {
        return $this->entityManager->find($class, $id)->getHistory();
    }

    /**
     * @inheritdoc
     */
    public function clearHistory(string $class, int $id): void
    {
        $entity = $this->entityManager->find($class, $id);
        $entity->setHistory([]);

        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function revert(string $class, int $id, string $field, $value): void
    {
        $entity = $this->entityManager->find($class, $id);

        if (method_exists($entity, "set$field")) {
            $entity->${"set$field"}($value);
        }
    }
}