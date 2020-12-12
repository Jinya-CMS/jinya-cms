<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;

class ThemePage extends ThemeHelperEntity implements FormattableEntityInterface
{

    public int $pageId = -1;
    public int $themeId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemePage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemePage
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_page', new self());
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
        return self::fetchByTheme($themeId, 'theme_page', new self());
    }

    public function format(): array
    {
        return [
            'name' => $this->name,
            'page' => $this->getPage()->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return SimplePage
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getPage(): SimplePage
    {
        return SimplePage::findById($this->pageId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_page');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_page');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_page');
    }
}