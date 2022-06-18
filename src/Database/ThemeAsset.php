<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Iterator;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 *
 */
class ThemeAsset extends ThemeHelperEntity
{
    /** @var string The public path of the asset */
    public string $publicPath = '';

    /**
     * Finds an asset by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeAsset|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeAsset
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_asset', new self());
    }

    /**
     * Finds the pages for the given theme
     *
     * @param int $themeId
     * @return Iterator<ThemeAsset>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByTheme($themeId, 'theme_asset', new self());
    }

    /**
     * Creates the current theme asset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('theme_asset');
    }

    /**
     * Deletes the current theme asset
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_asset');
    }

    /**
     * Updates the current theme
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_asset');
    }

    /**
     * Always returns an empty array
     *
     * @return array<string>
     */
    public function format(): array
    {
        return [];
    }
}
