<?php

namespace App\Database;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;

#[OpenApiModel('A theme blog category represents a blog category in the current theme configuration')]
class ThemeBlogCategory extends Utils\ThemeHelperEntity implements Utils\FormattableEntityInterface
{
    #[OpenApiField(object: true, objectRef: BlogCategory::class, name: 'blogCategory')]
    public int $blogCategoryId = -1;

    /**
     * @param int $themeId
     * @param string $name
     * @return ThemeBlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeBlogCategory|null
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_blog_category', new self());
    }

    /**
     * @param int $themeId
     * @return Iterator<ThemeBlogCategory>
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_blog_category', new self());
    }

    /**
     * @inheritDoc
     */
    public function create(): void
    {
        $this->internalCreate('theme_blog_category');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_blog_category');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->internalUpdate('theme_blog_category');
    }

    /**
     * @return array{blogCategory: array{description: null|string, id: int, name: string, parent: array<array-key, mixed>|null}|null, name: string}
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    #[ArrayShape(['name' => "string", 'blogCategory' => "array"])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'blogCategory' => $this->getBlogCategory()?->format(),
        ];
    }

    /**
     * @return BlogCategory|null
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function getBlogCategory(): BlogCategory|null
    {
        return BlogCategory::findById($this->blogCategoryId);
    }
}