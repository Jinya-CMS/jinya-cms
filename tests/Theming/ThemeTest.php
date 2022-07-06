<?php

namespace Jinya\Tests\Theming;

use App\Database;
use App\Theming;
use App\Theming\ThemeSyncer;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

class ThemeTest extends TestCase
{
    private static bool $existedBeforeTest = false;
    private static string $name;
    private static Filesystem $fs;
    private Theming\Theme $theme;
    private Database\Theme $dbTheme;

    public function testCompileScriptCache(): void
    {
        $this->theme->compileScriptCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/*'));


        // Compile it twice to test for recreation
        $this->theme->compileScriptCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/scripts/*'));
    }

    public function testCompileAssetCache(): void
    {
        $this->theme->compileAssetCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets/*'));

        // Compile it again to test update mechanism
        $this->theme->compileAssetCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets/*'));

        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name . '/assets/fonts/Poppins.latin.regular.woff2');
        // Compile it again to test update mechanism with missing file
        $this->theme->compileAssetCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets');
        self::assertEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/assets/*'));
    }

    public function testGetErrorBehavior(): void
    {
        self::assertEquals(Theming\Theme::ERROR_BEHAVIOR_ERROR_PAGE, $this->theme->getErrorBehavior());
    }

    public function testGetConfigurationValues(): void
    {
        $config = $this->theme->getConfigurationValues();
        self::assertEquals([
            'configGroup1' => [
                'text' => 'Text value',
            ],
            'configGroup2' => [
                'text1' => 'Text value',
                'text2' => 'Text value',
                'boolean1' => false,
            ],
        ], $config);
    }

    public function testGetPreviewImagePath(): void
    {
        $previewImagePath = $this->theme->getPreviewImagePath();
        self::assertFileEquals(ThemeSyncer::THEME_BASE_PATH . self::$name . '/Preview.jpg', $previewImagePath);
    }

    public function testCompileStyleCache(): void
    {
        $this->theme->compileAssetCache();
        $this->theme->compileStyleCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/*'));

        // Compile it twice to test for recreation
        $this->theme->compileStyleCache();
        self::assertDirectoryExists(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles');
        self::assertNotEmpty(glob(Theming\Theme::BASE_CACHE_PATH . $this->dbTheme->name . '/styles/*'));
    }

    public function testRegister(): void
    {
        $engine = Theming\Engine::getPlatesEngine();
        $this->theme->register($engine);
        self::assertTrue($engine->getFolders()->exists('theme'));
    }

    public function test__construct(): void
    {
        $theme = new Theming\Theme($this->dbTheme);
        self::assertNotNull($theme);
    }

    public function test__constructNoThemePhp(): void
    {
        $this->expectException(RuntimeException::class);
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name . '/theme.php');
        new Theming\Theme($this->dbTheme);
    }

    public function testGetConfigurationStructure(): void
    {
        $structure = $this->theme->getConfigurationStructure();
        self::assertEquals([
            'title' => 'Configure Jinya testing theme',
            'groups' => [
                [
                    'name' => 'configGroup1',
                    'title' => 'Config Group 1',
                    'fields' => [
                        [
                            'name' => 'text',
                            'type' => 'string',
                            'label' => 'Text value 1',
                        ],
                    ],
                ],
                [
                    'name' => 'configGroup2',
                    'title' => 'Config Group 2',
                    'fields' => [
                        [
                            'name' => 'text1',
                            'type' => 'string',
                            'label' => 'Text value 1',
                        ],
                        [
                            'name' => 'text2',
                            'type' => 'string',
                            'label' => 'Text value 2',
                        ],
                        [
                            'name' => 'boolean1',
                            'type' => 'boolean',
                            'label' => 'Boolean value 1',
                        ],
                    ],
                ],
            ],
            'links' => [
                'segment_pages' => [
                    'segmentPage1' => 'Segment Page 1',
                    'segmentPage2' => 'Segment Page 2',
                    'segmentPage3' => 'Segment Page 3',
                ],
                'menus' => [
                    'menu1' => 'Menu 1',
                    'menu2' => 'Menu 2',
                    'menu3' => 'Menu 3',
                ],
                'pages' => [
                    'page1' => 'Page 1',
                    'page2' => 'Page 2',
                    'page3' => 'Page 3',
                ],
                'forms' => [
                    'form1' => 'Form 1',
                    'form2' => 'Form 2',
                    'form3' => 'Form 3',
                ],
                'galleries' => [
                    'gallery1' => 'Gallery 1',
                    'gallery2' => 'Gallery 2',
                    'gallery3' => 'Gallery 3',
                ],
                'files' => [
                    'file1' => 'File 1',
                    'file2' => 'File 2',
                    'file3' => 'File 3',
                ],
                'blog_categories' => [
                    'blogCategory1' => 'Category 1',
                    'blogCategory2' => 'Category 2',
                    'blogCategory3' => 'Category 3',
                ],
            ],
        ], $structure);
    }

    public function testGetStyleVariables(): void
    {
        $variables = $this->theme->getStyleVariables();
        self::assertArrayHasKey('$jinya-debug', $variables);
        self::assertEquals('false', $variables['$jinya-debug']);
    }

    public function testScssJinyaAsset(): void
    {
        $this->dbTheme->makeActiveTheme();
        $this->theme->compileAssetCache();
        $asset = $this->theme->scssJinyaAsset([['string', '', ['poppins400Regular']]]);
        self::assertNotNull($asset);
    }

    public function testScssJinyaAssetNotFound(): void
    {
        $this->expectException(RuntimeException::class);
        $this->dbTheme->makeActiveTheme();
        $this->theme->compileAssetCache();
        $asset = $this->theme->scssJinyaAsset([['string', '', ['not-existing.woff2']]]);
        self::assertNotNull($asset);
    }

    public function testVariablesNotFound(): void
    {
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name . '/styles/_variables.scss');
        $result = $this->theme->getStyleVariables();
        self::assertEmpty($result);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$name = uniqid('unit-test-theme', true);
        self::$fs = new Filesystem();

        if (self::$fs->exists(ThemeSyncer::THEME_BASE_PATH . self::$name)) {
            self::$existedBeforeTest = true;
        } else {
            self::$fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . self::$name);
        }

        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $this->dbTheme = Database\Theme::findByName(self::$name);
        $this->theme = new Theming\Theme($this->dbTheme);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (!self::$existedBeforeTest) {
            self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
            self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);
        }
    }
}
