<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a blog category connected to a theme
 */
class ThemeBlogCategory extends Utils\ThemeHelperEntity
{
    /** @var int The blog category ID */
    public int $blogCategoryId = -1;

    /**
     * Finds a blog category by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeBlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeBlogCategory|null
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_blog_category', new self());
    }

    /**
     * Finds all theme blog categories in the with the given ID
     *
     * @param int $themeId
     * @return Iterator<ThemeBlogCategory>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByTheme($themeId, 'theme_blog_category', new self());
    }

    /**
     * Creates the current theme blog category
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function create(): void
    {
        $this->internalCreate('theme_blog_category');
    }

    /**
     * Deletes the current theme blog category
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_blog_category');
    }

    /**
     * Updates the current theme
     *
     * @return void
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_blog_category');
    }

    /**
     * Formats the theme blog category into an array
     *
     * @return array<string, array<string, mixed>|string|null>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    #[ArrayShape(['name' => 'string', 'blogCategory' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'blogCategory' => $this->getBlogCategory()?->format(),
        ];
    }

    /**
     * Gets the blog category associated to this theme blog category
     *
     * @return BlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    public function getBlogCategory(): BlogCategory|null
    {
        return BlogCategory::findById($this->blogCategoryId);
    }
}
