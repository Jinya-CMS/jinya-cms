<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a gallery connected to a theme
 */
class ThemeGallery extends ThemeHelperEntity
{
    /** @var int The gallery ID */
    public int $galleryId = -1;

    /**
     * Finds a gallery by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeGallery|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeGallery
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_gallery', new self());
    }

    /**
     * Finds the galleries for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_gallery', new self());
    }

    /**
     * Formats the theme gallery into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape(['name' => 'string', 'gallery' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'gallery' => $this->getGallery()?->format(),
        ];
    }

    /**
     * Gets the gallery of the theme gallery
     *
     * @return Gallery|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getGallery(): ?Gallery
    {
        return Gallery::findById($this->galleryId);
    }

    /**
     * Creates the given theme gallery
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('theme_gallery');
    }

    /**
     * Deletes the current theme gallery
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_gallery');
    }

    /**
     * Updates the current theme gallery
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_gallery');
    }
}
