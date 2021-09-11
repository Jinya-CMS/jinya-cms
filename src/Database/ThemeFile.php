<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;

class ThemeFile extends ThemeHelperEntity implements FormattableEntityInterface
{

    public int $fileId = -1;

    /**
     * Finds a file by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeFile|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeFile
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_file', new self());
    }

    /**
     * Finds the files for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_file', new self());
    }

    /**
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     */
    #[ArrayShape(['name' => "string", 'file' => "array"])] public function format(): array
    {
        return [
            'name' => $this->name,
            'file' => $this->getFile()->format(),
        ];
    }

    /**
     * Gets the file of the theme file
     *
     * @return File|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_file');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_file');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_file');
    }
}