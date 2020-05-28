<?php

namespace App\Database\Utils;

use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Predicate\PredicateSet;

abstract class RearrangableEntity extends LoadableEntity
{
    /**
     * Rearranges the items in the same parent
     *
     * @param string $table
     * @param string $parentIdName
     * @param int $parentId
     * @param int $newPosition
     */
    protected function internalRearrange(string $table, string $parentIdName, int $parentId, int $newPosition): void {
        $sql = self::getSql();
        $select = $sql->select()
            ->from($table)
            ->columns(['id', 'position'])
            ->where(['position >= :newPosition', "$parentIdName = :parentId"], PredicateSet::OP_AND)
            ->order('position ASC');

        $result = $sql->prepareStatementForSqlObject($select)->execute([
            'newPosition' => $newPosition,
            'parentId' => $parentId,
        ]);

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            $previousPosition = $newPosition;
            foreach ($resultSet as $row) {
                $update = $sql->update($table)
                    ->set(['position' => ++$previousPosition])
                    ->where('id = :id');
                $statement = $sql->prepareStatementForSqlObject($update);
                $statement->execute(['id' => $row->id]);
            }
        }
    }
}