<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;

class ThemeGallery extends ThemeHelperEntity implements FormattableEntityInterface
{

    public int $galleryId = -1;

    /**
     * Finds a gallery by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeGallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeGallery
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_gallery', new self());
    }

    /**
     * Finds the gallerys for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_gallery', new self());
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\InvalidQueryException
     */
    #[ArrayShape(['name' => "string", 'gallery' => "array"])] public function format(): array
    {
        return [
            'name' => $this->name,
            'gallery' => $this->getGallery()->format(),
        ];
    }

    /**
     * Gets the gallery of the theme gallery
     *
     * @return Gallery|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_gallery');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_gallery');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_gallery');
    }
}