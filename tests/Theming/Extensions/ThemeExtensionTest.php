<?php

namespace Jinya\Cms\Theming\Extensions;

use Jinya\Cms\Database;
use Jinya\Cms\Tests\DatabaseAwareTestCase;
use Jinya\Cms\Theming;
use Jinya\Cms\Theming\ThemeSyncer;
use Faker\Provider\Uuid;
use Symfony\Component\Filesystem\Filesystem;

class ThemeExtensionTest extends DatabaseAwareTestCase
{
    private static string $name;
    private static Filesystem $fs;
    private ThemeExtension $extension;
    private Theming\Theme $theme;
    private Database\Theme $dbTheme;

    public function testRegister(): void
    {
        $engine = Theming\Engine::getPlatesEngine();
        $this->extension->register($engine);

        self::assertTrue($engine->functions->exists('asset'));
        self::assertTrue($engine->functions->exists('config'));
        self::assertTrue($engine->functions->exists('styles'));
        self::assertTrue($engine->functions->exists('scripts'));
    }

    public function testScripts(): void
    {
        $this->theme->compileScriptCache();
        self::assertStringContainsString('.js', $this->extension->scripts());
        self::assertStringContainsString('<script', $this->extension->scripts());
    }

    public function testScriptsUnCompiled(): void
    {
        self::assertEmpty($this->extension->scripts());
    }

    public function testStyles(): void
    {
        $this->theme->compileAssetCache();
        $this->theme->compileStyleCache();
        self::assertStringContainsString('.css', $this->extension->styles());
        self::assertStringContainsString('<link', $this->extension->styles());
    }

    public function testStylesUnCompiled(): void
    {
        self::assertEmpty($this->extension->styles());
    }

    public function testConfigDefaultValue(): void
    {
        self::assertEquals('Text value', $this->extension->config('configGroup1', 'text'));
        self::assertFalse($this->extension->config('configGroup2', 'boolean1'));
    }

    public function testConfigChangedValue(): void
    {
        $this->dbTheme->configuration = [
            'configGroup1' => [
                'text' => 'Foo',
            ],
            'configGroup2' => [
                'boolean1' => true,
            ],
        ];
        $this->dbTheme->update();

        self::assertEquals('Foo', $this->extension->config('configGroup1', 'text'));
        self::assertTrue($this->extension->config('configGroup2', 'boolean1'));
    }

    public function testAsset(): void
    {
        $this->theme->compileAssetCache();
        $asset = $this->extension->asset('poppins400Regular');
        self::assertEquals('poppins400Regular', $asset->name);
    }

    public function testAssetNonExistent(): void
    {
        $this->theme->compileAssetCache();
        $asset = $this->extension->asset('non-existent');
        self::assertNull($asset);
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$name = Uuid::uuid();
        self::$fs = new Filesystem();
        self::$fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . self::$name);

        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $this->dbTheme = Database\Theme::findByName(self::$name);
        $this->theme = new Theming\Theme($this->dbTheme);
        $this->extension = new ThemeExtension($this->theme, $this->dbTheme);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::$fs->remove(ThemeSyncer::THEME_BASE_PATH . self::$name);
        self::$fs->remove(__ROOT__ . '/public/themes/' . self::$name);
    }
}
