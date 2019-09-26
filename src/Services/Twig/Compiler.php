<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:22
 */

namespace Jinya\Services\Twig;

use Jinya\Entity\Theme\Theme;
use Jinya\Entity\Theme\ThemeArtGallery;
use Jinya\Entity\Theme\ThemeArtwork;
use Jinya\Entity\Theme\ThemeFile;
use Jinya\Entity\Theme\ThemeForm;
use Jinya\Entity\Theme\ThemeGallery;
use Jinya\Entity\Theme\ThemeMenu;
use Jinya\Entity\Theme\ThemePage;
use Jinya\Entity\Theme\ThemeSegmentPage;
use Jinya\Entity\Theme\ThemeVideoGallery;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeLinkServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Underscore\Types\Strings;

class Compiler implements CompilerInterface
{
    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;

    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var ThemeLinkServiceInterface */
    private $themeLinkService;

    /** @var Environment */
    private $twig;

    /**
     * Compiler constructor.
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param ThemeLinkServiceInterface $themeLinkService
     * @param Environment $twig
     */
    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ThemeCompilerServiceInterface $themeCompilerService,
        ThemeConfigServiceInterface $themeConfigService,
        ThemeServiceInterface $themeService,
        ThemeLinkServiceInterface $themeLinkService,
        Environment $twig
    ) {
        $this->configurationService = $configurationService;
        $this->themeCompilerService = $themeCompilerService;
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->themeLinkService = $themeLinkService;
        $this->twig = $twig;
    }

    /**
     * Compiles the given template with the given context
     *
     * @param string $path
     * @param array $context
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function compile(string $path, array $context): string
    {
        $currentTheme = $this->configurationService->getConfig()->getCurrentTheme();
        $parameters = $context;

        if (Strings::find($path, '@Theme')) {
            [$themeViewPath, $parameters] = $this->includeTheme($path, $context, $currentTheme);

            if ($this->twig->getLoader()->exists($themeViewPath)) {
                $renderPath = $themeViewPath;
            } else {
                [$renderPath, $parameters] = $this->includeTheme(
                    '@Theme/Default/index.html.twig',
                    $context,
                    $currentTheme
                );
            }
        } else {
            $renderPath = $path;
        }

        return $this->twig->render($renderPath, $parameters);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param $theme
     * @return array
     */
    private function includeTheme(string $view, array $parameters, Theme $theme): array
    {
        $isCompiled = $this->themeCompilerService->isCompiled($theme);

        if (!$isCompiled || getenv('APP_DEBUG')) {
            $this->themeCompilerService->compileTheme($theme);
        }

        $this->themeService->registerThemes();
        $themeViewPath = sprintf(
            '@Theme/%s%s',
            $this->themeConfigService->getThemeNamespace($theme),
            str_replace('@Theme', '/', $view)
        );

        $parameters['themeConfig'] = $theme->getConfiguration();
        $this->twig->addGlobal('themeConfig', $theme->getConfiguration());

        $parameters['theme']['config'] = $theme->getConfiguration();
        $parameters['theme']['active'] = $theme;
        $parameters['theme']['links'] = [];
        $links = $this->themeLinkService->getLinkConfigStructure($theme->getName());
        foreach ($links as $linkType => $data) {
            $parameters['theme']['links'][$linkType] = [];
        }

        foreach ($theme->getMenus() as $item) {
            /* @var $item ThemeMenu */
            $parameters['theme']['links']['menus'][$item->getName()] = $item->getMenu();
        }

        foreach ($theme->getPages() as $item) {
            /* @var $item ThemePage */
            $parameters['theme']['links']['pages'][$item->getName()] = $item->getPage();
        }

        foreach ($theme->getForms() as $item) {
            /* @var $item ThemeForm */
            $parameters['theme']['links']['forms'][$item->getName()] = $item->getForm();
        }

        foreach ($theme->getArtworks() as $item) {
            /* @var $item ThemeArtwork */
            $parameters['theme']['links']['artworks'][$item->getName()] = $item->getArtwork();
        }

        foreach ($theme->getArtGalleries() as $item) {
            /* @var $item ThemeArtGallery */
            $parameters['theme']['links']['artGalleries'][$item->getName()] = $item->getArtGallery();
        }

        foreach ($theme->getVideoGalleries() as $item) {
            /* @var $item ThemeVideoGallery */
            $parameters['theme']['links']['videoGalleries'][$item->getName()] = $item->getVideoGallery();
        }

        foreach ($theme->getGalleries() as $item) {
            /* @var $item ThemeGallery */
            $parameters['theme']['links']['galleries'][$item->getName()] = $item->getGallery();
        }

        foreach ($theme->getSegmentPages() as $item) {
            /* @var $item ThemeSegmentPage */
            $parameters['theme']['links']['segmentPages'][$item->getName()] = $item->getSegmentPage();
        }

        foreach ($theme->getFiles() as $item) {
            /* @var $item ThemeFile */
            $parameters['theme']['links']['files'][$item->getName()] = $item->getFile();
        }

        $parameters['theme']['path'] = $this->themeService->getThemeDirectory()
            . DIRECTORY_SEPARATOR
            . $theme->getName()
            . DIRECTORY_SEPARATOR;

        return [$themeViewPath, $parameters];
    }

    /**
     * Adds a global variable to template engine
     *
     * @param string $name
     * @param $value
     */
    public function addGlobal(string $name, $value): void
    {
        $this->twig->addGlobal($name, $value);
    }
}
