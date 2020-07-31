<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;

class ThemeSegmentPage extends ThemeHelperEntity implements FormattableEntityInterface
{

    public int $segmentPageId = -1;
    public int $themeId = -1;

    /**
     * Finds a page by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeSegmentPage
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeSegmentPage
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_segment_page', new self());
    }

    /**
     * Finds the pages for the given theme
     *
     * @param int $themeId
     * @return Iterator
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_segment_page', new self());
    }

    public function format(): array
    {
        return [
            'name' => $this->name,
            'segmentPage' => $this->getSegmentPage()->format(),
        ];
    }

    /**
     * Gets the page of the theme page
     *
     * @return SegmentPage
     */
    public function getSegmentPage(): SegmentPage
    {
        return SegmentPage::findById($this->segmentPageId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_segment_page');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_segment_page');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_segment_page');
    }
}