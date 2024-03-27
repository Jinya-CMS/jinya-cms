<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\ThemeBlogCategory;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;
use PDOException;

class ThemeBlogCategoryTest extends ThemeTestCase
{
    private BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $this->category = $category;
    }

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
        $themeCat->themeId = $this->theme->id;
        $themeCat->blogCategoryId = $this->category->id;
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

        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->id, $themeCat->name);
        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testCreateNonUniqueName(): void
    {
        $this->expectException(PDOException::class);
        $this->createThemeBlogCategory();
        $this->createThemeBlogCategory();
    }

    public function testUpdate(): void
    {
        $themeCat = $this->createThemeBlogCategory();

        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $themeCat->blogCategoryId = $category->id;
        $themeCat->update();

        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->id, $themeCat->name);
        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testFindByThemeAndName(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->id, $themeCat->name);

        $this->assertEquals($themeCat->format(), $foundCat->format());
    }

    public function testDelete(): void
    {
        $themeCat = $this->createThemeBlogCategory();
        $themeCat->delete();
        $foundCat = ThemeBlogCategory::findByThemeAndName($this->theme->id, $themeCat->name);

        $this->assertNull($foundCat);
    }

    public function testFindByTheme(): void
    {
        $this->createThemeBlogCategory(name: Uuid::uuid());
        $this->createThemeBlogCategory(name: Uuid::uuid());

        $found = ThemeBlogCategory::findByTheme($this->theme->id);
        $this->assertCount(2, iterator_to_array($found));
    }
}
