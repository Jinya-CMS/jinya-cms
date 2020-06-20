<?php

namespace App\Database\Utils;

use App\Database\Exceptions\UniqueFailedException;
use Exception;
use Iterator;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ReflectionHydrator;
use RuntimeException;

abstract class ThemeHelperEntity extends LoadableEntity
{

    public string $name = '';
    public int $themeId = -1;

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

        return self::hydrateSingleResult($result, $prototype);
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
            ->where(['theme_id = :id']);

        $result = self::executeStatement($sql->prepareStatementForSqlObject($select), ['id' => $themeId]);

        return self::hydrateMultipleResults($result, $prototype);
    }

    /**
     * @param string $table
     */
    protected function internalDelete(string $table): void
    {
        $sql = self::getSql();
        $delete = $sql->delete()->from($table)->where(['theme_id = :themeId', 'name = :name']);
        self::executeStatement($sql->prepareStatementForSqlObject($delete), [
            'name' => $this->name,
            'themeId' => $this->themeId,
        ]);
    }

    /**
     * @param string $table
     * @param array $strategies
     * @throws UniqueFailedException
     * @throws Exception
     */
    protected function internalUpdate(string $table, array $strategies = []): void
    {
        $sql = self::getSql();
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($strategies as $key => $strategy) {
            $hydrator->addStrategy($key, $strategy);
        }

        $data = $hydrator->extract($this);
        $params = [];
        foreach ($data as $key => $value) {
            if ($key === 'id') {
                continue;
            }
            $params[$key] = $value;
        }

        $update = $sql->update($table)
            ->where(['theme_id = :themeId', 'name = :name'])
            ->set($params);

        try {
            self::executeStatement($sql->prepareStatementForSqlObject($update), [
                'name' => $this->name,
                'themeId' => $this->themeId,
            ]);
        } catch (InvalidQueryException $exception) {
            throw $this->convertInvalidQueryExceptionToException($exception);
        }
    }
}