<?php

namespace Jinya\Tests\Theming\Extensions;

use App\Database\BlogCategory;
use App\Database\File;
use App\Database\Form;
use App\Database\Gallery;
use App\Database\Menu;
use App\Database\SegmentPage;
use App\Database\SimplePage;
use App\Database\Theme;
use App\Database\ThemeBlogCategory;
use App\Database\ThemeFile;
use App\Database\ThemeForm;
use App\Database\ThemeGallery;
use App\Database\ThemeMenu;
use App\Database\ThemePage;
use App\Database\ThemeSegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Theming\Engine;
use App\Theming\Extensions\LinksExtension;
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
        $themeGallery->galleryId = $gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
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
        $themeBlogCategory->blogCategoryId = $category->getIdAsInt();
        $themeBlogCategory->themeId = $this->theme->getIdAsInt();
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
        $themeGallery->galleryId = $gallery->getIdAsInt();
        $themeGallery->themeId = $this->theme->getIdAsInt();
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
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->themeId = $this->theme->getIdAsInt();
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
        $themeForm->formId = $form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
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
        $page = new SegmentPage();
        $page->name = 'Temp';
        $page->create();

        $themePage = new ThemeSegmentPage();
        $themePage->name = 'Test';
        $themePage->segmentPageId = $page->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
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
        $themeForm->formId = $form->getIdAsInt();
        $themeForm->themeId = $this->theme->getIdAsInt();
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

        self::assertTrue($engine->doesFunctionExist('segmentPage'));
        self::assertTrue($engine->doesFunctionExist('page'));
        self::assertTrue($engine->doesFunctionExist('simplePage'));
        self::assertTrue($engine->doesFunctionExist('file'));
        self::assertTrue($engine->doesFunctionExist('menu'));
        self::assertTrue($engine->doesFunctionExist('gallery'));
        self::assertTrue($engine->doesFunctionExist('form'));
        self::assertTrue($engine->doesFunctionExist('blogCategory'));

        self::assertTrue($engine->doesFunctionExist('hasSegmentPage'));
        self::assertTrue($engine->doesFunctionExist('hasPage'));
        self::assertTrue($engine->doesFunctionExist('hasSimplePage'));
        self::assertTrue($engine->doesFunctionExist('hasFile'));
        self::assertTrue($engine->doesFunctionExist('hasGallery'));
        self::assertTrue($engine->doesFunctionExist('hasMenu'));
        self::assertTrue($engine->doesFunctionExist('hasForm'));
        self::assertTrue($engine->doesFunctionExist('hasBlogCategory'));
    }

    public function testSimplePage(): void
    {
        $page = new SimplePage();
        $page->title = 'Temp';
        $page->content = '';
        $page->create();

        $themePage = new ThemePage();
        $themePage->name = 'Test';
        $themePage->pageId = $page->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
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
        $page = new SegmentPage();
        $page->name = 'Temp';
        $page->create();

        $themePage = new ThemeSegmentPage();
        $themePage->name = 'Test';
        $themePage->segmentPageId = $page->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
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
        $page = new SimplePage();
        $page->title = 'Temp';
        $page->content = '';
        $page->create();

        $themePage = new ThemePage();
        $themePage->name = 'Test';
        $themePage->pageId = $page->getIdAsInt();
        $themePage->themeId = $this->theme->getIdAsInt();
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
        $themeFile->fileId = $file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
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
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->themeId = $this->theme->getIdAsInt();
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
        $themeFile->fileId = $file->getIdAsInt();
        $themeFile->themeId = $this->theme->getIdAsInt();
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
        $themeBlogCategory->blogCategoryId = $category->getIdAsInt();
        $themeBlogCategory->themeId = $this->theme->getIdAsInt();
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->theme->delete();
    }
}
