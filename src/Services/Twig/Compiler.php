<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:22
 */

namespace Jinya\Services\Twig;

use Jinya\Entity\Theme\Theme;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
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

    /** @var Environment */
    private $twig;

    /**
     * Compiler constructor.
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param Environment $twig
     */
    public function __construct(
        ConfigurationServiceInterface $configurationService,
        ThemeCompilerServiceInterface $themeCompilerService,
        ThemeConfigServiceInterface $themeConfigService,
        ThemeServiceInterface $themeService,
        Environment $twig
    ) {
        $this->configurationService = $configurationService;
        $this->themeCompilerService = $themeCompilerService;
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
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

        $parameters['theme']['active'] = $theme;
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
