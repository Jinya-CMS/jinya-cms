<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a segment page connected to a theme
 */
class ThemeSegmentPage extends ThemeHelperEntity
{
    /** @var int The segment page ID */
    public int $segmentPageId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeSegmentPage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeSegmentPage
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_segment_page', new self());
    }

    /**
     * Finds the pages for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_segment_page', new self());
    }

    /**
     * Formats the theme segment page into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape(['name' => 'string', 'segmentPage' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'segmentPage' => $this->getSegmentPage()?->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return SegmentPage|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        return SegmentPage::findById($this->segmentPageId);
    }

    /**
     * Creates the current theme segment page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('theme_segment_page');
    }

    /**
     * Deletes the current theme segment page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_segment_page');
    }

    /**
     * Updates the current theme segment page
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_segment_page');
    }
}
