<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Theme;
use App\Database\ThemeBlogCategory;
use Faker\Provider\Uuid;
use PHPUnit\Framework\TestCase;

class ThemeBlogCategoryTest extends TestCase
{
    private Theme $theme;
    private BlogCategory $category;

    public function testFormat(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $formatted = $themeCat->format();

        $this->assertArrayHasKey('name', $formatted);
        $this->assertArrayHasKey('blogCategory', $formatted);
    }

    public function createThemeBlogCategory(bool $execute = true, string $name = 'Test'): ThemeBlogCategory
    {
        $themeCat = new ThemeBlogCategory();
        $themeCat->themeId = $this->theme->getIdAsInt();
        $themeCat->blogCategoryId = $this->category->getIdAsInt();
        $themeCat->name = $name;
        if ($execute) {
            $themeCat->create();
        }

        return $themeCat;
    }

    public function testGetBlogCategory(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $cat = $themeCat->getBlogCategory();

        $this->assertEquals($this->category, $cat);
    }

    public function testCreate(): void
    {
        $themeCat = $this->createThemeBlogCategory(execute: false);
        $themeCat->create();

        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->getIdAsInt(), $themeCat->name);
        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testCreateNonUniqueName(): void
    {
        $this->expectException(UniqueFailedException::class);
        $this->createThemeBlogCategory();
        $this->createThemeBlogCategory();
    }

    public function testUpdate(): void
    {
        $themeCat = $this->createThemeBlogCategory();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $themeCat->blogCategoryId = $category->getIdAsInt();
        $themeCat->update();

        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->getIdAsInt(), $themeCat->name);
        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testFindByThemeAndName(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->getIdAsInt(), $themeCat->name);

        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testDelete(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $themeCat->delete();
        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->getIdAsInt(), $themeCat->name);

        $this->assertNull($foundCat);
    }

    public function testFindByTheme(): void
    {
        $this->createThemeBlogCategory(name: Uuid::uuid());
        $this->createThemeBlogCategory(name: Uuid::uuid());

        $found = ThemeBlogCategory::findByTheme($this->theme->getIdAsInt());
        $this->assertCount(2, $found);
    }

    protected function setUp(): void
    {
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = Uuid::uuid();
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->create();

        $this->theme = $theme;

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $this->category = $category;
    }
}
