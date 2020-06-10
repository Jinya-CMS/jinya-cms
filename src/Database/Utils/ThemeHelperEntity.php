<?php

namespace App\Database\Utils;

use Iterator;
use Laminas\Db\Sql\Predicate\PredicateSet;
use RuntimeException;

abstract class ThemeHelperEntity extends LoadableEntity
{

    public static function findById(int $id)
    {
        throw new RuntimeException('Not implemented');
    }

    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Finds a file by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return mixed
     */
    abstract public static function findByThemeAndName(int $themeId, string $name);

    /**
     * Finds the items for the given theme
     *
     * @param int $themeId
     * @return Iterator
     */
    abstract public static function findByTheme(int $themeId): Iterator;

    /**
     * Fetches the item by theme and name
     *
     * @param int $themeId
     * @param string $name
     * @param string $table
     * @param $prototype
     * @return mixed
     */
    protected static function fetchByThemeAndName(int $themeId, string $name, string $table, $prototype)
    {
        $sql = self::getSql();
        $select = $sql->select()->from($table)->where(['theme_id = :id AND name = :name']);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), [
            'id' => $themeId,
            'name' => $name
        ]);

        $hydrated = self::hydrateSingleResult($result, $prototype);
        if ($hydrated) {
            return $hydrated;
        }

        $prototype->create();
        return $prototype;
    }

    /**
     * Fetches the items for the given theme
     *
     * @param int $themeId
     * @param string $table
     * @param $prototype
     * @return Iterator
     */
    protected static function fetchByTheme(int $themeId, string $table, $prototype): Iterator
    {
        $sql = self::getSql();
        $select = $sql
            ->select()
            ->from($table)
            ->where(['theme_id = :id'], PredicateSet::OP_OR);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $themeId]);

        return self::hydrateMultipleResults($result, $prototype);
    }
}