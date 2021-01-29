<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiField;

abstract class RearrangableEntity extends LoadableEntity
{
    #[OpenApiField(required: true)]
    public int $position;

    /**
     * Moves the entity
     *
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function move(int $newPosition): void
    {
        $this->position = $newPosition;
        $this->update();
    }

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
    }
}
