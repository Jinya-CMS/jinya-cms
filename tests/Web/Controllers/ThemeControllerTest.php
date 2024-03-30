<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\BlogCategory;
use Jinya\Cms\Database\ClassicPage;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\Form;
use Jinya\Cms\Database\Gallery;
use Jinya\Cms\Database\Menu;
use Jinya\Cms\Database\ModernPage;
use Jinya\Cms\Database\Theme as DatabaseTheme;
use Jinya\Cms\Database\ThemeBlogCategory;
use Jinya\Cms\Database\ThemeClassicPage;
use Jinya\Cms\Database\ThemeFile;
use Jinya\Cms\Database\ThemeForm;
use Jinya\Cms\Database\ThemeGallery;
use Jinya\Cms\Database\ThemeMenu;
use Jinya\Cms\Database\ThemeModernPage;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming\Theme;
use Jinya\Cms\Theming\ThemeSyncer;
use Jinya\Cms\Utils\UuidGenerator;
use Faker\Provider\Uuid;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use Symfony\Component\Filesystem\Filesystem;

class ThemeControllerTest extends DatabaseAwareTestCase
{
    private function getDefaultTheme(): ?DatabaseTheme
    {
        $syncer = new ThemeSyncer();
        $syncer->syncThemes();

        return DatabaseTheme::findByName('jinya-default-theme');
    }

    private function getController(mixed $body, bool $binaryBody = false, array $queryParams = []): ThemeController
    {
        $controller = new ThemeController();
        if ($binaryBody) {
            $body->rewind();
            $controller->request = (new ServerRequest('', ''))->withBody($body);
        } else {
            $controller->body = $body;
            $controller->request = (new ServerRequest('', ''))->withParsedBody($body);
        }
        $controller->request = $controller->request->withQueryParams($queryParams);

        return $controller;
    }

