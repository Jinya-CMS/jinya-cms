<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiField;
use Iterator;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ReflectionHydrator;
use RuntimeException;

abstract class ThemeHelperEntity extends LoadableEntity
{
    #[OpenApiField(required: true)]
    public string $name = '';
    public int $themeId = -1;

    public static function findById(int $id): ?object
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
     */
    abstract public static function findByThemeAndName(int $themeId, string $name): mixed;

    /**
     * Finds the items for the given theme
     */
    abstract public static function findByTheme(int $themeId): Iterator;

    /**
     * Fetches the item by theme and name
     *
     * @param $prototype
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    protected static function fetchByThemeAndName(int $themeId, string $name, string $table, $prototype): ?object
    {
        $sql = "SELECT * FROM $table WHERE theme_id = :id AND name = :name";
        $result = self::executeStatement(
            $sql,
            [
                'id' => $themeId,
                'name' => $name,
            ]
        );
        if (0 === count($result)) {
            return null;
        }

        return self::hydrateSingleResult($result[0], $prototype);
    }

    /**
     * Fetches the items for the given theme
     *
     * @param $prototype
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected static function fetchByTheme(int $themeId, string $table, $prototype): Iterator
    {
        $sql = "SELECT * FROM $table WHERE theme_id = :id";
        $result = self::executeStatement($sql, ['id' => $themeId]);

        return self::hydrateMultipleResults($result, $prototype);
    }

    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalDelete(string $table): void
    {
        $sql = "DELETE FROM $table WHERE theme_id = :themeId AND name = :name";
        self::executeStatement(
            $sql,
            [
                'name' => $this->name,
                'themeId' => $this->themeId,
            ]
        );
    }

    /**
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function internalUpdate(string $table, array $strategies = [], array $skippedFields = []): void
    {
        $hydrator = new ReflectionHydrator();
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($strategies as $key => $strategy) {
            $hydrator->addStrategy($key, $strategy);
        }
        $hydrator = self::getHydrator($strategies);
        $hydrator->addFilter('excludes', new SkipFieldFilter([...$skippedFields, 'id', 'pdo']));

        $extracted = $hydrator->extract($this);
        $update = [];
        foreach ($extracted as $key => $item) {
            $update[] = "$key=:$key";
        }
        $setInstructions = implode(',', $update);
        $sql = "UPDATE $table SET $setInstructions WHERE theme_id = :wThemeId AND name = :wName";
        $extracted['wName'] = $this->name;
        $extracted['wThemeId'] = $this->themeId;
        self::executeStatement($sql, $extracted);
    }
}
