<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 *
 */
class ThemeSegmentPage extends ThemeHelperEntity
{

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
     * @return array
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
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getSegmentPage(): ?SegmentPage
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
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