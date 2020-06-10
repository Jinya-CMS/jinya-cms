<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;

class ThemeGallery extends ThemeHelperEntity implements FormattableEntityInterface
{

    public string $name;
    public int $galleryId;
    public int $themeId;

    /**
     * Finds a gallery by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeGallery
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeGallery
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_gallery', new self());
    }

    /**
     * Finds the gallerys for the given theme
     *
     * @param int $themeId
     * @return Iterator
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_gallery', new self());
    }

    public function format(): array
    {
        return [
            'name' => $this->name,
            'gallery' => $this->getGallery()->format(),
        ];
    }

    /**
     * Gets the gallery of the theme gallery
     *
     * @return Gallery
     */
    public function getGallery(): Gallery
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