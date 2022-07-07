<?php

namespace Jinya\Tests\Web\Middleware;

use App\Database;
use App\Database\BlogPost;
use App\Database\Menu;
use App\Database\MenuItem;
use App\Database\Theme;
use App\Database\ThemeMenu;
use App\Database\Utils\LoadableEntity;
use App\Tests\TestRequestHandler;
use App\Theming\ThemeSyncer;
use App\Web\Middleware\CheckRouteInCurrentThemeMiddleware;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class CheckRouteInCurrentThemeMiddlewareTest extends TestCase
{
    private string $name;

    public function testProcess(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName('jinya-default-theme');

        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = 'Test';
        $menuItem->position = 0;
        $menuItem->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->name = 'primary';
        $themeMenu->create();

        $request = new ServerRequest('GET', 'http://localhost:8080/test');
        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware = new CheckRouteInCurrentThemeMiddleware();
        $response = $middleware->process($request, $handler);

        self::assertNotEquals(500, $response->getStatusCode());
    }

    public function testProcessActiveThemeNull(): void
    {
        $request = new ServerRequest('GET', 'http://localhost:8080/test');
        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware = new CheckRouteInCurrentThemeMiddleware();
        $response = $middleware->process($request, $handler);
        self::assertEquals(500, $response->getStatusCode());
    }

    public function testProcessErrorBehaviorErrorPage(): void
    {
        $fs = new Filesystem();
        try {
            $this->name = uniqid('unit-test-theme', true);

            $fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . $this->name);

            $themeSyncer = new ThemeSyncer();
            $themeSyncer->syncThemes();

            $dbTheme = Database\Theme::findByName($this->name);

            $menu = new Menu();
            $menu->name = 'Test';
            $menu->create();

            $menuItem = new MenuItem();
            $menuItem->menuId = $menu->getIdAsInt();
            $menuItem->route = 'test';
            $menuItem->title = 'Test';
            $menuItem->position = 0;
            $menuItem->create();

            $themeMenu = new ThemeMenu();
            $themeMenu->themeId = $dbTheme->getIdAsInt();
            $themeMenu->menuId = $menu->getIdAsInt();
            $themeMenu->name = 'menu1';
            $themeMenu->create();

            $dbTheme->makeActiveTheme();

            $request = new ServerRequest('GET', 'http://localhost:8080/test2');
            $response = new Response();
            $handler = new TestRequestHandler($response);
            $middleware = new CheckRouteInCurrentThemeMiddleware();
            $response = $middleware->process($request, $handler);

            self::assertEquals(404, $response->getStatusCode());
        } finally {
            $fs->remove(ThemeSyncer::THEME_BASE_PATH . $this->name);
        }
    }

    public function testProcessErrorBehaviorEmergency(): void
    {
        $fs = new Filesystem();
        try {
            $this->name = uniqid('unit-test-theme', true);

            $fs->mirror(__ROOT__ . '/tests/files/theme/unit-test-theme', ThemeSyncer::THEME_BASE_PATH . $this->name);

            $themeSyncer = new ThemeSyncer();
            $themeSyncer->syncThemes();

            $dbTheme = Database\Theme::findByName($this->name);

            $menu = new Menu();
            $menu->name = 'Test';
            $menu->create();

            $menuItem = new MenuItem();
            $menuItem->menuId = $menu->getIdAsInt();
            $menuItem->route = 'test';
            $menuItem->title = 'Test';
            $menuItem->position = 0;
            $menuItem->create();

            $themeMenu = new ThemeMenu();
            $themeMenu->themeId = $dbTheme->getIdAsInt();
            $themeMenu->menuId = $menu->getIdAsInt();
            $themeMenu->name = 'menu2';
            $themeMenu->create();

            $dbTheme->makeActiveTheme();

            $request = new ServerRequest('GET', 'http://localhost:8080/test2');
            $response = new Response();
            $handler = new TestRequestHandler($response);
            $middleware = new CheckRouteInCurrentThemeMiddleware();
            $response = $middleware->process($request, $handler);

            self::assertEquals(500, $response->getStatusCode());
        } finally {
            $fs->remove(ThemeSyncer::THEME_BASE_PATH . $this->name);
        }
    }

    public function testProcessChildItem(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName('jinya-default-theme');

        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = 'Test';
        $menuItem->position = 0;
        $menuItem->create();

        $menuItem2 = new MenuItem();
        $menuItem2->parentId = $menuItem->getIdAsInt();
        $menuItem2->route = 'foo/bar';
        $menuItem2->title = 'Test';
        $menuItem2->position = 0;
        $menuItem2->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->name = 'primary';
        $themeMenu->create();

        $request = new ServerRequest('GET', 'http://localhost:8080/foo/bar');
        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware = new CheckRouteInCurrentThemeMiddleware();
        $response = $middleware->process($request, $handler);

        self::assertNotEquals(500, $response->getStatusCode());
    }

    public function testProcessRouteIsBlogPost(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName('jinya-default-theme');

        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = 'Test';
        $menuItem->position = 0;
        $menuItem->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->name = 'primary';
        $themeMenu->create();

        $blogPost = new BlogPost();
        $blogPost->title = Uuid::uuid();
        $blogPost->public = true;
        $blogPost->slug = 'test2';
        $blogPost->create();

        $request = new ServerRequest('GET', 'http://localhost:8080/test2');
        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware = new CheckRouteInCurrentThemeMiddleware();
        $response = $middleware->process($request, $handler);

        self::assertNotEquals(500, $response->getStatusCode());
    }

    public function testProcessRouteNotInTheme(): void
    {
        $themeSyncer = new ThemeSyncer();
        $themeSyncer->syncThemes();

        $theme = Theme::findByName('jinya-default-theme');

        $menu = new Menu();
        $menu->name = 'Test';
        $menu->create();

        $menuItem = new MenuItem();
        $menuItem->menuId = $menu->getIdAsInt();
        $menuItem->route = 'test';
        $menuItem->title = 'Test';
        $menuItem->position = 0;
        $menuItem->create();

        $themeMenu = new ThemeMenu();
        $themeMenu->themeId = $theme->getIdAsInt();
        $themeMenu->menuId = $menu->getIdAsInt();
        $themeMenu->name = 'primary';
        $themeMenu->create();

        $request = new ServerRequest('GET', 'http://localhost:8080/test2');
        $response = new Response();
        $handler = new TestRequestHandler($response);
        $middleware = new CheckRouteInCurrentThemeMiddleware();
        $response = $middleware->process($request, $handler);

        self::assertEquals(302, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        LoadableEntity::executeSqlString('DELETE FROM configuration');
        LoadableEntity::executeSqlString('INSERT INTO configuration (current_frontend_theme_id) VALUES (null)');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

    }
}