    public function testActivateTheme(): void
    {
        $controller = $this->getController([]);
        $result = $controller->activateTheme($this->getDefaultTheme()->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testActivateThemeNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->activateTheme(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testCompileTheme(): void
    {
        $controller = $this->getController([]);
        $result = $controller->compileTheme($this->getDefaultTheme()->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testCompileThemeNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->compileTheme(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testCreateTheme(): void
    {
        $name = UuidGenerator::generateV4();
        try {
            $controller = $this->getController(
                Stream::create(fopen(__DIR__ . '/../../files/unit-test-theme.zip', 'rb+')),
                true,
                ['name' => $name]
            );
            $result = $controller->uploadTheme();

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $fs = new Filesystem();
            $fs->remove(__ROOT__ . '/themes/' . $name);
        }
    }

    public function testCreateThemeInvalidBody(): void
    {
        $name = UuidGenerator::generateV4();
        try {
            $controller = $this->getController(
                Stream::create('invalid-zip-data'),
                true,
                ['name' => $name]
            );
            $result = $controller->uploadTheme();
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(500, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Failed to open the zip file',
                    'type' => 'open-failed',
                ],
            ], $body);
        } finally {
            $fs = new Filesystem();
            $fs->remove(__ROOT__ . '/themes/' . $name);
        }
    }

    public function testUpdateTheme(): void
    {
        $theme = new DatabaseTheme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->hasApiTheme = true;
        $theme->create();

        try {
            $controller = $this->getController(
                Stream::create(fopen(__DIR__ . '/../../files/unit-test-theme.zip', 'rb+')),
                true
            );
            $result = $controller->updateTheme($theme->id);

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $fs = new Filesystem();
            $fs->remove(__ROOT__ . '/themes/' . $theme->name);
        }
    }

    public function testUpdateThemeInvalidBody(): void
    {
        $theme = new DatabaseTheme();
        $theme->name = Uuid::uuid();
        $theme->displayName = Uuid::uuid();
        $theme->description = ['en' => Uuid::uuid()];
        $theme->scssVariables = [];
        $theme->configuration = [];
        $theme->hasApiTheme = true;
        $theme->create();

        try {
            $controller = $this->getController(
                Stream::create('invalid-zip-data'),
                true,
            );
            $result = $controller->updateTheme($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(500, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Failed to open the zip file',
                    'type' => 'open-failed',
                ],
            ], $body);
        } finally {
            $fs = new Filesystem();
            $fs->remove(__ROOT__ . '/themes/' . $theme->name);
        }
    }

    public function testUpdateThemeNotFound(): void
    {
        $name = UuidGenerator::generateV4();
        $controller = $this->getController(
            Stream::create('invalid-zip-data'),
            true,
        );
        $result = $controller->updateTheme(-1);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetPreviewImage(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getPreviewImage($this->getDefaultTheme()->id);
        $result->getBody()->rewind();
        $theme = new Theme($this->getDefaultTheme());

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals(mime_content_type($theme->getPreviewImagePath()), $result->getHeaderLine('Content-Type'));
        self::assertEquals(
            hash_file('sha256', $theme->getPreviewImagePath()),
            hash('sha256', $result->getBody()->getContents())
        );
    }

    public function testGetPreviewImageNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getPreviewImage(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetConfigurationStructure(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getConfigurationStructure($this->getDefaultTheme()->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $theme = new Theme($this->getDefaultTheme());

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($theme->getConfigurationStructure(), $body);
    }

    public function testGetConfigurationStructureNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getConfigurationStructure(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetDefaultConfiguration(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getDefaultConfiguration($this->getDefaultTheme()->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $theme = new Theme($this->getDefaultTheme());

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($theme->getConfigurationValues(), $body);
    }

    public function testGetDefaultConfigurationNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getDefaultConfiguration(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testGetStyleVariables(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getStyleVariables($this->getDefaultTheme()->id);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $theme = new Theme($this->getDefaultTheme());

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($theme->getStyleVariables(), $body);
    }

    public function testGetStyleVariablesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getStyleVariables(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateConfiguration(): void
    {
        $controller = $this->getController([
            'configuration' => [
                'test' => true
            ]
        ]);
        $result = $controller->updateConfiguration($this->getDefaultTheme()->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdateConfigurationNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateConfiguration(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateStyleVariables(): void
    {
        $controller = $this->getController([
            'variables' => [
                'test' => 'true'
            ]
        ]);
        $result = $controller->updateStyleVariables($this->getDefaultTheme()->id);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function testUpdateStyleVariablesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateStyleVariables(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    private function recurseCopy(string $src, string $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function prepareLinks(): DatabaseTheme
    {
        $this->recurseCopy(__ROOT__ . '/tests/files/theme/unit-test-theme', __ROOT__ . '/themes/unit-test-theme');
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = DatabaseTheme::findByName('unit-test-theme');

        $blogCategory = new BlogCategory();
        $blogCategory->name = Uuid::Uuid();
        $blogCategory->create();

        $themeBlogCategory = new ThemeBlogCategory();
        $themeBlogCategory->name = 'blogCategory1';
        $themeBlogCategory->blogCategoryId = $blogCategory->id;
        $themeBlogCategory->themeId = $theme->id;
        $themeBlogCategory->create();

        $classicPage = new ClassicPage();
        $classicPage->title = Uuid::Uuid();
        $classicPage->content = Uuid::Uuid();
        $classicPage->create();

        $themeClassicPage = new ThemeClassicPage();
        $themeClassicPage->name = 'classicPage1';
        $themeClassicPage->classicPageId = $classicPage->id;
        $themeClassicPage->themeId = $theme->id;
        $themeClassicPage->create();

        $file = new File();
        $file->name = Uuid::Uuid();
        $file->create();

        $themeFile = new ThemeFile();
        $themeFile->name = 'file1';
        $themeFile->fileId = $file->id;
        $themeFile->themeId = $theme->id;
        $themeFile->create();

        $form = new Form();
        $form->title = Uuid::Uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $themeForm = new ThemeForm();
        $themeForm->name = 'form1';
        $themeForm->formId = $form->id;
        $themeForm->themeId = $theme->id;
        $themeForm->create();

        $gallery = new Gallery();
        $gallery->name = Uuid::Uuid();
        $gallery->create();

        $themeGallery = new ThemeGallery();
        $themeGallery->name = 'gallery1';
        $themeGallery->galleryId = $gallery->id;
        $themeGallery->themeId = $theme->id;
        $themeGallery->create();

        $menu = new Menu();
        $menu->name = Uuid::Uuid();
        $menu->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->name = 'menu1';
        $themeMenu->menuId = $menu->id;
        $themeMenu->themeId = $theme->id;
        $themeMenu->create();

        $modernPage = new ModernPage();
        $modernPage->name = Uuid::Uuid();
        $modernPage->create();

        $themeModernPage = new ThemeModernPage();
        $themeModernPage->name = 'modernPage1';
        $themeModernPage->modernPageId = $modernPage->id;
        $themeModernPage->themeId = $theme->id;
        $themeModernPage->create();

        return $theme;
    }

    private function resetLinks(): void
    {
        $fs = new Filesystem();
        $fs->remove(__ROOT__ . '/themes/unit-test-theme');
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();
    }

    public function testGetThemeBlogCategories(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeBlogCategories($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeBlogCategoriesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeBlogCategories(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeBlogCategory(): void
    {
        $blogCategory = new BlogCategory();
        $blogCategory->name = Uuid::Uuid();
        $blogCategory->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['blogCategory' => $blogCategory->id]);

        try {
            $result = $controller->updateThemeBlogCategory($theme->id, 'blogCategory1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeBlogCategory($theme->id, 'blogCategory2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeBlogCategoryThemeNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeBlogCategory(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeBlogCategoryBlogCategoryNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['blogCategory' => -1]);

        try {
            $result = $controller->updateThemeBlogCategory($theme->id, 'blogCategory1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Blog category not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeBlogCategory($theme->id, 'blogCategory2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Blog category not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeClassicPages(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeClassicPages($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeClassicPagesThemeNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeClassicPages(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeClassicPage(): void
    {
        $classicPage = new ClassicPage();
        $classicPage->title = Uuid::Uuid();
        $classicPage->content = Uuid::Uuid();
        $classicPage->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['classicPage' => $classicPage->id]);

        try {
            $result = $controller->updateThemeClassicPage($theme->id, 'page1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeClassicPage($theme->id, 'page2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeClassicPageNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeClassicPage(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeClassicPageClassicPageNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['classicPage' => -1]);

        try {
            $result = $controller->updateThemeClassicPage($theme->id, 'classicPage1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Classic page not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeClassicPage($theme->id, 'classicPage2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Classic page not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeFiles(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeFiles($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeFilesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeFiles(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeFile(): void
    {
        $file = new File();
        $file->name = Uuid::Uuid();
        $file->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['file' => $file->id]);

        try {
            $result = $controller->updateThemeFile($theme->id, 'file1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeFile($theme->id, 'file2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeFileNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeFile(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeFileFileNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['file' => -1]);

        try {
            $result = $controller->updateThemeFile($theme->id, 'file1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'File not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeFile($theme->id, 'file2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'File not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeForms(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeForms($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeFormsNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeForms(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeForm(): void
    {
        $form = new Form();
        $form->title = Uuid::Uuid();
        $form->toAddress = 'test@example.com';
        $form->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['form' => $form->id]);

        try {
            $result = $controller->updateThemeForm($theme->id, 'form1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeForm($theme->id, 'form2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeFormNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeForm(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeFormFormNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['form' => -1]);

        try {
            $result = $controller->updateThemeForm($theme->id, 'form1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Form not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeForm($theme->id, 'form2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Form not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeGalleries(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeGalleries($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeGalleriesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeGalleries(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeGallery(): void
    {
        $gallery = new Gallery();
        $gallery->name = Uuid::Uuid();
        $gallery->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['gallery' => $gallery->id]);

        try {
            $result = $controller->updateThemeGallery($theme->id, 'gallery1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeGallery($theme->id, 'gallery2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeGalleryNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeGallery(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeGalleryGalleryNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['gallery' => -1]);

        try {
            $result = $controller->updateThemeGallery($theme->id, 'gallery1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Gallery not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeGallery($theme->id, 'gallery2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Gallery not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeMenus(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeMenus($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeMenusNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeMenus(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeMenu(): void
    {
        $menu = new Menu();
        $menu->name = Uuid::Uuid();
        $menu->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['menu' => $menu->id]);

        try {
            $result = $controller->updateThemeMenu($theme->id, 'menu1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeMenu($theme->id, 'menu2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeMenuNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeMenu(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeMenuMenuNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['menu' => -1]);

        try {
            $result = $controller->updateThemeMenu($theme->id, 'menu1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Menu not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeMenu($theme->id, 'menu2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Menu not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeModernPages(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController([]);

        try {
            $result = $controller->getThemeModernPages($theme->id);
            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(200, $result->getStatusCode());
            self::assertCount(1, $body);
        } finally {
            $this->resetLinks();
        }
    }

    public function testGetThemeModernPagesNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->getThemeModernPages(-1);

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeModernPage(): void
    {
        $modernPage = new ModernPage();
        $modernPage->name = Uuid::Uuid();
        $modernPage->create();

        $theme = $this->prepareLinks();
        $controller = $this->getController(['modernPage' => $modernPage->id]);

        try {
            $result = $controller->updateThemeModernPage($theme->id, 'modernPage1');

            self::assertEquals(204, $result->getStatusCode());

            $result = $controller->updateThemeModernPage($theme->id, 'modernPage2');

            self::assertEquals(204, $result->getStatusCode());
        } finally {
            $this->resetLinks();
        }
    }

    public function testUpdateThemeModernPageNotFound(): void
    {
        $controller = $this->getController([]);
        $result = $controller->updateThemeModernPage(-1, '');

        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(404, $result->getStatusCode());
        self::assertEquals([
            'success' => false,
            'error' => [
                'message' => 'Theme not found',
                'type' => 'not-found',
            ],
        ], $body);
    }

    public function testUpdateThemeModernPageModernPageNotFound(): void
    {
        $theme = $this->prepareLinks();
        $controller = $this->getController(['modernPage' => -1]);

        try {
            $result = $controller->updateThemeModernPage($theme->id, 'modernPage1');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Modern page not found',
                    'type' => 'not-found',
                ],
            ], $body);

            $result = $controller->updateThemeModernPage($theme->id, 'modernPage2');

            $result->getBody()->rewind();
            $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            self::assertEquals(404, $result->getStatusCode());
            self::assertEquals([
                'success' => false,
                'error' => [
                    'message' => 'Modern page not found',
                    'type' => 'not-found',
                ],
            ], $body);
        } finally {
            $this->resetLinks();
        }
    }
}
