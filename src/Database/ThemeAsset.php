<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;
use RuntimeException;

class ThemeAsset extends ThemeHelperEntity
{

    public int $themeId = -1;
    public string $publicPath = '';

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeAsset|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeAsset
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_asset', new self());
    }

    /**
     * Finds the pages for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_asset', new self());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_asset');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_asset');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_asset');
    }
}