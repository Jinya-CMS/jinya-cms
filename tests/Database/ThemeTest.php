<?php

namespace Jinya\Tests\Database;

use App\Database\BlogCategory;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Database\Theme;
use App\Database\ThemeAsset;
use App\Database\ThemeBlogCategory;
use App\Database\ThemeFile;
use App\Database\ThemeForm;
use App\Database\ThemeGallery;
use App\Database\ThemeMenu;
use App\Database\ThemePage;
use App\Database\ThemeSegmentPage;
use App\Database\Utils\LoadableEntity;
use App\Tests\ThemeTestCase;
use Faker\Provider\Uuid;
use stdClass;

class ThemeTest extends ThemeTestCase
{

    public function testFindById(): void
    {
        $theme = Theme::findById($this->theme->getIdAsInt());
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
            'id' => $this->theme->getIdAsInt(),
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
            'id' => $this->theme->getIdAsInt(),
            'hasApi' => $this->theme->hasApiTheme,
        ];
        self::assertEquals($configuration, $this->theme->format());
    }

    public function testActiveTheme(): void
    {
        LoadableEntity::executeSqlString('INSERT INTO configuration (current_frontend_theme_id) VALUES (null)');

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

        $changedTheme = Theme::findById($this->theme->getIdAsInt());
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

    public function testFindByKeyword(): void
    {
        $theme = new Theme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->create();

        $foundThemes = Theme::findByKeyword($theme->displayName);
        self::assertCount(1, iterator_to_array($foundThemes));

        $foundThemes = Theme::findByKeyword(Uuid::uuid());
        self::assertCount(0, iterator_to_array($foundThemes));
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
        $asset->themeId = $this->theme->getIdAsInt();
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
        $themeCat->themeId = $this->theme->getIdAsInt();
        $themeCat->blogCategoryId = $category->getIdAsInt();
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
        $themeFile->fileId = $file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
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
        $themeForm->formId = $form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
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
        $themeGallery->galleryId = $gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
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
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->themeId = $this->theme->getIdAsInt();
        $themeMenu->name = 'Test';
        $themeMenu->create();

        $menus = $this->theme->getMenus();
        self::assertArrayHasKey($themeMenu->name, $menus);
    }

    public function testGetPages(): void
    {
        $simplePage = new SimplePage();
        $simplePage->title = Uuid::uuid();
        $simplePage->content = Uuid::uuid();
        $simplePage->create();

        $themePage = new ThemePage();
        $themePage->pageId = $simplePage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        $pages = $this->theme->getPages();
        self::assertArrayHasKey($themePage->name, $pages);
    }

    public function testGetSegmentPages(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $themePage = new ThemeSegmentPage();
        $themePage->segmentPageId = $segmentPage->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
        $themePage->name = 'Test';
        $themePage->create();

        $pages = $this->theme->getSegmentPages();
        self::assertArrayHasKey($themePage->name, $pages);
    }
}
