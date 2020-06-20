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
     * @return ThemeAsset
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
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_asset', new self());
    }

    /**
     * @inheritDoc
     */
    public static function findById(int $id)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findByKeyword(string $keyword): Iterator
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @inheritDoc
     */
    public static function findAll(): Iterator
    {
        throw new RuntimeException('Not implemented');
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