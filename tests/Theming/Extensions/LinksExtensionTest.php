<?php

namespace Jinya\Cms\Theming\Extensions;

use Jinya\Cms\Database\BlogCategory;
use Jinya\Cms\Database\ClassicPage;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\Gallery;
use Jinya\Cms\Database\Menu;
use Jinya\Cms\Database\ModernPage;
use Jinya\Cms\Database\Theme;
use Jinya\Cms\Database\ThemeBlogCategory;
use Jinya\Cms\Database\ThemeClassicPage;
use Jinya\Cms\Database\ThemeFile;
use Jinya\Cms\Database\ThemeForm;
use Jinya\Cms\Database\ThemeGallery;
use Jinya\Cms\Database\ThemeMenu;
use Jinya\Cms\Database\ThemeModernPage;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Engine;
use Jinya\Cms\Theming\Extensions\LinksExtension;
use Faker\Factory;

class LinksExtensionTest extends DatabaseAwareTestCase
{
    private Theme $theme;
    private LinksExtension $extension;

    public function testHasGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = 'Temp';
        $gallery->create();

        $themeGallery = new ThemeGallery();
        $themeGallery->name = 'Test';
        $themeGallery->galleryId = $gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->create();

        self::assertTrue($this->extension->hasGallery('Test'));
    }

    public function testHasGalleryNotExisting(): void
    {
        $result = $this->extension->hasGallery('Not existing');
        self::assertFalse($result);
    }

    public function testHasBlogCategory(): void
    {
        $category = new BlogCategory();
        $category->name = 'Temp';
        $category->create();

        $themeBlogCategory = new ThemeBlogCategory();
        $themeBlogCategory->name = 'Test';
        $themeBlogCategory->blogCategoryId = $category->id;
        $themeBlogCategory->themeId = $this->theme->id;
        $themeBlogCategory->create();

        self::assertTrue($this->extension->hasBlogCategory('Test'));
    }

    public function testHasBlogCategoryNotExisting(): void
    {
        $result = $this->extension->hasBlogCategory('Not existing');
        self::assertFalse($result);
    }

    public function testGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = 'Temp';
        $gallery->create();

        $themeGallery = new ThemeGallery();
        $themeGallery->name = 'Test';
        $themeGallery->galleryId = $gallery->id;
        $themeGallery->themeId = $this->theme->id;
        $themeGallery->create();

        self::assertNotNull($this->extension->gallery('Test'));
    }

    public function testGalleryNotExisting(): void
    {
        $result = $this->extension->hasGallery('Not existing');
        self::assertFalse($result);
    }

    public function testMenu(): void
    {
        $menu = new Menu();
        $menu->name = 'Temp';
        $menu->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->name = 'Test';
        $themeMenu->menuId = $menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->create();

        self::assertNotNull($this->extension->menu('Test'));
    }

    public function testMenuNotExisting(): void
    {
        $result = $this->extension->menu('Not existing');
        self::assertNull($result);
    }

    public function testHasForm(): void
    {
        $form = new Form();
        $form->title = 'Temp';
        $form->toAddress = Factory::create()->safeEmail();
        $form->create();

        $themeForm = new ThemeForm();
        $themeForm->name = 'Test';
        $themeForm->formId = $form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->create();

        self::assertTrue($this->extension->hasForm('Test'));
    }

    public function testHasFormNotExisting(): void
    {
        $result = $this->extension->hasForm('Not existing');
        self::assertFalse($result);
    }

    public function testSegmentPage(): void
    {
        $page = new ModernPage();
        $page->name = 'Temp';
        $page->create();

        $themePage = new ThemeModernPage();
        $themePage->name = 'Test';
        $themePage->modernPageId = $page->id;
        $themePage->themeId = $this->theme->id;
        $themePage->create();

        self::assertNotNull($this->extension->segmentPage('Test'));
    }

    public function testSegmentPageNotExisting(): void
    {
        $result = $this->extension->segmentPage('Not existing');
        self::assertNull($result);
    }

    public function testForm(): void
    {
        $form = new Form();
        $form->title = 'Temp';
        $form->toAddress = Factory::create()->safeEmail();
        $form->create();

        $themeForm = new ThemeForm();
        $themeForm->name = 'Test';
        $themeForm->formId = $form->id;
        $themeForm->themeId = $this->theme->id;
        $themeForm->create();

        self::assertNotNull($this->extension->form('Test'));
    }

    public function testFormNotExisting(): void
    {
        $result = $this->extension->form('Not existing');
        self::assertNull($result);
    }

    public function testRegister(): void
    {
        $engine = Engine::getPlatesEngine();
        $this->extension->register($engine);

        self::assertTrue($engine->functions->exists('segmentPage'));
        self::assertTrue($engine->functions->exists('page'));
        self::assertTrue($engine->functions->exists('simplePage'));
        self::assertTrue($engine->functions->exists('file'));
        self::assertTrue($engine->functions->exists('menu'));
        self::assertTrue($engine->functions->exists('gallery'));
        self::assertTrue($engine->functions->exists('form'));
        self::assertTrue($engine->functions->exists('blogCategory'));

        self::assertTrue($engine->functions->exists('hasSegmentPage'));
        self::assertTrue($engine->functions->exists('hasPage'));
        self::assertTrue($engine->functions->exists('hasSimplePage'));
        self::assertTrue($engine->functions->exists('hasFile'));
        self::assertTrue($engine->functions->exists('hasGallery'));
        self::assertTrue($engine->functions->exists('hasMenu'));
        self::assertTrue($engine->functions->exists('hasForm'));
        self::assertTrue($engine->functions->exists('hasBlogCategory'));
    }

    public function testSimplePage(): void
    {
        $page = new ClassicPage();
        $page->title = 'Temp';
        $page->content = '';
        $page->create();

        $themePage = new ThemeClassicPage();
        $themePage->name = 'Test';
        $themePage->classicPageId = $page->id;
        $themePage->themeId = $this->theme->id;
        $themePage->create();

        self::assertNotNull($this->extension->simplePage('Test'));
    }

    public function testSimplePageNotExisting(): void
    {
        $result = $this->extension->simplePage('Not existing');
        self::assertNull($result);
    }

    public function testHasSegmentPage(): void
    {
        $page = new ModernPage();
        $page->name = 'Temp';
        $page->create();

        $themePage = new ThemeModernPage();
        $themePage->name = 'Test';
        $themePage->modernPageId = $page->id;
        $themePage->themeId = $this->theme->id;
        $themePage->create();

        self::assertTrue($this->extension->hasSegmentPage('Test'));
    }

    public function testHasSegmentPageNotExisting(): void
    {
        $result = $this->extension->hasSegmentPage('Not existing');
        self::assertFalse($result);
    }

    public function testHasSimplePage(): void
    {
        $page = new ClassicPage();
        $page->title = 'Temp';
        $page->content = '';
        $page->create();

        $themePage = new ThemeClassicPage();
        $themePage->name = 'Test';
        $themePage->classicPageId = $page->id;
        $themePage->themeId = $this->theme->id;
        $themePage->create();

        self::assertTrue($this->extension->hasSimplePage('Test'));
    }

    public function testHasSimplePageNotExisting(): void
    {
        $result = $this->extension->hasSimplePage('Not existing');
        self::assertFalse($result);
    }

    public function testHasFile(): void
    {
        $file = new File();
        $file->name = 'Temp';
        $file->create();

        $themeFile = new ThemeFile();
        $themeFile->name = 'Test';
        $themeFile->fileId = $file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->create();

        self::assertTrue($this->extension->hasFile('Test'));
    }

    public function testHasFileNotExisting(): void
    {
        $result = $this->extension->hasFile('Not existing');
        self::assertFalse($result);
    }

    public function testHasMenu(): void
    {
        $menu = new Menu();
        $menu->name = 'Temp';
        $menu->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->name = 'Test';
        $themeMenu->menuId = $menu->id;
        $themeMenu->themeId = $this->theme->id;
        $themeMenu->create();

        self::assertTrue($this->extension->hasMenu('Test'));
    }

    public function testHasMenuNotExisting(): void
    {
        $result = $this->extension->hasMenu('Not existing');
        self::assertFalse($result);
    }

    public function testFile(): void
    {
        $file = new File();
        $file->name = 'Temp';
        $file->create();

        $themeFile = new ThemeFile();
        $themeFile->name = 'Test';
        $themeFile->fileId = $file->id;
        $themeFile->themeId = $this->theme->id;
        $themeFile->create();

        self::assertNotNull($this->extension->file('Test'));
    }

    public function testFileNotExisting(): void
    {
        $result = $this->extension->file('Not existing');
        self::assertNull($result);
    }

    public function testBlogCategory(): void
    {
        $category = new BlogCategory();
        $category->name = 'Temp';
        $category->create();

        $themeBlogCategory = new ThemeBlogCategory();
        $themeBlogCategory->name = 'Test';
        $themeBlogCategory->blogCategoryId = $category->id;
        $themeBlogCategory->themeId = $this->theme->id;
        $themeBlogCategory->create();

        self::assertNotNull($this->extension->blogCategory('Test'));
    }

    public function testBlogCategoryNotExisting(): void
    {
        $result = $this->extension->blogCategory('Not existing');
        self::assertNull($result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->theme = new Theme();
        $this->theme->name = 'Test theme';
        $this->theme->displayName = 'Test';
        $this->theme->configuration = [];
        $this->theme->scssVariables = [];
        $this->theme->description = [];
        $this->theme->create();

        $this->extension = new LinksExtension($this->theme);
    }
}
