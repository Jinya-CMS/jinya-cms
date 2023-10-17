<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a file connected to a theme
 */
class ThemeFile extends ThemeHelperEntity
{
    /** @var int The file ID */
    public int $fileId = -1;

    /**
     * Finds a file by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeFile|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeFile
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_file', new self());
    }

    /**
     * Finds the files for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_file', new self());
    }

    /**
     * Formats the theme file into an array
     *
     * @return array{'name': string, 'file': array<string, mixed>|null}
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape(['name' => 'string', 'file' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'file' => $this->getFile()?->format(),
        ];
    }

    /**
     * Gets the file of the theme file
     *
     * @return File|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * Create the current theme file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('theme_file');
    }

    /**
     * Deletes the current theme file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_file');
    }

    /**
     * Updates the current theme file
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_file');
    }
}
