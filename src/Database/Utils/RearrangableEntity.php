<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Jinya\PDOx\Exceptions\InvalidQueryException;

/**
 * Base class for all entities that need the ability to rearrange themselves in their parent
 */
abstract class RearrangableEntity extends LoadableEntity
{
    /** @var int The position of the current entity */
    public int $position;

    /**
     * Moves the entity
     *
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    abstract public function move(int $newPosition): void;

    /**
     * Rearranges the items in the same parent
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalRearrange(string $table, string $parentIdName, int $parentId, int $newPosition): void
    {
        $sql = "SELECT position, id FROM $table WHERE position >= :newPosition AND $parentIdName = :parentId ORDER BY position";
        $result = self::executeStatement($sql, ['newPosition' => $newPosition, 'parentId' => $parentId]);
        if (is_array($result)) {
            $previousPosition = $newPosition;
            foreach ($result as $item) {
                ++$previousPosition;
                $stmt = "UPDATE $table SET position = :position WHERE id = :id";
                self::executeStatement($stmt, ['position' => $previousPosition, 'id' => $item['id']]);
            }
        }

        $this->position = $newPosition;
    }

    /**
     * Resets the order of all entities in the given parent. Reset means in this context, that it starts the position from 0 and counts up
     *
     * @param string $table The table the entity is rearranged in
     * @param string $parentIdName The name of the parent id
     * @param int $parentId The parent id
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function resetOrder(string $table, string $parentIdName, int $parentId): void
    {
        $sql = "SELECT position, id FROM $table WHERE $parentIdName = :parentId ORDER BY position";
        $result = self::executeStatement($sql, ['parentId' => $parentId]);

        if (!is_countable($result)) {
            $result = [];
        }
        foreach ($result as $i => $item) {
            $stmt = "UPDATE $table SET position = :position WHERE id = :id";
            self::executeStatement($stmt, ['position' => $i, 'id' => $item['id']]);
        }
    }
}
