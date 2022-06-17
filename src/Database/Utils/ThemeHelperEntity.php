<?php

namespace App\Database\Utils;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\ThemeAsset;
use App\Database\ThemeBlogCategory;
use App\Database\ThemeFile;
use App\Database\ThemeForm;
use App\Database\ThemeGallery;
use App\Database\ThemeMenu;
use App\Database\ThemePage;
use App\Database\ThemeSegmentPage;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\StrategyInterface;
use RuntimeException;

/**
 * The ThemeHelperEntity adds several additional helper methods specifically for the the entities
 */
abstract class ThemeHelperEntity extends LoadableEntity
{
    /** @var string The theme name */
    public string $name = '';
    /** @var int The theme id */
    public int $themeId = -1;

    /**
     * Is not available in theme entities, use findByThemeAndName instead
     *
     * @param int $id
     * @return object|null
     */
    public static function findById(int $id): ?object
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Is not available in theme entities, use findByTheme instead and filter the result
     *
     * @param string $keyword
     * @return Iterator
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Is not available in theme entities, use findByTheme instead
     *
     * @return Iterator
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * Finds a file by name and theme
     *
     * @param int $themeId The ID of the theme the entity belongs to
     * @param string $name The name of the entity link value
     * @return ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory
     */
    abstract public static function findByThemeAndName(int $themeId, string $name): mixed;

    /**
     * Finds the items for the given theme
     *
     * @param int $themeId The ID of the theme the entities belongs to
     * @return Iterator<ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory>
     */
    abstract public static function findByTheme(int $themeId): Iterator;

    /**
     * Fetches the item by theme and name
     *
     * @param int $themeId The ID of the theme the entity belongs to
     * @param string $name The name of the entity link value
     * @param string $table The table to fetch from
     * @param ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory $prototype An initialized instance of the class to hydrate the values into
     * @return ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     */
    protected static function fetchByThemeAndName(int $themeId, string $name, string $table, ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory $prototype): ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory|null
    {
        $sql = "SELECT * FROM $table WHERE theme_id = :id AND name = :name";

        try {
            return self::getPdo()->fetchObject($sql, $prototype, [
                'id' => $themeId,
                'name' => $name,
            ]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Fetches the items for the given theme
     *
     * @param int $themeId The ID of the theme the entity belongs to
     * @param string $table The table to fetch from
     * @param ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory $prototype An initialized instance of the class to hydrate the values into
     * @return Iterator<object>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected static function fetchByTheme(int $themeId, string $table, ThemeAsset|ThemeFile|ThemeForm|ThemeGallery|ThemeMenu|ThemePage|ThemeSegmentPage|ThemeBlogCategory $prototype): Iterator
    {
        $sql = "SELECT * FROM $table WHERE theme_id = :id";

        try {
            return self::getPdo()->fetchIterator($sql, $prototype, ['id' => $themeId]);
        } catch (InvalidQueryException$exception) {
            throw self::convertInvalidQueryExceptionToException($exception);
        }
    }

    /**
     * Function to delete the current entity
     *
     * @param string $table The table to delete the entity from
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException,
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
     * Function to update the current entity
     *
     * @param string $table The table to update the entity in
     * @param array<StrategyInterface> $strategies An array of additional hydration strategies
     * @param array<string> $skippedFields The fields to not include in the sql statement
     * @return void
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
