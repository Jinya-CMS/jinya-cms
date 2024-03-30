<?php

namespace Jinya\Cms\Database;

use Jinya\Cms\Tests\ThemeTestCase;
use Faker\Provider\Uuid;
use Jinya\Database\Entity;
use stdClass;

class ThemeTest extends ThemeTestCase
{
    public function testFindById(): void
    {
        $theme = Theme::findById($this->theme->id);
        self::assertEquals($theme->format(), $this->theme->format());
    }

    public function testFindByIdNonExistent(): void
    {
        $theme = Theme::findById(-1);
        self::assertNull($theme);
    }

    public function testFormatNoConfigAndScss(): void
    {
        $configuration = [
            'configuration' => new stdClass(),
            'description' => $this->theme->description,
            'name' => $this->theme->name,
            'displayName' => $this->theme->displayName,
            'scssVariables' => new stdClass(),
            'id' => $this->theme->id,
            'hasApi' => $this->theme->hasApiTheme,
        ];
        self::assertEquals($configuration, $this->theme->format());
    }

    public function testFormat(): void
    {
        $this->theme->configuration = ['test' => 'test'];
        $this->theme->scssVariables = ['test' => 'test'];

        $configuration = [
            'configuration' => $this->theme->configuration,
            'description' => $this->theme->description,
            'name' => $this->theme->name,
            'displayName' => $this->theme->displayName,
            'scssVariables' => $this->theme->scssVariables,
            'id' => $this->theme->id,
            'hasApi' => $this->theme->hasApiTheme,
        ];
        self::assertEquals($configuration, $this->theme->format());
    }

    public function testActiveTheme(): void
    {
        $query = Entity::getQueryBuilder()
            ->newInsert()
            ->into('configuration')
            ->addRow(['current_frontend_theme_id' => null]);
        Entity::executeQuery($query);

        $activeTheme = Theme::getActiveTheme();
        self::assertNull($activeTheme);

        Theme::findByName($this->theme->name)->makeActiveTheme();
        $activeTheme = Theme::getActiveTheme();
        self::assertNotNull($activeTheme);
        self::assertEquals($this->theme->format(), $activeTheme->format());
    }

    public function testUpdate(): void
    {
        $this->theme->displayName = 'New name';
        $this->theme->configuration = ['test' => Uuid::uuid()];
        $this->theme->scssVariables = ['test' => Uuid::uuid()];
        $this->theme->description = ['en' => 'Test'];
        $this->theme->hasApiTheme = !$this->theme->hasApiTheme;
        $this->theme->update();

        $changedTheme = Theme::findById($this->theme->id);
        self::assertEquals($this->theme->format(), $changedTheme->format());
    }

    public function testFindByNameNonExistent(): void
    {
        $theme = Theme::findByName('This theme does not exist');
        self::assertNull($theme);
    }

    public function testFindByName(): void
    {
        $theme = Theme::findByName($this->theme->name);
        self::assertEquals($theme->format(), $this->theme->format());
    }

    public function testCreate(): void
    {
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->hasApiTheme = true;
        $theme->create();

        $created = Theme::findByName($theme->name);
        self::assertEquals($theme->format(), $created->format());
    }

    public function testDelete(): void
    {
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->create();

        $created = Theme::findByName($theme->name);
        self::assertEquals($theme->format(), $created->format());

        $created->delete();
        self::assertNull(Theme::findByName($theme->name));
    }

    public function testFindAll(): void
    {
        $themes = Theme::findAll();
        self::assertCount(1, iterator_to_array($themes));

        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->create();

        $themes = Theme::findAll();
        self::assertCount(2, iterator_to_array($themes));
    }

    public function testGetAssets(): void
    {
        $asset = new ThemeAsset();
        $asset->publicPath = '/local';
        $asset->themeId = $this->theme->id;
        $asset->name = 'Test';
        $asset->create();

        $assets = $this->theme->getAssets();
        self::assertArrayHasKey($asset->name, $assets);
    }

    public function testGetCategories(): void
    {
        $category = new BlogCategory();
        $category->name = Uuid::uuid();
        $category->create();

        $themeCat = new ThemeBlogCategory();
        $themeCat->themeId = $this->theme->id;
        $themeCat->blogCategoryId = $category->id;
        $themeCat->name = 'Test';
        $themeCat->create();

        $cats = $this->theme->getCategories();
        self::assertArrayHasKey($themeCat->name, $cats);
    }

    public function testGetFiles(): void
    {
        $file = new File();
        $file->name = 'Tempfile';
        $file->create();

        $themeFile = new ThemeFile();
        $themeFile->fileId = $file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->name = 'Test';
        $themeFile->create();

        $files = $this->theme->getFiles();
        self::assertArrayHasKey($themeFile->name, $files);
    }

    public function testGetForms(): void
    {
        $form = new Form();
        $form->title = Uuid::uuid();
        $form->toAddress = 'example@example.com';
        $form->create();

        $themeForm = new ThemeForm();
        $themeForm->formId = $form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->name = 'Test';
        $themeForm->create();

        $forms = $this->theme->getForms();
        self::assertArrayHasKey($themeForm->name, $forms);
    }

    public function testGetGalleries(): void
    {
        $gallery = new Gallery();
        $gallery->name = 'Tempgallery';
        $gallery->create();

        $themeGallery = new ThemeGallery();
        $themeGallery->galleryId = $gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->name = 'Test';
        $themeGallery->create();

        $galleries = $this->theme->getGalleries();
        self::assertArrayHasKey($themeGallery->name, $galleries);
    }

    public function testGetMenus(): void
    {
        $menu = new Menu();
        $menu->name = 'Tempmenu';
        $menu->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->menuId = $menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->name = 'Test';
        $themeMenu->create();

        $menus = $this->theme->getMenus();
        self::assertArrayHasKey($themeMenu->name, $menus);
    }

    public function testGetPages(): void
    {
        $simplePage = new ClassicPage();
        $simplePage->title = Uuid::uuid();
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $themePage = new ThemeClassicPage();
        $themePage->classicPageId = $simplePage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $pages = $this->theme->getClassicPages();
        self::assertArrayHasKey($themePage->name, $pages);
    }

    public function testGetSegmentPages(): void
    {
        $segmentPage = new ModernPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $themePage = new ThemeModernPage();
        $themePage->modernPageId = $segmentPage->id;
        $themePage->themeId = $this->theme->id;
        $themePage->name = 'Test';
        $themePage->create();

        $pages = $this->theme->getModernPages();
        self::assertArrayHasKey($themePage->name, $pages);
    }
}
