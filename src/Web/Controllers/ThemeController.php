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
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Theming\Theme;
use Jinya\Cms\Theming\ThemeSyncer;
use Jinya\Cms\Utils\UuidGenerator;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware;
use Exception;
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use stdClass;
use ZipArchive;

#[Controller]
class ThemeController extends BaseController
{
    private readonly LoggerInterface $logger;

    public function __construct(private readonly ThemeSyncer $themeSyncer = new ThemeSyncer())
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * Uploads a new theme, unzips the posted body and syncs all themes
     *
     * @return ResponseInterface
     * @throws Exception
     */
    #[Route(HttpMethod::POST, '/api/theme')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function uploadTheme(): ResponseInterface
    {
        $themeName = $this->getQueryParameter('name');
        if ($themeName) {
            $tmpFile = __JINYA_TEMP . UuidGenerator::generateV4();
            file_put_contents($tmpFile, $this->request->getBody()->detach());

            $zipArchive = new ZipArchive();
            $openRes = $zipArchive->open($tmpFile);
            if ($openRes !== true) {
                $this->logger->error(
                    'The zip could not be opened. Check the docs here, https://www.php.net/manual/en/ziparchive.open.php'
                );
                $this->logger->error('Error message: ' . $zipArchive->getStatusString());
                $this->logger->error("Errorcode: $openRes");

                return $this->json([
                    'success' => false,
                    'error' => [
                        'message' => 'Failed to open the zip file',
                        'type' => 'open-failed',
                    ],
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
            if (!$zipArchive->extractTo(ThemeSyncer::THEME_BASE_PATH . $themeName)) {
                $this->logger->error('The zip could not be extracted');
                $this->logger->error('Error message: ' . $zipArchive->getStatusString());

                return $this->json([
                    'success' => false,
                    'error' => [
                        'message' => 'Failed to extract the zip file',
                        'type' => 'extract-failed',
                    ],
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
            $this->themeSyncer->syncThemes();

            unlink($tmpFile);
        }

        return $this->noContent();
    }

    /**
     * Updates the given theme, from the zip file in the body
     *
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     * @throws Exception
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function updateTheme(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if ($theme) {
            $tmpFile = __JINYA_TEMP . UuidGenerator::generateV4();
            file_put_contents($tmpFile, $this->request->getBody()->detach());

            $zipArchive = new ZipArchive();
            $openRes = $zipArchive->open($tmpFile);
            if ($openRes !== true) {
                $this->logger->error(
                    'The zip could not be opened. Check the docs here, https://www.php.net/manual/en/ziparchive.open.php'
                );
                $this->logger->error('Error message: ' . $zipArchive->getStatusString());
                $this->logger->error("Errorcode: $openRes");

                return $this->json([
                    'success' => false,
                    'error' => [
                        'message' => 'Failed to open the zip file',
                        'type' => 'open-failed',
                    ],
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
            if (!$zipArchive->extractTo(ThemeSyncer::THEME_BASE_PATH . $theme->name)) {
                $this->logger->error('The zip could not be extracted');
                $this->logger->error('Error message: ' . $zipArchive->getStatusString());

                return $this->json([
                    'success' => false,
                    'error' => [
                        'message' => 'Failed to extract the zip file',
                        'type' => 'extract-failed',
                    ],
                ], self::HTTP_INTERNAL_SERVER_ERROR);
            }
            $themeSyncer = new ThemeSyncer();
            $themeSyncer->syncThemes();

            unlink($tmpFile);

            return $this->noContent();
        }

        return $this->entityNotFound('Theme not found');
    }

    /**
     * Activates the given theme
     *
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/active')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function activateTheme(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme->makeActiveTheme();

        return $this->noContent();
    }

    /**
     * Compiles all theme assets
     *
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     * @throws Exception
     * @throws Exception
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/assets')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function compileTheme(int $id): ResponseInterface
    {
        $this->themeSyncer->syncThemes();
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme = new Theme($dbTheme);
        $theme->compileAssetCache();
        $theme->compileStyleCache();
        $theme->compileScriptCache();

        return $this->noContent();
    }

    /**
     * Updates the theme configuration for the given theme. Before an update is made, the themes will be synced to have the latest state
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/configuration')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function updateConfiguration(int $id): ResponseInterface
    {
        $this->themeSyncer->syncThemes();
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $config = $this->body['configuration'];

        $theme->configuration = $config;
        $theme->update();

        return $this->noContent();
    }

    /**
     * Gets the structure of the configuration for the given theme
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/configuration/structure')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getConfigurationStructure(int $id): ResponseInterface
    {
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme = new Theme($dbTheme);

        return $this->json($theme->getConfigurationStructure());
    }

    /**
     * Gets the default configuration values by theme ID
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/configuration/default')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getDefaultConfiguration(int $id): ResponseInterface
    {
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme = new Theme($dbTheme);
        $config = $theme->getConfigurationValues();

        return $this->json($config);
    }

    /**
     * Gets the SCSS variables from the given theme
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/styling')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getStyleVariables(int $id): ResponseInterface
    {
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme = new Theme($dbTheme);
        $vars = $theme->getStyleVariables();
        $dbVars = $dbTheme->scssVariables;

        return $this->json(array_merge($vars, $dbVars));
    }

    /**
     * Updates the SCSS variable for the given theme from the body
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/styling')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function updateStyleVariables(int $id): ResponseInterface
    {
        $this->themeSyncer->syncThemes();
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $variables = $this->body['variables'];

        $dbTheme->scssVariables = $variables;
        $dbTheme->update();

        $theme = new Theme($dbTheme);
        $theme->compileStyleCache();

        return $this->noContent();
    }

    /**
     * Gets the theme preview image, if no preview image is configured a 204 is returned
     *
     * @param int $id
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/preview')]
    public function getPreviewImage(int $id): ResponseInterface
    {
        $dbTheme = DatabaseTheme::findById($id);
        if (!$dbTheme) {
            return $this->entityNotFound('Theme not found');
        }

        $theme = new Theme($dbTheme);
        if (file_exists($theme->getPreviewImagePath())) {
            return new Response(
                self::HTTP_OK,
                ['Content-Type' => mime_content_type($theme->getPreviewImagePath()) ?: 'application/octet-stream'],
                Stream::create(fopen($theme->getPreviewImagePath(), 'rb') ?: '')
            );
        }

        return $this->noContent();
    }

    /**
     * @param array<string, BlogCategory|ClassicPage|File|Form|Gallery|Menu|ModernPage> $links
     * @return ResponseInterface
     * @throws JsonException
     * @throws Exception
     */
    private function formatThemeLinks(array $links): ResponseInterface
    {
        $result = [];

        foreach ($links as $key => $link) {
            $result[$key] = $link->format();
        }

        if (empty($result)) {
            $result = new stdClass();
        }

        return $this->json($result);
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/blog-category')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeBlogCategories(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getCategories();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/blog-category/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['blogCategory']))]
    public function updateThemeBlogCategory(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeBlogCategory::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->blogCategoryId = $this->body['blogCategory'];
                $link->update();
            } else {
                $link = new ThemeBlogCategory();
                $link->name = $name;
                $link->themeId = $id;
                $link->blogCategoryId = $this->body['blogCategory'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Blog category not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/classic-page')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeClassicPages(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getClassicPages();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/classic-page/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['classicPage']))]
    public function updateThemeClassicPage(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeClassicPage::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->classicPageId = $this->body['classicPage'];
                $link->update();
            } else {
                $link = new ThemeClassicPage();
                $link->name = $name;
                $link->themeId = $id;
                $link->classicPageId = $this->body['classicPage'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Classic page not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/file')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeFiles(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getFiles();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/file/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['file']))]
    public function updateThemeFile(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeFile::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->fileId = $this->body['file'];
                $link->update();
            } else {
                $link = new ThemeFile();
                $link->name = $name;
                $link->themeId = $id;
                $link->fileId = $this->body['file'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('File not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/form')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeForms(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getForms();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/form/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['form']))]
    public function updateThemeForm(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeForm::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->formId = $this->body['form'];
                $link->update();
            } else {
                $link = new ThemeForm();
                $link->name = $name;
                $link->themeId = $id;
                $link->formId = $this->body['form'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Form not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/gallery')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeGalleries(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getGalleries();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/gallery/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['gallery']))]
    public function updateThemeGallery(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeGallery::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->galleryId = $this->body['gallery'];
                $link->update();
            } else {
                $link = new ThemeGallery();
                $link->name = $name;
                $link->themeId = $id;
                $link->galleryId = $this->body['gallery'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Gallery not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/menu')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeMenus(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getMenus();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/menu/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['menu']))]
    public function updateThemeMenu(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeMenu::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->menuId = $this->body['menu'];
                $link->update();
            } else {
                $link = new ThemeMenu();
                $link->name = $name;
                $link->themeId = $id;
                $link->menuId = $this->body['menu'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Menu not found');
        }

        return $this->noContent();
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/theme/{id}/modern-page')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function getThemeModernPages(int $id): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $links = $theme->getModernPages();

        return $this->formatThemeLinks($links);
    }

    /**
     * @param int $id
     * @param string $name
     * @return ResponseInterface
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/theme/{id}/modern-page/{name}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER), new CheckRequiredFieldsMiddleware(['modernPage']))]
    public function updateThemeModernPage(int $id, string $name): ResponseInterface
    {
        $theme = DatabaseTheme::findById($id);
        if (!$theme) {
            return $this->entityNotFound('Theme not found');
        }

        $link = ThemeModernPage::findByThemeAndName($id, $name);
        try {
            if ($link) {
                $link->modernPageId = $this->body['modernPage'];
                $link->update();
            } else {
                $link = new ThemeModernPage();
                $link->name = $name;
                $link->themeId = $id;
                $link->modernPageId = $this->body['modernPage'];
                $link->create();
            }
        } catch (ForeignKeyFailedException) {
            return $this->entityNotFound('Modern page not found');
        }

        return $this->noContent();
    }
}
